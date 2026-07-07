<?php
require_once '../config/db.php';
$base_url = '/shoe-repair';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . '/src/pages/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение заказов пользователя с полными данными о статусах
$stmt = $pdo->prepare("
    SELECT o.*, os.status_name, os.color 
    FROM orders o
    JOIN order_statuses os ON o.status_id = os.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
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
    <style>
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }
        .order-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .order-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .order-id {
            font-weight: 700;
            font-size: 16px;
        }
        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px 20px;
            font-size: 14px;
        }
        .order-details .label {
            color: #777;
        }
        .order-details .value {
            font-weight: 500;
        }
        @media (max-width: 390px) {
            .order-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="welcome">
            <h2>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
            <p style="color: #777;">Здесь вы можете создавать заказы и отслеживать их статус</p>
        </div>
        
        <div class="grid-2">
            <!-- Форма создания заказа -->
            <div class="card">
                <h3>📝 Создать новый заказ</h3>
                <form method="POST" action="<?php echo $base_url; ?>/src/pages/create_order.php">
                    <div class="form-group">
                        <label>Тип обуви *</label>
                        <select name="shoe_type" required>
                            <option value="">-- Выберите --</option>
                            <option value="Ботинки">👢 Ботинки</option>
                            <option value="Туфли">👞 Туфли</option>
                            <option value="Кроссовки">👟 Кроссовки</option>
                            <option value="Сапоги">🥾 Сапоги</option>
                            <option value="Сандалии">👡 Сандалии</option>
                            <option value="Другое">Другое</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Описание работы *</label>
                        <textarea name="description" rows="3" required placeholder="Например: замена набоек, ремонт каблука..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Желаемая дата *</label>
                        <input type="date" name="repair_date" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Примерная стоимость (₽) *</label>
                        <input type="number" name="estimated_cost" min="100" step="100" required placeholder="500">
                    </div>
                    
                    <button type="submit" name="create_order" class="btn btn-primary" style="width: 100%;">
                        ✅ Создать заказ
                    </button>
                </form>
            </div>
            
            <!-- Список заказов -->
            <div class="card">
                <h3>📋 Мои заказы</h3>
                
                <?php if (empty($orders)): ?>
                    <p style="text-align: center; padding: 20px; color: #999;">У вас пока нет заказов</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-item">
                            <div class="order-header">
                                <span class="order-id">Заказ #<?php echo $order['id']; ?></span>
                                <span class="status-badge" style="background: <?php echo $order['color'] . '22'; ?>; color: <?php echo $order['color']; ?>;">
                                    <?php echo $order['status_name']; ?>
                                </span>
                            </div>
                            <div class="order-details">
                                <div>
                                    <span class="label">Тип обуви:</span>
                                    <span class="value"><?php echo htmlspecialchars($order['shoe_type']); ?></span>
                                </div>
                                <div>
                                    <span class="label">Стоимость:</span>
                                    <span class="value"><?php echo number_format($order['estimated_cost'], 0, ',', ' '); ?> ₽</span>
                                </div>
                                <div>
                                    <span class="label">Дата выполнения:</span>
                                    <span class="value"><?php echo date('d.m.Y', strtotime($order['repair_date'])); ?></span>
                                </div>
                                <div>
                                    <span class="label">Создан:</span>
                                    <span class="value"><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></span>
                                </div>
                                <div style="grid-column: 1 / -1;">
                                    <span class="label">Описание:</span>
                                    <span class="value"><?php echo htmlspecialchars($order['description']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>