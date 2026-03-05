<?php
require_once '../config/db.php';
$base_url = '/shoe-repair';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . '/src/pages/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/mobile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="welcome">
            <h2>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
        </div>
        
        <div class="grid-2">
            <div class="card">
                <h3>Создать новый заказ</h3>
                <form method="POST" action="create_order.php">
                    <div class="form-group">
                        <label>Тип обуви</label>
                        <select name="shoe_type" required>
                            <option value="">Выберите</option>
                            <option value="Ботинки">Ботинки</option>
                            <option value="Туфли">Туфли</option>
                            <option value="Кроссовки">Кроссовки</option>
                            <option value="Сапоги">Сапоги</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Описание работы</label>
                        <textarea name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Желаемая дата</label>
                        <input type="date" name="repair_date" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Примерная стоимость (₽)</label>
                        <input type="number" name="estimated_cost" min="0" step="100" required>
                    </div>
                    
                    <button type="submit" name="create_order" class="btn btn-primary">Создать заказ</button>
                </form>
            </div>
            
            <div class="card">
                <h3>Мои заказы</h3>
                <?php if (empty($orders)): ?>
                    <p>У вас пока нет заказов</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
                            <p><strong>Заказ #<?php echo $order['id']; ?></strong></p>
                            <p>Тип: <?php echo $order['shoe_type']; ?></p>
                            <p>Статус: <?php echo $order['status_id'] == 1 ? 'Новый' : 'В обработке'; ?></p>
                            <p>Стоимость: <?php echo number_format($order['estimated_cost'], 2); ?> ₽</p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>