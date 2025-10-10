<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/database/config.php';

try {
    $result = testDbConnection();
    echo $result;
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
