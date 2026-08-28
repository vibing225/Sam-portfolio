CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    category ENUM('Web', 'Mobile', 'Gestion', 'Autres') NOT NULL,
    short_description TEXT NOT NULL,
    description TEXT NOT NULL,
    technologies TEXT DEFAULT NULL,
    project_url VARCHAR(255) DEFAULT NULL,
    github_url VARCHAR(255) DEFAULT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS technologies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    logo_path VARCHAR(255) DEFAULT NULL,
    logo_url VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO technologies (name, slug, logo_url, status, sort_order)
VALUES
('HTML5', 'html5', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg', 'active', 1),
('CSS3', 'css3', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg', 'active', 2),
('JavaScript', 'javascript', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg', 'active', 3),
('Bootstrap', 'bootstrap', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg', 'active', 4),
('PHP', 'php', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg', 'active', 5),
('MySQL', 'mysql', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg', 'active', 6),
('Python', 'python', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg', 'active', 7),
('Django', 'django', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/django/django-plain.svg', 'active', 8),
('Laravel', 'laravel', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-original.svg', 'active', 9),
('Git', 'git', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg', 'active', 10),
('GitHub', 'github', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/github/github-original.svg', 'active', 11),
('VS Code', 'vscode', 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vscode/vscode-original.svg', 'active', 12)
ON DUPLICATE KEY UPDATE
    logo_url = VALUES(logo_url),
    status = VALUES(status),
    sort_order = VALUES(sort_order);

INSERT INTO projects (
    title,
    slug,
    category,
    short_description,
    description,
    technologies,
    project_url,
    github_url,
    image_path,
    status,
    featured
)
VALUES
(
    'Amogemio Website',
    'amogemio-website',
    'Web',
    'Site vitrine moderne pour une marque en croissance.',
    'Projet de site vitrine à venir — la description détaillée sera ajoutée depuis le panneau d\'administration une fois les informations complètes validées.',
    'HTML, CSS, JavaScript, PHP',
    '#',
    '#',
    NULL,
    'published',
    1
),
(
    'Brasserie Artemis',
    'brasserie-artemis',
    'Web',
    'Site institutionnel et événementiel pour une brasserie.',
    'Projet réel de site de brasserie — description courte à compléter selon la version finale du projet.',
    'HTML, CSS, JavaScript, Bootstrap',
    '#',
    '#',
    NULL,
    'published',
    1
),
(
    'SamiSpa',
    'samispa',
    'Web',
    'Système de réservation et gestion du parcours client.',
    'Application de réservation et de prise de rendez-vous à compléter avec les informations officielles du projet.',
    'PHP, MySQL, JavaScript, Bootstrap',
    '#',
    '#',
    NULL,
    'published',
    1
),
(
    'Digital Menus',
    'digital-menus',
    'Mobile',
    'Solution numérique pour menus de restaurants.',
    'Projet de menu digital pour restaurants — description détaillée à ajouter selon la version finale.',
    'PHP, MySQL, JavaScript, Responsive Design',
    '#',
    '#',
    NULL,
    'published',
    0
),
(
    'Gestion_Immo',
    'gestion-immo',
    'Gestion',
    'Plateforme de gestion immobilière développée en Django.',
    'Projet académique de gestion immobilière — détails à finaliser via le back-office administrateur du site.',
    'Python, Django, SQL, HTML, CSS',
    '#',
    '#',
    NULL,
    'published',
    0
)
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    category = VALUES(category),
    short_description = VALUES(short_description),
    description = VALUES(description),
    technologies = VALUES(technologies),
    project_url = VALUES(project_url),
    github_url = VALUES(github_url),
    image_path = VALUES(image_path),
    status = VALUES(status),
    featured = VALUES(featured);
