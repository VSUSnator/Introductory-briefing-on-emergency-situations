<?php
session_start();
// Здесь вы можете проверить состояние ознакомления для каждого блога
// Это пример, в реальной жизни данные должны быть получены из базы данных
$acknowledged = [
    'blog1' => isset($_SESSION['blog1_acknowledged']) && $_SESSION['blog1_acknowledged'],
    'blog2' => isset($_SESSION['blog2_acknowledged']) && $_SESSION['blog2_acknowledged'],
    'blog3' => isset($_SESSION['blog3_acknowledged']) && $_SESSION['blog3_acknowledged'],
];

echo json_encode($acknowledged);
?>