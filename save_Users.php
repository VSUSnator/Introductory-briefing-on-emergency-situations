<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Настройка пути к файлу
$fileName = __DIR__ . '/data.csv'; // Используем __DIR__ для получения абсолютного пути к файлу

// Проверка, что данные были отправлены через POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = trim($_POST['full_name']);
    $phoneNumber = trim($_POST['phone_number']);

    // Валидация данных
    if (empty($fullName) || empty($phoneNumber)) {
        echo json_encode(['message' => 'Ошибка: ФИО и номер телефона не могут быть пустыми.']);
        exit;
    }

    // Валидация ФИО
    if (!preg_match("/^[\p{Cyrillic}]+( [\p{Cyrillic}]+)*$/u", $fullName)) {
        echo json_encode(['message' => 'Ошибка: ФИО должно содержать только буквы кириллицы и пробелы между словами.']);
        exit;
    }

    // Валидация номера телефона
    if (!preg_match("/^\+7\d{10}$/", $phoneNumber)) {
        echo json_encode(['message' => 'Ошибка: Номер телефона должен быть в формате +7XXXXXXXXXX.']);
        exit;
    }

    try {
        // Открываем файл для добавления данных
        $fileHandle = fopen($fileName, 'a');
        if ($fileHandle === false) {
            throw new Exception('Не удалось открыть файл для записи.');
        }

        // Записываем данные в CSV
        fputcsv($fileHandle, [$fullName, $phoneNumber]);

        // Закрываем файл
        fclose($fileHandle);

        // Возвращаем ответ
        echo json_encode(['message' => 'Данные успешно сохранены.', 'redirect' => 'progress.html']);
    } catch (Exception $e) {
        echo json_encode(['message' => 'Ошибка: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['message' => 'Ошибка: данные не были отправлены.']);
}