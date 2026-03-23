<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

// Только для авторизованных
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: events.php');
    exit();
}

$event_id = $_POST['event_id'] ?? 0;

try {
    // Проверяем существование мероприятия
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header('Location: events.php');
        exit();
    }
    
    // Проверяем, что это будущее мероприятие
    if (!$event['is_future']) {
        header('Location: events.php');
        exit();
    }
    
    // Проверяем, не записан ли уже
    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$_SESSION['user_id'], $event_id]);
    
    if ($stmt->fetch()) {
        header('Location: events.php');
        exit();
    }
    
    // Проверяем, есть ли места
    if ($event['max_participants'] > 0 && 
        ($event['current_participants'] ?? 0) >= $event['max_participants']) {
        header('Location: events.php');
        exit();
    }
    
    // Записываем
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("
        INSERT INTO registrations (user_id, event_id, status) 
        VALUES (?, ?, 'pending')
    ");
    $stmt->execute([$_SESSION['user_id'], $event_id]);
    
    // Увеличиваем счётчик участников
    $stmt = $pdo->prepare("
        UPDATE events 
        SET current_participants = COALESCE(current_participants, 0) + 1 
        WHERE id = ?
    ");
    $stmt->execute([$event_id]);
    
    $pdo->commit();
    
    // Уведомление об успехе
    session_start();
    $_SESSION['notification'] = ['type' => 'success', 'message' => 'Вы успешно записаны на мероприятие! Ожидайте подтверждения.'];
    
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Registration error: " . $e->getMessage());
    session_start();
    $_SESSION['notification'] = ['type' => 'error', 'message' => 'Ошибка при записи. Попробуйте позже.'];
}

header('Location: events.php');
exit();
?>