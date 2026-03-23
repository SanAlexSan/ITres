<?php
require_once 'includes/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#join');
    exit();
}

$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$vk_link = trim($_POST['vk_link'] ?? '');

$errors = [];

if (empty($full_name)) $errors[] = 'Введите ФИО';
if (empty($phone)) $errors[] = 'Введите номер телефона';
if (empty($vk_link) || !filter_var($vk_link, FILTER_VALIDATE_URL)) {
    $errors[] = 'Введите корректную ссылку ВК';
}

if (!empty($errors)) {
    $_SESSION['join_error'] = implode('<br>', $errors);
    header('Location: index.php#join');
    exit();
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO join_requests (full_name, phone, vk_link) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$full_name, $phone, $vk_link]);
    
    $_SESSION['join_success'] = 'Спасибо за заявку на вступление! Скоро с вами свяжутся.';
    header('Location: index.php#join');
    exit();
    
} catch (PDOException $e) {
    error_log("Join request error: " . $e->getMessage());
    $_SESSION['join_error'] = 'Ошибка при отправке. Попробуйте позже.';
    header('Location: index.php#join');
    exit();
}
?>