<?php
session_start();

// Добавляем заголовки для предотвращения кэширования
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$fileName = __DIR__ . '/new_data.csv';
$userPhoneNumber = $_SESSION['user']['phone_number'] ?? null;

$materialsByProfession = [
    'врач' => [
        'material1.html' => 'Материал для врача',
        'material2.html' => 'Дополнительный материал для врача',
    ],
    'медсестра' => [
        'material3.html' => 'Материал для медсестры',
        'material4.html' => 'Дополнительный материал для медсестры',
    ],
    'администратор' => [
        'material5.html' => 'Материал для администратора',
        'material6.html' => 'Дополнительный материал для администратора',
    ],
    'лаборант' => [
        'material7.html' => 'Материал для лаборанта',
        'material8.html' => 'Дополнительный материал для лаборанта',
    ],
    'фельдшер' => [
        'material9.html' => 'Материал для фельдшера',
        'material10.html' => 'Дополнительный материал для фельдшера',
    ],
];

// Функция для обновления статуса материала в CSV
function updateCsv($fileName, $phoneNumber, $materialFile) {
    $data = [];
    if (($handle = fopen($fileName, 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            if ($row[1] === $phoneNumber) {
                $row[7] = (isset($row[7]) ? $row[7] . ',' : '') . $materialFile; // Добавляем материал в завершенные
                $row[7] = 'пройден'; // Обновляем статус
            }
            $data[] = $row;
        }
        fclose($handle);
    }

    // Записываем обновленные данные обратно в CSV
    if (($handle = fopen($fileName, 'w')) !== false) {
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}

// Обработка нажатия кнопки "Ознакомлен"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete'])) {
    updateCsv($fileName, $userPhoneNumber, 'material1.html'); // Укажите файл материала
    header('Location: profile.php'); // Перенаправление на профиль
    exit;
}

// Чтение данных из CSV
function readCsv($fileName) {
    if (!file_exists($fileName) || !is_readable($fileName)) {
        return [];
    }
    
    $data = [];
    if (($handle = fopen($fileName, 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 8) { // Обновлено для учета 8 столбца
                $data[] = $row;
            }
        }
        fclose($handle);
    }
    return $data;
}

if ($userPhoneNumber) {
    $existingData = readCsv($fileName);
    foreach ($existingData as $row) {
        if ($row[1] === $userPhoneNumber) {
            $_SESSION['user'] = [
                'full_name' => $row[0],
                'phone_number' => $row[1],
                'profession' => $row[2],
                'completed_materials' => [
                    'additional' => []
                ]
            ];

            // Сохраняем завершенные дополнительные материалы
            if (isset($row[6])) {
                foreach ($materialsByProfession[$row[2]] as $file => $title) {
                    $_SESSION['user']['completed_materials']['additional'][$file] = (strpos($row[6], $file) !== false);
                }
            }

            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Дополнительный материал</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        #menu {
            background: #007bff;
            padding: 10px;
            color: white;
        }
        #menu .menu ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: space-around;
        }
        #menu .menu ul li {
            display: inline;
        }
        #menu .menu ul li a {
            color: white;
            text-decoration: none;
            padding: 10px;
        }
        #wrapper {
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        #materialContent {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        #completeMaterialButton {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        #completeMaterialButton:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div id="menu">
    <div class="menu">
        <ul>
            <li><a href="registrationForm.html">Главная</a></li>
            <li><a href="blog.html">Инструктаж</a></li>
            <li><a href="fullwidth.html">О нас</a></li>
            <li><a href="profile.php" class="active">Мой профиль</a></li>
            <li><a href="index.html">Регистрация</a></li>
        </ul>
    </div>
</div>

<div id="wrapper">
    <section class="intro-instructions">
        <h2>Профиль пользователя</h2>
        <div id="userInfo">
            <?php if (isset($_SESSION['user'])): ?>
                <p><strong>ФИО:</strong> <?php echo htmlspecialchars($_SESSION['user']['full_name']); ?></p>
                <p><strong>Телефон:</strong> <?php echo htmlspecialchars($_SESSION['user']['phone_number']); ?></p>
                <p><strong>Профессия:</strong> <?php echo htmlspecialchars($_SESSION['user']['profession']); ?></p>
                <form id="logoutForm" action="logout.php" method="post">
                    <button type="submit" id="logoutButton">Выход из профиля пользователя</button>
                </form>
            <?php else: ?>
                <p>Вы не зарегистрированы. Пожалуйста, <a href="index.html">зарегистрируйтесь</a>, чтобы получить доступ к вашему профилю.</p>
            <?php endif; ?>
        </div>

        <h3>Специальные материалы для профессии:</h3>
        <div id="materialList">
            <?php if (isset($_SESSION['user'])): ?>
                <?php
                $userProfession = $_SESSION['user']['profession'];
                if (isset($materialsByProfession[$userProfession])) {
                    foreach ($materialsByProfession[$userProfession] as $materialFile => $materialTitle) {
                        $isCompleted = $_SESSION['user']['completed_materials']['additional'][$materialFile];
                        echo '<p>' . htmlspecialchars($materialTitle) . ': <strong>' . ($isCompleted ? 'Я ознакомлен' : 'Не пройдено') . '</strong></p>';
                        if (!$isCompleted): ?>
                            <form method="post">
                                <input type="hidden" name="complete" value="1">
                                <button type="submit">Ознакомлен</button>
                            </form>
                        <?php endif;
                    }
                }
                ?>
            <?php else: ?>
                <p>Материалы доступны только зарегистрированным пользователям.</p>
            <?php endif; ?>
        </div>
    </section>
</div>
</body>
</html>