<?php
require_once dirname(__DIR__) . '/config/database.php';
session_start();
session_destroy();
redirect(adminUrl('login.php'));
