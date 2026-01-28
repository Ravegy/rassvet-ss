<?php
// НАСТРОЙКИ ПОДКЛЮЧЕНИЯ (Возьми их в панели управления хостингом -> Базы данных MySQL)

$host = 'sql213.infinityfree.com';      // Обычно остается localhost
$db   = 'if0_40985904_rassvet';   // ИМЯ твоей базы данных (которую ты создал в phpMyAdmin)
$user = 'if0_40985904';   // ИМЯ ПОЛЬЗОВАТЕЛЯ базы данных
$pass = 'GpAzqcvFlTzra8';    // ПАРОЛЬ от базы данных
$charset = 'utf8mb4';

// Дальше код трогать не нужно, это стандартная настройка
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opt);
} catch (\PDOException $e) {
    // Если подключиться не удалось, покажем ошибку
    die('Ошибка подключения к Базе Данных. Проверьте логин/пароль в файле includes/db.php');
}
?>