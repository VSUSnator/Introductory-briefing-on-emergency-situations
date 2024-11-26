<?php
session_start(); // Запуск сессии
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, авторизован ли пользователь
    if (isset($_SESSION['user'])) {
        $phoneNumber = $_SESSION['user']['phone_number'];
        
        $fileName = __DIR__ . '/new_data.csv';
        $existingData = [];
        $userUpdated = false;

        // Проверяем, существует ли файл
        $fileExists = file_exists($fileName);

        // Читаем существующие данные
        if ($fileExists) {
            $fileHandle = fopen($fileName, 'r');
            while (($row = fgetcsv($fileHandle)) !== false) {
                $existingData[] = $row;
            }
            fclose($fileHandle);
        }

        // Определяем структуру материалов
        $materials = [
            'main' => [
                'material1' => 'Гражданская оборона и ЧС',
                'material2' => 'Антитеррористическая защищенность',
                'material3' => 'Инструкция о порядке действия сотрудников при пожаре'
            ],
            // В будущем можно добавить дополнительные материалы
            'additional' => [
                 'material4' => 'Дополнительный материал 1',
                 'material5' => 'Дополнительный материал 2'
            ]
        ];

        // Обновляем статус для текущего пользователя
        foreach ($existingData as $index => $row) {
            if ($row[1] === $phoneNumber) {
                // Обновляем статус для каждого основного материала
                foreach ($materials['main'] as $key => $title) {
                    $rowKey = array_search($title, $materials['main']); // Находим индекс материала
                    if ($rowKey !== false) {
                        $row[$rowKey + 3] = 'пройден'; // Обновляем статус материала
                    }
                }
                $existingData[$index] = $row; // Обновляем строку пользователя
                $userUpdated = true;
                break;
            }
        }

        // Записываем обновленные данные обратно в файл
        if ($fileExists) {
            $fileHandle = fopen($fileName, 'w');
        } else {
            // Если файла нет, создаем его и записываем заголовки
            $fileHandle = fopen($fileName, 'w');
            fputcsv($fileHandle, ['ФИО', 'Номер телефона', 'Дата регистрации', 'Гражданская оборона и ЧС', 'Антитеррористическая защищенность', 'Инструкция о порядке действия сотрудников при пожаре']);
        }

        // Если пользователь был обновлен, записываем данные
        if ($userUpdated) {
            foreach ($existingData as $row) {
                fputcsv($fileHandle, $row);
            }
            fclose($fileHandle);
            echo json_encode(['message' => 'Вы успешно прошли материал.']);
        } else {
            echo json_encode(['message' => 'Пользователь не найден.']);
        }
    } else {
        echo json_encode(['message' => 'Ошибка: пользователь не авторизован.']);
    }
} else {
    echo json_encode(['message' => 'Ошибка: неверный метод запроса.']);
}