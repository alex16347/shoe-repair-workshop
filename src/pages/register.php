<?php
require_once '../config/db.php';
$base_url = '/shoe-repair';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    
    if (empty($username) || empty($password) || empty($full_name) || empty($phone) || empty($email)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть минимум 6 символов';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Пользователь с таким логином или email уже существует';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, phone, email, role) VALUES (?, ?, ?, ?, ?, 'user')");
            
            if ($stmt->execute([$username, $hashed_password, $full_name, $phone, $email])) {
                $success = 'Регистрация успешна! <a href="' . $base_url . '/src/pages/login.php">Войти</a>';
            } else {
                $error = 'Ошибка при регистрации';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/src/css/mobile.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="auth-form">
            <h2>Регистрация</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
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
                
                <div class="form-group">
                    <label>Подтвердите пароль</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <label>ФИО</label>
                    <input type="text" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="phone" required placeholder="+7 (999) 123-45-67">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Уже есть аккаунт? <a href="<?php echo $base_url; ?>/src/pages/login.php">Войти</a>
            </p>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>