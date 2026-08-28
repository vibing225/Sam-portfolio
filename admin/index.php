<?php

require_once __DIR__ . '/../config/app.php';

if (adminIsLoggedIn()) {
    redirect('dashboard.php');
}

redirect('login.php');
