<?php
require_once 'src/config/db.php';
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
    <?php include 'src/includes/header.php'; ?>
    
    <div class="container">
        <div class="welcome">
            <h2>Добро пожаловать в мастерскую по ремонту обуви!</h2>
            <p>Мы предлагаем качественный ремонт любой обуви по доступным ценам.</p>
        </div>
        
        <div class="grid-2">
            <div class="card">
                <h3>Наши услуги</h3>
                <ul>
                    <li>✓ Замена набоек</li>
                    <li>✓ Ремонт каблуков</li>
                    <li>✓ Профилактика подошвы</li>
                    <li>✓ Растяжка обуви</li>
                    <li>✓ Замена молний</li>
                    <li>✓ Ремонт замшевой обуви</li>
                </ul>
            </div>
            
            <div class="card">
                <h3>Почему выбирают нас</h3>
                <ul>
                    <li>🔧 Опытные мастера</li>
                    <li>⚡ Современное оборудование</li>
                    <li>⏱️ Быстрый ремонт</li>
                    <li>🏆 Гарантия качества</li>
                    <li>💰 Доступные цены</li>
                </ul>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="<?php echo $base_url; ?>/src/pages/register.php" class="btn btn-primary">Начать работу</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'src/includes/footer.php'; ?>
</body>
</html>