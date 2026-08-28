<?php

declare(strict_types=1);

/**
 * Robust Input and File Upload Schema Validator
 */
class Validator
{
    private array $data = [];
    private array $errors = [];
    private array $validated = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    /**
     * Validate data against an array of schema rules.
     * Example rules:
     * [
     *   'username' => 'required|string|min:3|max:64',
     *   'category' => 'required|in:Web,Mobile,Gestion,Autres',
     *   'limit'    => 'int|min_val:1|max_val:50',
     *   'url'      => 'nullable|url|max:2048',
     * ]
     */
    public function validate(array $schema): bool
    {
        $this->errors = [];
        $this->validated = [];

        foreach ($schema as $field => $rulesString) {
            $rules = is_array($rulesString) ? $rulesString : explode('|', $rulesString);
            $value = $this->data[$field] ?? null;
            $isNullable = in_array('nullable', $rules, true);
            $isRequired = in_array('required', $rules, true);

            // Handle empty value
            if ($value === null || $value === '') {
                if ($isRequired) {
                    $this->errors[$field] = "Le champ '{$field}' est obligatoire.";
                    continue;
                }
                if ($isNullable) {
                    $this->validated[$field] = null;
                    continue;
                }
                $this->validated[$field] = '';
                continue;
            }

            $currentVal = $value;
            $hasError = false;

            foreach ($rules as $rule) {
                if ($rule === 'required' || $rule === 'nullable') {
                    continue;
                }

                [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

                switch ($ruleName) {
                    case 'string':
                        if (!is_string($currentVal)) {
                            $this->errors[$field] = "Le champ '{$field}' doit être une chaîne de caractères.";
                            $hasError = true;
                        } else {
                            $currentVal = trim($currentVal);
                        }
                        break;

                    case 'int':
                        if (is_numeric($currentVal) && (string)(int)$currentVal === (string)$currentVal) {
                            $currentVal = (int) $currentVal;
                        } else {
                            $this->errors[$field] = "Le champ '{$field}' doit être un entier valide.";
                            $hasError = true;
                        }
                        break;

                    case 'bool':
                        $currentVal = filter_var($currentVal, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
                        break;

                    case 'min':
                        $min = (int) $param;
                        if (mb_strlen((string) $currentVal) < $min) {
                            $this->errors[$field] = "Le champ '{$field}' doit contenir au moins {$min} caractères.";
                            $hasError = true;
                        }
                        break;

                    case 'max':
                        $max = (int) $param;
                        if (mb_strlen((string) $currentVal) > $max) {
                            $this->errors[$field] = "Le champ '{$field}' ne peut pas dépasser {$max} caractères.";
                            $hasError = true;
                        }
                        break;

                    case 'min_val':
                        $minVal = (int) $param;
                        if ((int) $currentVal < $minVal) {
                            $this->errors[$field] = "Le champ '{$field}' doit être supérieur ou égal à {$minVal}.";
                            $hasError = true;
                        }
                        break;

                    case 'max_val':
                        $maxVal = (int) $param;
                        if ((int) $currentVal > $maxVal) {
                            $this->errors[$field] = "Le champ '{$field}' doit être inférieur ou égal à {$maxVal}.";
                            $hasError = true;
                        }
                        break;

                    case 'in':
                        $allowed = explode(',', (string) $param);
                        if (!in_array((string) $currentVal, $allowed, true)) {
                            $this->errors[$field] = "Valeur non autorisée pour le champ '{$field}'.";
                            $hasError = true;
                        }
                        break;

                    case 'url':
                        $url = trim((string) $currentVal);
                        if ($url !== '') {
                            $validatedUrl = filter_var($url, FILTER_VALIDATE_URL);
                            $scheme = parse_url($url, PHP_URL_SCHEME);
                            if ($validatedUrl === false || !in_array(strtolower((string) $scheme), ['http', 'https'], true)) {
                                $this->errors[$field] = "Le champ '{$field}' doit être une URL HTTP ou HTTPS valide.";
                                $hasError = true;
                            } else {
                                $currentVal = $validatedUrl;
                            }
                        } else {
                            $currentVal = null;
                        }
                        break;

                    case 'slug':
                        $slug = strtolower(trim((string) $currentVal));
                        $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
                        $slug = trim($slug, '-');
                        $currentVal = $slug;
                        break;
                }

                if ($hasError) {
                    break;
                }
            }

            if (!$hasError) {
                $this->validated[$field] = $currentVal;
            }
        }

        return empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        if (empty($this->errors)) {
            return null;
        }
        return reset($this->errors);
    }

    public function validated(): array
    {
        return $this->validated;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->validated[$key] ?? $default;
    }

    /**
     * Secure file upload validation and storage
     */
    public static function validateAndStoreFile(
        array $file,
        string $destinationSubdir,
        int $maxSizeBytes = 2097152,
        array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp']
    ): ?string {
        if (empty($file['name']) || !isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($errorCode !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Erreur lors de l'envoi du fichier (code: {$errorCode}).");
        }

        $fileSize = (int) ($file['size'] ?? 0);
        if ($fileSize <= 0 || $fileSize > $maxSizeBytes) {
            $maxMb = round($maxSizeBytes / (1024 * 1024), 1);
            throw new RuntimeException("Le fichier est trop volumineux (taille maximale : {$maxMb} Mo).");
        }

        // Verify genuine MIME type using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes, true)) {
            throw new RuntimeException("Format de fichier non autorisé. Formats acceptés : " . implode(', ', $allowedMimes));
        }

        // Map MIME to safe extension
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        ];
        $ext = $mimeMap[$mime] ?? 'bin';

        // Prepare destination directory securely
        $uploadsRoot = realpath(__DIR__ . '/../uploads');
        if ($uploadsRoot === false) {
            $uploadsRoot = __DIR__ . '/../uploads';
            if (!is_dir($uploadsRoot) && !mkdir($uploadsRoot, 0755, true) && !is_dir($uploadsRoot)) {
                throw new RuntimeException("Impossible d'accéder au dossier d'uploads.");
            }
            $uploadsRoot = realpath($uploadsRoot);
        }

        $cleanSubdir = trim(str_replace(['..', '\\', '/'], '/', $destinationSubdir), '/');
        $targetDir = $uploadsRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanSubdir);

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException("Le dossier de destination est inaccessible.");
        }

        // Generate unique, safe filename
        $safeBase = bin2hex(random_bytes(16));
        $finalFilename = time() . '_' . $safeBase . '.' . $ext;
        $finalPath = $targetDir . DIRECTORY_SEPARATOR . $finalFilename;

        if (!move_uploaded_file($file['tmp_name'], $finalPath)) {
            throw new RuntimeException("Le déplacement du fichier a échoué.");
        }

        return 'uploads/' . ($cleanSubdir !== '' ? $cleanSubdir . '/' : '') . $finalFilename;
    }

    /**
     * Safely delete a file ensuring it is within the uploads directory
     */
    public static function safeDeleteUploadedFile(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        $cleanRelative = ltrim(str_replace('\\', '/', $relativePath), '/');
        $uploadsRoot = realpath(__DIR__ . '/../uploads');
        if ($uploadsRoot === false) {
            return;
        }

        $fullPath = realpath(__DIR__ . '/../' . $cleanRelative);
        if ($fullPath !== false && str_starts_with($fullPath, $uploadsRoot . DIRECTORY_SEPARATOR)) {
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
    }
}
