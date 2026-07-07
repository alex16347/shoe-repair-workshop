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
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/slider.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/animations.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="page-fade-in">
    <?php include 'src/includes/header.php'; ?>
    
    <!-- ======================== -->
    <!-- СЛАЙДЕР -->
    <!-- ======================== -->
    <div class="slider-container" role="region" aria-label="Примеры наших работ">
        <div class="slider-wrapper">
            <!-- Слайд 1 -->
            <div class="slider-slide active">
                <img src="<?php echo $base_url; ?>/src/images/slider/slide1.jpg" 
                     alt="Ремонт мужской обуви"
                     loading="eager">
                <div class="slide-content">
                    <h3>Профессиональный ремонт</h3>
                    <p>Любые виды обуви — от классики до кроссовок</p>
                </div>
            </div>
            
            <!-- Слайд 2 -->
            <div class="slider-slide">
                <img src="<?php echo $base_url; ?>/src/images/slider/slide2.jpg" 
                     alt="Реставрация женской обуви"
                     loading="lazy">
                <div class="slide-content">
                    <h3>Реставрация и обновление</h3>
                    <p>Вернём вторую жизнь вашей любимой паре</p>
                </div>
            </div>
            
            <!-- Слайд 3 -->
            <div class="slider-slide">
                <img src="<?php echo $base_url; ?>/src/images/slider/slide3.jpg" 
                     alt="Замена набоек и подошвы"
                     loading="lazy">
                <div class="slide-content">
                    <h3>Качественные материалы</h3>
                    <p>Используем только проверенные комплектующие</p>
                </div>
            </div>
            
            <!-- Слайд 4 -->
            <div class="slider-slide">
                <img src="<?php echo $base_url; ?>/src/images/slider/slide4.jpg" 
                     alt="Ремонт спортивной обуви"
                     loading="lazy">
                <div class="slide-content">
                    <h3>Быстро и надёжно</h3>
                    <p>Средний срок ремонта — всего 1-2 дня</p>
                </div>
            </div>
        </div>
        
        <button class="slider-btn slider-prev" aria-label="Предыдущий слайд">‹</button>
        <button class="slider-btn slider-next" aria-label="Следующий слайд">›</button>
        <div class="slider-dots" role="tablist"></div>
    </div>
    
    <!-- ======================== -->
    <!-- ОСНОВНОЙ КОНТЕНТ -->
    <!-- ======================== -->
    <div class="container">
        <div class="welcome animate-on-scroll">
            <h2>👞 Добро пожаловать в мастерскую по ремонту обуви!</h2>
            <p>Мы предлагаем качественный ремонт любой обуви по доступным ценам.</p>
        </div>
        
        <div class="grid-2">
            <div class="card animate-on-scroll" style="transition-delay: 0.1s;">
                <h3>📋 Наши услуги</h3>
                <ul>
                    <li>✅ Замена набоек</li>
                    <li>✅ Ремонт каблуков</li>
                    <li>✅ Профилактика подошвы</li>
                    <li>✅ Растяжка обуви</li>
                    <li>✅ Замена молний</li>
                    <li>✅ Ремонт замшевой обуви</li>
                </ul>
            </div>
            
            <div class="card animate-on-scroll" style="transition-delay: 0.2s;">
                <h3>⭐ Почему выбирают нас</h3>
                <ul>
                    <li>🔧 Опытные мастера</li>
                    <li>⚡ Современное оборудование</li>
                    <li>⏱️ Быстрый ремонт</li>
                    <li>🏆 Гарантия качества</li>
                    <li>💰 Доступные цены</li>
                </ul>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="<?php echo $base_url; ?>/src/pages/register.php" class="btn btn-primary btn-pulse">Начать работу</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?php echo $base_url; ?>/src/js/slider.js"></script>
    <script src="<?php echo $base_url; ?>/src/js/animations.js"></script>
    <?php include 'src/includes/footer.php'; ?>
</body>
</html>