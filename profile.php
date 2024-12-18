<?php
session_start();

// Добавляем заголовки для предотвращения кэширования
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$fileName = __DIR__ . '/new_data.csv';
$userPhoneNumber = $_SESSION['user']['phone_number'] ?? null;

// Определяем общие материалы для всех профессий
$commonMaterials = [
    'common_material1.html' => 'Общий материал 1',
    'common_material2.html' => 'Общий материал 2',
    'common_material3.html' => 'Общий материал 3',
    'common_material4.html' => 'Общий материал 4',
    'common_material5.html' => 'Общий материал 5',
];

$additionalMaterials = [
    'врач' => [
        "Дополнительный материал врач",
        "Дополнительный материал врач 2",
    ],
    'медсестра' => [
        "Дополнительный материал медсестра",
        "Дополнительный материал медсестра 2",
    ],
    'администратор' => [
        "Дополнительный материал администратор",
        "Дополнительный материал администратор 2",
    ],
    'лаборант' => [
        "Дополнительный материал лаборант",
        "Дополнительный материал лаборант 2",
    ],
    'фельдшер' => [
        "Дополнительный материал фельдшер",
        "Дополнительный материал фельдшер 2",
    ],
];
$additionalMaterialsLinks = [
    'врач' => [
        "blog4.html",
        "blog5.html",
    ],
    'медсестра' => [
        "blog4.html",
        "blog5.html",
    ],
    'администратор' => [
        "blog4.html",
        "blog5.html",
    ],
    'лаборант' => [
        "blog4.html",
        "blog5.html",
    ],
    'фельдшер' => [
        "blog4.html",
        "blog5.html",
    ],
];

function readCsv($fileName) {
    if (!file_exists($fileName) || !is_readable($fileName)) {
        return [];
    }
    
    $data = [];
    if (($handle = fopen($fileName, 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 7) {
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
                    'main' => [
                        'material1' => isset($row[4]) && $row[4] === 'пройден',
                        'material2' => isset($row[5]) && $row[5] === 'пройден',
                        'material3' => isset($row[6]) && $row[6] === 'пройден',
                        'material4' => isset($row[7]) && $row[7] === 'пройден',
                        'material5' => isset($row[8]) && $row[8] === 'пройден'
                    ],
                ]
            ];
            break; // Выходим из цикла, если нашли пользователя
        }
    }
}
?>
    
<head>
    <meta charset="UTF-8">
    <title>Регистрация и отслеживание прогресса</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css" media="all" />
    <style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f7f9fc; /* Светлый серо-голубой фон */
    }
    .logo {
        text-align: center; /* Центрируем логотип */
        margin: 20px 0; /* Отступы сверху и снизу */
    }
    .logo img {
        max-width: 100%; /* Делает изображение адаптивным */
        height: auto; /* Сохраняет пропорции изображения */
    }
    #menu {
        background: #5e35b1; /* Фиолетовый фон для меню */
        box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.3);
        margin-bottom: 20px; /* Отступ снизу для меню */
    }
    .menu ul {
        list-style-type: none; /* Убираем маркеры списка */
        padding: 0; /* Убираем отступы */
        margin: 0; /* Убираем отступы */
        text-align: center; /* Центрируем текст меню */
    }
    .menu ul li {
        display: inline; /* Выравнивание элементов в строку */
        margin: 0 0px; /* Отступы между элементами меню */
    }
    .menu ul li a {
        color: white; /* Цвет текста в меню */
        text-decoration: none; /* Убираем подчеркивание */
        transition: color 0.3s; /* Плавный переход цвета */
    }
    .menu ul li a:hover {
        color: #ab47bc; /* Цвет текста при наведении */
    }
    #wrapper {
        background-color: white; /* Белый фон для обертки */
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Меньшая тень для обертки */
        margin-top: 20px;
    }
    .intro-instructions h2 {
        color: #5e35b1; /* Более мягкий фиолетовый цвет */
        margin-bottom: 15px;
        font-size: 24px;
        border-bottom: 2px solid #ab47bc; /* Светлый фиолетовый подчеркиватель */
        padding-bottom: 10px;
    }
    #userInfo {
        margin-bottom: 20px;
    }
    #materialList {
        margin-top: 20px;
    }
    .material-link {
        color: #3B0FFF; /* Цвет текста ссылки */
        text-decoration: none; /* Убираем подчеркивание */
        transition: color 0.3s; /* Плавный переход цвета */
    }
    .material-link:hover {
        color: #ab47bc; /* Цвет текста при наведении */
        text-decoration: underline; /* Подчеркивание при наведении */
    }
    #materialList p {
        font-size: 16px;
        margin: 10px 0;
        padding: 10px;
        border: 1px solid #d1d1d1; /* Светлая рамка */
        border-radius: 5px;
        background-color: #f4f4f4; /* Светло-серый фон для материалов */
        transition: background-color 0.3s, color 0.3s;
    }
    #materialList p:hover {
        background-color: #e0e0e0; /* Более темный светло-серый фон при наведении */
        color: #333; /* Темный текст при наведении */
    }
    button {
        background-color: #45a049; /* Яркий оранжевый цвет для кнопок */
        color: white;
        border: none;
        padding: 15px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        transition: background-color 0.3s, transform 0.2s, box-shadow 0.3s; /* Добавлен переход для тени */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Тень для кнопок */
    }
    button:hover {
        background-color: #388e3c; /* Темнее оранжевый при наведении */
        transform: translateY(-2px); /* Поднятие кнопки при наведении */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* Увеличение тени при наведении */
    }
    #message {
        margin: 20px 0;
        padding: 10px;
        border-radius: 5px;
        background-color: #e9ecef; /* Светлый фон для сообщений */
        font-size: 16px;
        color: #3B0FFF; /* Синий текст для сообщений */
    }
    #errorMessage {
        color: red;
        font-weight: bold;
        padding: 10px;
        border: 1px solid red;
        border-radius: 5px;
        background-color: #f8d7da; /* Светлый фон для сообщений об ошибках */
    }
    #materialRedirectButton {
        background-color: #007bff; /* Основной фиолетовый цвет для кнопки перенаправления */
        margin-top: 20px; /* Отступ сверху для кнопки перенаправления */
    }
    #materialRedirectButton:hover {
        background-color: #0056b3; /* Темный фиолетовый при наведении */
    }
    .special-materials {
        border: 2px solid #007BFF; /* Синяя рамка */
        background-color: #E7F3FF; /* Светло-синий фон */
        padding: 15px;
        margin-top: 20px;
        border-radius: 5px;
    }
    .special-materials h4 {
        color: #0056b3; /* Темно-синий цвет заголовка */
    }
    .special-materials a {
        font-weight: bold; /* Выделение ссылок */
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
                <p><strong>ФИО:</strong> <span id="userFullName"><?php echo htmlspecialchars($_SESSION['user']['full_name']); ?></span></p>
                <p><strong>Телефон:</strong> <span id="userPhoneNumber"><?php echo htmlspecialchars($_SESSION['user']['phone_number']); ?></span></p>
                <p><strong>Профессия:</strong> <span id="userProfession"><?php echo htmlspecialchars($_SESSION['user']['profession']); ?></span></p>
                <form id="logoutForm" action="logout.php" method="post">
                    <button type="submit" id="logoutButton">Выход из профиля пользователя</button>
                </form>
            <?php else: ?>
                <p>Вы не зарегистрированы. Пожалуйста, <a href="index.html">зарегистрируйтесь</a>, чтобы получить доступ к вашему профилю.</p>
            <?php endif; ?>
        </div>
        <h3>Материалы для прохождения</h3>
        <div id="materialList">
            <?php if (isset($_SESSION['user'])): ?>
                <?php
                // Общие материалы
                $mainMaterials = [
                    "Гражданская оборона и ЧС",
                    "Антитеррористическая защищенность",
                    "Порядок действия сотрудников при пожаре"
                ];

                // Выводим основные материалы
                foreach ($mainMaterials as $index => $title): ?>
                    <p>
                        <a href="blog<?php echo $index + 1; ?>.html" class="material-link"><?php echo $title; ?>: <strong><?php echo $_SESSION['user']['completed_materials']['main']['material' . ($index + 1)] ? 'Пройдено' : 'Не пройдено'; ?></strong></a>
                    </p>
                <?php endforeach; ?>

                <?php
                // Дополнительные материалы
                $userProfession = $_SESSION['user']['profession'];
                $userAdditionalMaterials = $additionalMaterials[$userProfession] ?? []; // Получаем материалы для данной профессии
                $userAdditionalMaterialsLinks = $additionalMaterialsLinks[$userProfession] ?? []; // Получаем ссылки для данной профессии

                // Выводим дополнительные материалы
                if (!empty($userAdditionalMaterials)) {
                    echo '<h3>Дополнительные материалы</h3>';
                    foreach ($userAdditionalMaterials as $index => $title): 
                        $isCompleted = $_SESSION['user']['completed_materials']['main']['material' . (count($mainMaterials) + $index + 1)];
                        ?>
                        <p class="<?php echo !$isCompleted ? 'not-completed' : ''; ?>">
                            <a href="<?php echo htmlspecialchars($userAdditionalMaterialsLinks[$index]); ?>" class="material-link">
                                <?php echo htmlspecialchars($title); ?>: 
                                <strong><?php echo $isCompleted ? 'Пройдено' : 'Не пройдено'; ?></strong>
                            </a>
                        </p>
                    <?php endforeach; 
                }
                ?>
            <?php else: ?>
                <p>Материалы доступны только зарегистрированным пользователям.</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user'])): ?>
            <!-- Кнопка "Начать обучение" -->
            <div>
                <a href="registrationForm.html" class="button">Начать обучение</a>
            </div>
        <?php endif; ?>
        </div>
        <button id="nextButton" style="display:none;" aria-label="Перейти на следующий сайт">Перейти на следующий сайт</button>
    </section>
</div>

<script>
    $(document).ready(function () {
        const materials = [
            { title: "Гражданская оборона и ЧС", completed: <?php echo json_encode($_SESSION['user']['completed_materials']['main']['material1']); ?> },
            { title: "Антитеррористическая защищенность", completed: <?php echo json_encode($_SESSION['user']['completed_materials']['main']['material2']); ?> },
            { title: "Порядок действия сотрудников при пожаре", completed: <?php echo json_encode($_SESSION['user']['completed_materials']['main']['material3']); ?> },
            { title: "Материал", completed: <?php echo json_encode($_SESSION['user']['completed_materials']['main']['material4']); ?> },
            { title: "Материал 2", completed: <?php echo json_encode($_SESSION['user']['completed_materials']['main']['material5']); ?> }
        ];

        // Проверка завершенности всех материалов
        if (materials.every(material => material.completed)) {
            $('#message').text('Вы завершили ознакомление со всеми материалами!').show().delay(5000).fadeOut();
        }
    });
</script>
</body>
</html>

<!-- <body>
<h3>Дополнительные материалы</h3>
<div class="special-materials">
    <?php foreach ($userAdditionalMaterials as $index => $title): 
        $isCompleted = $_SESSION['user']['completed_materials']['main']['material' . (count($mainMaterials) + $index + 1)];
        ?>
        <p class="<?php echo !$isCompleted ? 'not-completed' : ''; ?>">
            <a href="<?php echo htmlspecialchars($userAdditionalMaterialsLinks[$index]); ?>" class="material-link">
                <?php echo htmlspecialchars($title); ?>: 
                <strong><?php echo $isCompleted ? 'Пройдено' : 'Не пройдено'; ?></strong>
            </a>
        </p>
    <?php endforeach; ?>
</div>
</body> -->