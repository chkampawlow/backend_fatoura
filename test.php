<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/db.php';

$conn = db();

echo "DB connected successfully";