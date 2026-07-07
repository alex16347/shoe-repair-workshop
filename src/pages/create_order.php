<?php
require_once '../config/db.php';
$base_url = '/shoe-repair';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . '/src/pages/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_order'])) {
    $shoe_type = trim($_POST['shoe_type']);
    $description = trim($_POST['description']);
    $repair_date = $_POST['repair_date'];
    $estimated_cost = floatval($_POST['estimated_cost']);
    
    // Валидация
    if (empty($shoe_type) || empty($description) || empty($repair_date) || $estimated_cost <= 0) {
        $error = 'Заполните все поля корректно';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, shoe_type, description, repair_date, estimated_cost, status_id) 
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            
            if ($stmt->execute([$user_id, $shoe_type, $description, $repair_date, $estimated_cost])) {
                $success = '✅ Заказ успешно создан!';
                // Очищаем поля формы
                $_POST = [];
            } else {
                $error = '❌ Ошибка при создании заказа';
            }
        } catch (PDOException $e) {
            $error = '❌ Ошибка базы данных: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание заказа</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/mobile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="auth-form" style="max-width: 600px;">
            <h2>📝 Создание нового заказа</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="shoe_type">Тип обуви *</label>
                    <select id="shoe_type" name="shoe_type" required>
                        <option value="">-- Выберите тип --</option>
                        <option value="Ботинки">👢 Ботинки</option>
                        <option value="Туфли">👞 Туфли</option>
                        <option value="Кроссовки">👟 Кроссовки</option>
                        <option value="Сапоги">🥾 Сапоги</option>
                        <option value="Сандалии">👡 Сандалии</option>
                        <option value="Другое">Другое</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание работы *</label>
                    <textarea id="description" name="description" rows="4" required placeholder="Например: замена набоек, ремонт каблука..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="repair_date">Желаемая дата выполнения *</label>
                    <input type="date" id="repair_date" name="repair_date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="estimated_cost">Примерная стоимость (₽) *</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" min="100" step="100" required placeholder="500">
                </div>
                
                <button type="submit" name="create_order" class="btn btn-primary" style="width: 100%;">
                    ✅ Создать заказ
                </button>
                
                <div style="text-align: center; margin-top: 15px;">
                    <a href="<?php echo $base_url; ?>/src/pages/dashboard.php" style="color: #2A5C82;">← Вернуться в личный кабинет</a>
                </div>
            </form>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>