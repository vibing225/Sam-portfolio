<?php
require_once __DIR__ . '/config/app.php';

if (!setupIsComplete()) {
    header('Location: setup.php');
    exit;
}

require __DIR__ . '/index.html';
