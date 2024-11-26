<?php
session_start(); // Начинаем сессию

// Проверяем, существует ли сессия
if (isset($_SESSION['user'])) {
    // Уничтожаем сессию
    session_destroy(); 
}

// Перенаправляем на страницу регистрации
header('Location: index.html'); 
exit; // Завершаем выполнение скрипта
?>