<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name']);
    $phoneNumber = trim($_POST['phone_number']);
    $profession = trim($_POST['profession']); // Получаем профессию
    $action = $_POST['action'];

    // Очистка номера телефона
    $cleanedPhoneNumber = preg_replace('/\D/', '', $phoneNumber);

    // Путь к CSV-файлу
    $fileName = __DIR__ . '/new_data.csv';
    $existingData = readCSV($fileName);
    $userExists = false;
    $userRow = null;

    // Проверка существования пользователя
    foreach ($existingData as $row) {
        if (preg_replace('/\D/', '', $row[1]) === $cleanedPhoneNumber) {
            $userExists = true;
            $userRow = $row;
            break;
        }
    }

    // Материалы для каждой профессии
    $materials = [
        'врач' => ['Материал 1 для врача', 'Материал 2 для врача'],
        'медсестра' => ['Материал 1 для медсестры', 'Материал 2 для медсестры'],
        'администратор' => ['Материал 1 для администратора', 'Материал 2 для администратора'],
        'лаборант' => ['Материал 1 для лаборанта', 'Материал 2 для лаборанта'],
        'фельдшер' => ['Материал 1 для фельдшера', 'Материал 2 для фельдшера'],
        // Вы можете добавить другие профессии и их материалы здесь
    ];

    if ($action === 'register') {
        if ($userExists) {
            echo json_encode(['success' => false, 'message' => 'Пользователь с таким номером телефона уже зарегистрирован.']);
        } else {
            // Получаем материалы для выбранной профессии
            $selectedMaterials = isset($materials[$profession]) ? $materials[$profession] : ['Нет доступных материалов', 'Нет доступных материалов'];

            // Изменяем порядок элементов: ФИО, Номер телефона, Профессия, Дата регистрации, статусы, материалы
            $newRow = [$fullName, $phoneNumber, $profession, date('Y-m-d H:i:s'), 'не пройден', 'не пройден', 'не пройден', ...$selectedMaterials];
            $existingData[] = $newRow;
            writeDataToCSV($fileName, $existingData);
            $_SESSION['user'] = [
                'full_name' => $fullName,
                'phone_number' => $phoneNumber,
                'profession' => $profession, // Сохраняем профессию в сессии
                'completed_materials' => [false, false, false]
            ];
            echo json_encode(['success' => true, 'message' => 'Вы успешно зарегистрированы.', 'materials' => $selectedMaterials]);
        }
    } elseif ($action === 'login') {
        if ($userExists) {
            // Установка данных пользователя в сессию
            $_SESSION['user'] = [
                'full_name' => $userRow[0],
                'phone_number' => $userRow[1],
                'profession' => $userRow[2], // Получаем профессию из CSV
                'completed_materials' => array_slice($userRow, 3)
            ];
            echo json_encode(['success' => true, 'message' => 'Вы успешно вошли в систему.', 'materials' => array_slice($userRow, 3)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Пользователь с таким номером телефона не найден.']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка: неверный метод запроса.']);
}

// Функция для чтения данных из CSV
function readCSV($fileName) {
    $data = [];
    if (file_exists($fileName)) {
        $fileHandle = fopen($fileName, 'r');
        while (($row = fgetcsv($fileHandle)) !== false) {
            $data[] = $row;
        }
        fclose($fileHandle);
    }
    return $data;
}

// Функция для записи данных в CSV
function writeDataToCSV($fileName, $data) {
    $fileHandle = fopen($fileName, 'w');

    // Записываем заголовки, только если файл пустой
    if (count($data) === 1) { // Заголовки будут записаны только при первой записи
        fputcsv($fileHandle, ['ФИО', 'Номер телефона', 'Профессия', 'Дата регистрации', 'Гражданская оборона и ЧС', 'Антитеррористическая защищенность', 'Инструкция о порядке действия сотрудников при пожаре', 'Материал 1', 'Материал 2']);
    }

    foreach ($data as $row) {
        fputcsv($fileHandle, $row);
    }
    fclose($fileHandle);
}
?>