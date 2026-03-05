<?php
require_once '../config/db.php';
$base_url = '/shoe-repair';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . '/src/pages/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            if ($user['role'] == 'admin') {
                header('Location: ' . $base_url . '/src/pages/admin.php');
            } else {
                header('Location: ' . $base_url . '/src/pages/dashboard.php');
            }
            exit();
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/mobile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="auth-form">
            <h2>Вход в систему</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Логин</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Войти</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Нет аккаунта? <a href="<?php echo $base_url; ?>/src/pages/register.php">Зарегистрироваться</a>
            </p>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>