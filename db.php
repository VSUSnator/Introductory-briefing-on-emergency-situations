<?php
// Настройки подключения к базе данных
$host = 'localhost'; 
$db = 'my_database'; 
$user = 'username'; 
$pass = 'password'; 

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Проверка, был ли получен POST-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];

    // Пример SQL-запроса для добавления данных в таблицу contacts
    $stmt = $mysqli->prepare("INSERT INTO contacts (full_name, phone_number) VALUES (?, ?)");
    $stmt->bind_param("ss", $full_name, $phone_number);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Данные успешно отправлены.']);
    } else {
        echo json_encode(['message' => 'Ошибка при отправке данных.']);
    }

    $stmt->close();
}

$mysqli->close();
?>