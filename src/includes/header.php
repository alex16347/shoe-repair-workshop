<?php
$base_url = '/shoe-repair';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мастерская по ремонту обуви</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/mobile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <h1>👞 Мастерская обуви</h1>
            </div>
            <nav>
                <ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $base_url; ?>/src/pages/dashboard.php">📋 Личный кабинет</a></li>
                        <?php if ($_SESSION['user_role'] == 'admin'): ?>
                            <li><a href="<?php echo $base_url; ?>/src/pages/admin.php" style="background: #e67e22; border-radius: 4px;">👑 Админ-панель</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $base_url; ?>/src/pages/logout.php">🚪 Выйти</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_url; ?>/">🏠 Главная</a></li>
                        <li><a href="<?php echo $base_url; ?>/src/pages/login.php">🔑 Вход</a></li>
                        <li><a href="<?php echo $base_url; ?>/src/pages/register.php">📝 Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>