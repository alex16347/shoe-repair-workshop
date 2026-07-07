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
    
    // Валидация
    if (empty($username) || empty($password) || empty($full_name) || empty($phone) || empty($email)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть минимум 6 символов';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email';
    } else {
        // Проверка уникальности
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
    <style>
        .form-error {
            font-size: 13px;
            color: #e74c3c;
            margin-top: 4px;
            min-height: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="auth-form">
            <h2>📝 Регистрация</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="registerForm" novalidate>
                <!-- ========================================== -->
                <!-- ТОЛЬКО ОДИН БЛОК ПОЛЕЙ (НЕ ДУБЛИРОВАТЬ!)   -->
                <!-- ========================================== -->
                <div class="form-group">
                    <label for="username">Логин *</label>
                    <input type="text" id="username" name="username" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+" placeholder="Только латиница, цифры и _">
                    <div class="form-error" id="username-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль *</label>
                    <input type="password" id="password" name="password" required minlength="6">
                    <div class="form-error" id="password-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Подтвердите пароль *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <div class="form-error" id="confirm-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="full_name">ФИО *</label>
                    <input type="text" id="full_name" name="full_name" required>
                    <div class="form-error" id="fullname-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" id="phone" name="phone" required placeholder="+7 (999) 123-45-67">
                    <div class="form-error" id="phone-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                    <div class="form-error" id="email-error"></div>
                </div>
                <!-- ========================================== -->
                <!-- КОНЕЦ БЛОКА ПОЛЕЙ                           -->
                <!-- ========================================== -->
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Зарегистрироваться</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                Уже есть аккаунт? <a href="<?php echo $base_url; ?>/src/pages/login.php">Войти</a>
            </p>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registerForm');
        const username = document.getElementById('username');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');
        const fullName = document.getElementById('full_name');
        
        // Валидация в реальном времени
        username.addEventListener('input', function() {
            const error = document.getElementById('username-error');
            if (this.value.length < 3) {
                error.textContent = '❌ Минимум 3 символа';
            } else if (!/^[a-zA-Z0-9_]+$/.test(this.value)) {
                error.textContent = '❌ Только латиница, цифры и _';
            } else {
                error.textContent = '✅ OK';
                error.style.color = '#27ae60';
            }
        });
        
        password.addEventListener('input', function() {
            const error = document.getElementById('password-error');
            if (this.value.length < 6) {
                error.textContent = '❌ Минимум 6 символов';
            } else {
                error.textContent = '✅ OK';
                error.style.color = '#27ae60';
            }
        });
        
        confirmPassword.addEventListener('input', function() {
            const error = document.getElementById('confirm-error');
            if (this.value !== password.value) {
                error.textContent = '❌ Пароли не совпадают';
            } else {
                error.textContent = '✅ OK';
                error.style.color = '#27ae60';
            }
        });
        
        email.addEventListener('input', function() {
            const error = document.getElementById('email-error');
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
                error.textContent = '❌ Неверный email';
            } else {
                error.textContent = '✅ OK';
                error.style.color = '#27ae60';
            }
        });
        
        phone.addEventListener('input', function() {
            const error = document.getElementById('phone-error');
            const cleaned = this.value.replace(/[\s\-()]/g, '');
            if (cleaned.length < 10) {
                error.textContent = '❌ Минимум 10 цифр';
            } else {
                error.textContent = '✅ OK';
                error.style.color = '#27ae60';
            }
        });
        
        fullName.addEventListener('input', function() {
            const error = document.getElementById('fullname-error');
            if (this.value.length < 2) {
                error.textContent = '❌ Введите полное имя';
            } else {
                error.textContent = '✅ OK';
                error.style.color = '#27ae60';
            }
        });
        
        form.addEventListener('submit', function(e) {
            const errors = document.querySelectorAll('.form-error');
            let hasError = false;
            errors.forEach(err => {
                if (err.textContent && !err.textContent.includes('✅')) {
                    hasError = true;
                }
            });
            
            if (hasError) {
                e.preventDefault();
                alert('⚠️ Пожалуйста, исправьте ошибки в форме');
            }
        });
    });
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>