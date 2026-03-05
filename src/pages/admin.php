<?php
require_once '../config/db.php';
$base_url = '/shoe-repair';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ' . $base_url . '/src/pages/login.php');
    exit();
}

$stmt = $pdo->query("SELECT o.*, u.username, u.full_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/mobile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="welcome">
            <h2>Панель администратора</h2>
        </div>
        
        <div class="card">
            <h3>Все заказы</h3>
            <?php if (empty($orders)): ?>
                <p>Заказов нет</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 10px; text-align: left;">ID</th>
                            <th style="padding: 10px; text-align: left;">Клиент</th>
                            <th style="padding: 10px; text-align: left;">Тип обуви</th>
                            <th style="padding: 10px; text-align: left;">Статус</th>
                            <th style="padding: 10px; text-align: left;">Стоимость</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td style="padding: 10px;">#<?php echo $order['id']; ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td style="padding: 10px;"><?php echo $order['shoe_type']; ?></td>
                                <td style="padding: 10px;"><?php echo $order['status_id'] == 1 ? 'Новый' : 'В работе'; ?></td>
                                <td style="padding: 10px;"><?php echo number_format($order['estimated_cost'], 2); ?> ₽</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>