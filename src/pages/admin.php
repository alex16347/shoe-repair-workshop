<?php
require_once '../config/db.php';
$base_url = '/shoe-repair';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ' . $base_url . '/src/pages/login.php');
    exit();
}

// Обработка изменения статуса
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status_id = $_POST['status_id'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status_id = ? WHERE id = ?");
    $stmt->execute([$status_id, $order_id]);
}

// Получение всех статусов для фильтра
$stmt = $pdo->query("SELECT * FROM order_statuses ORDER BY sort_order");
$statuses = $stmt->fetchAll();

// Фильтры
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Построение WHERE для фильтров
$where = [];
$params = [];

if ($status_filter) {
    $where[] = "o.status_id = ?";
    $params[] = $status_filter;
}

if ($date_from) {
    $where[] = "DATE(o.created_at) >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $where[] = "DATE(o.created_at) <= ?";
    $params[] = $date_to;
}

$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Пагинация
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Подсчёт общего количества заказов с фильтрами
$count_stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM orders o
    JOIN order_statuses os ON o.status_id = os.id
    $where_clause
");
$count_stmt->execute($params);
$total_orders = $count_stmt->fetch()['total'];
$total_pages = ceil($total_orders / $per_page);

// Получение заказов с пагинацией
$per_page = (int)$per_page;
$offset = (int)$offset;

$stmt = $pdo->prepare("
    SELECT o.*, u.username, u.full_name, u.phone, u.email, os.status_name, os.color 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_statuses os ON o.status_id = os.id
    $where_clause
    ORDER BY o.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $per_page, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll();

// =============================================
// СТАТИСТИКА
// =============================================
$stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
$total = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status_id = 1");
$new_orders = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status_id = 2");
$in_progress = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status_id = 3");
$completed = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status_id = 4");
$delivered = $stmt->fetch()['count'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/mobile.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/animations.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .stat-card h3 {
            font-size: 13px;
            color: #777;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .stat-card .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #2A5C82;
        }
        .filters {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .filters-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        .filter-group {
            flex: 1;
            min-width: 150px;
        }
        .filter-group label {
            display: block;
            font-size: 13px;
            color: #777;
            margin-bottom: 4px;
            font-weight: 500;
        }
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #2A5C82;
            outline: none;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .orders-table th {
            background: #f8f9fa;
            padding: 10px 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            font-size: 13px;
        }
        .orders-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
            font-size: 13px;
        }
        .orders-table tbody tr {
            transition: background 0.2s ease;
        }
        .orders-table tbody tr:hover {
            background: #f0f7ff;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #2A5C82;
            transition: all 0.3s ease;
        }
        .pagination a:hover {
            background: #2A5C82;
            color: white;
            transform: translateY(-2px);
        }
        .pagination a.active {
            background: #2A5C82;
            color: white;
        }
        .pagination a.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        .btn-small {
            padding: 5px 12px;
            font-size: 12px;
        }
        @media (max-width: 768px) {
            .admin-stats {
                grid-template-columns: repeat(3, 1fr);
            }
            .filters-form {
                flex-direction: column;
            }
            .filter-group {
                min-width: 100%;
            }
        }
        @media (max-width: 390px) {
            .admin-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            .orders-table {
                font-size: 11px;
            }
            .orders-table th,
            .orders-table td {
                padding: 6px 8px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="welcome">
            <h2>👑 Панель администратора</h2>
            <p>Управление заказами мастерской по ремонту обуви</p>
        </div>
        
        <!-- ========================================== -->
        <!-- СТАТИСТИКА                                  -->
        <!-- ========================================== -->
        <div class="admin-stats">
            <div class="stat-card">
                <h3>📊 Всего заказов</h3>
                <div class="stat-number"><?php echo $total; ?></div>
            </div>
            <div class="stat-card">
                <h3>🆕 Новые</h3>
                <div class="stat-number" style="color: #3498db;"><?php echo $new_orders; ?></div>
            </div>
            <div class="stat-card">
                <h3>⚙️ В работе</h3>
                <div class="stat-number" style="color: #f39c12;"><?php echo $in_progress; ?></div>
            </div>
            <div class="stat-card">
                <h3>✅ Готовы</h3>
                <div class="stat-number" style="color: #27ae60;"><?php echo $completed; ?></div>
            </div>
            <div class="stat-card">
                <h3>📦 Выданы</h3>
                <div class="stat-number" style="color: #95a5a6;"><?php echo $delivered; ?></div>
            </div>
        </div>
        
        <!-- ========================================== -->
        <!-- ФИЛЬТРЫ                                    -->
        <!-- ========================================== -->
        <div class="filters">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label for="status">📌 Статус</label>
                    <select id="status" name="status">
                        <option value="">Все статусы</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo $status['id']; ?>" 
                                <?php echo $status_filter == $status['id'] ? 'selected' : ''; ?>>
                                <?php echo $status['status_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date_from">📅 С даты</label>
                    <input type="date" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                </div>
                
                <div class="filter-group">
                    <label for="date_to">📅 По дату</label>
                    <input type="date" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                </div>
                
                <div class="filter-group" style="flex: 0.5; min-width: auto;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">🔍 Применить</button>
                </div>
                <div class="filter-group" style="flex: 0.5; min-width: auto;">
                    <a href="admin.php" class="btn" style="width: 100%; background: #95a5a6; color: white; text-align: center;">🔄 Сбросить</a>
                </div>
            </form>
        </div>
        
        <!-- ========================================== -->
        <!-- ТАБЛИЦА ЗАКАЗОВ                            -->
        <!-- ========================================== -->
        <div class="card">
            <h3>📋 Список всех заказов (<?php echo $total_orders; ?>)</h3>
            
            <?php if (empty($orders)): ?>
                <p style="text-align: center; padding: 30px; color: #999;">Заказов не найдено</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Клиент</th>
                                <th>Тип</th>
                                <th>Описание</th>
                                <th>Дата</th>
                                <th>Стоимость</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($order['full_name']); ?><br>
                                        <small style="color: #888;"><?php echo $order['phone']; ?></small>
                                    </td>
                                    <td><?php echo $order['shoe_type']; ?></td>
                                    <td style="max-width: 150px; font-size: 13px;">
                                        <?php echo htmlspecialchars(mb_substr($order['description'], 0, 40)) . (mb_strlen($order['description']) > 40 ? '...' : ''); ?>
                                    </td>
                                    <td><?php echo date('d.m.Y', strtotime($order['repair_date'])); ?></td>
                                    <td><?php echo number_format($order['estimated_cost'], 0, ',', ' '); ?> ₽</td>
                                    <td>
                                        <span class="status-badge" style="background: <?php echo $order['color'] . '22'; ?>; color: <?php echo $order['color']; ?>;">
                                            <?php echo $order['status_name']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="" style="display: flex; gap: 5px; align-items: center;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status_id" onchange="this.form.submit()" style="padding: 5px 8px; border-radius: 4px; border: 1px solid #ddd; font-size: 13px;">
                                                <?php foreach ($statuses as $status): ?>
                                                    <option value="<?php echo $status['id']; ?>" 
                                                        <?php echo $status['id'] == $order['status_id'] ? 'selected' : ''; ?>>
                                                        <?php echo $status['status_name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Пагинация -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <a href="?page=<?php echo max(1, $page-1); ?>&status=<?php echo $status_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" 
                           class="<?php echo $page <= 1 ? 'disabled' : ''; ?>">‹</a>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i >= $page - 2 && $i <= $page + 2): ?>
                                <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" 
                                   class="<?php echo $i == $page ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <a href="?page=<?php echo min($total_pages, $page+1); ?>&status=<?php echo $status_filter; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" 
                           class="<?php echo $page >= $total_pages ? 'disabled' : ''; ?>">›</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="<?php echo $base_url; ?>/src/pages/dashboard.php" class="btn btn-primary">← Вернуться в личный кабинет</a>
        </div>
    </div>
    
    <script>
        // Toast уведомление при изменении статуса
        document.querySelectorAll('form[action=""]').forEach(form => {
            form.addEventListener('submit', function() {
                showToast('✅ Статус заказа изменён!', 'success');
            });
        });
    </script>
    
    <script src="<?php echo $base_url; ?>/src/js/animations.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>