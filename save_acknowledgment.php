<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blogId'])) {
    $blogId = $_POST['blogId'];

    // Сохраняем состояние ознакомления в сессии
    $_SESSION[$blogId . '_acknowledged'] = true; // Например, 'blog1_acknowledged'

    // Возвращаем ответ
    echo json_encode(['status' => 'success', 'message' => 'Вы успешно ознакомились с ' . $blogId]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Некорректный запрос']);
}
?>