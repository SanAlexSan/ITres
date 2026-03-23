<?php
// Файл для управления сессией и проверки авторизации

// Запускаем сессию, если она ещё не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Функция для проверки, авторизован ли пользователь
 * @return bool true если пользователь авторизован
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Функция для получения данных текущего пользователя
 * @return array|null массив с данными пользователя или null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}

/**
 * Функция для проверки роли администратора
 * @return bool true если пользователь администратор
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

/**
 * Функция для редиректа, если пользователь не авторизован
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.html');
        exit();
    }
}

/**
 * Функция для редиректа, если пользователь не администратор
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        // Можно редиректнуть на главную или показать ошибку
        header('Location: /index.html');
        exit();
    }
}
?>