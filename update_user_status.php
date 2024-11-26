<?php
session_start(); // Запуск сессии
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, авторизован ли пользователь
    if (isset($_SESSION['user'])) {
        $phoneNumber = $_SESSION['user']['phone_number'];
        $materialIndex = $_POST['material_index']; // Индекс материала (0, 1, 2, 3 или 4)

        $fileName = __DIR__ . '/new_data.csv';
        $existingData = [];
        $userUpdated = false;

        // Читаем существующие данные
        if (file_exists($fileName)) {
            $fileHandle = fopen($fileName, 'r');
            while (($row = fgetcsv($fileHandle)) !== false) {
                $existingData[] = $row;
            }
            fclose($fileHandle);
        }

        // Обновляем статус для текущего пользователя
        foreach ($existingData as $index => $row) {
            if ($row[1] === $phoneNumber) {
                // Обновляем статус только для текущего материала
                $row[$materialIndex + 4] = 'пройден'; // +4, чтобы учесть первые четыре колонки
                $existingData[$index] = $row; // Обновляем строку в массиве
                $userUpdated = true;
                break;
            }
        }

        // Записываем обновленные данные обратно в файл
        if ($userUpdated) {
            $fileHandle = fopen($fileName, 'w');

            // Проверяем, нужно ли записывать заголовки
            if (count($existingData) === 0 || !isset($existingData[0][0]) || empty($existingData[0][0])) {
                $header = ['ФИО', 'Номер телефона', 'Профессия', 'Дата регистрации', 
                           'Гражданская оборона и ЧС', 
                           'Антитеррористическая защищенность', 
                           'Инструкция о порядке действия сотрудников при пожаре', 
                           'Материал 1', 'Материал 2', 'Материал 3', 
                           'Материал 4', 'Материал 5']; // Добавлены новые материалы
                fputcsv($fileHandle, $header); // Записываем заголовки
            }
            
            foreach ($existingData as $row) {
                fputcsv($fileHandle, $row);
            }
            fclose($fileHandle);

            echo json_encode(['message' => 'Статус материала успешно обновлен.']);
        } else {
            echo json_encode(['message' => 'Пользователь не найден.']);
        }
    } else {
        echo json_encode(['message' => 'Ошибка: пользователь не авторизован.']);
    }
} else {
    echo json_encode(['message' => 'Ошибка: неверный метод запроса.']);
}
?>