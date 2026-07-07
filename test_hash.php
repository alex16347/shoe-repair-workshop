<?php
// Подключение к БД
$host = 'localhost';
$dbname = 'shoe_repair_workshop';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Поиск пользователя admin
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
$stmt->execute();
$user = $stmt->fetch();

if ($user) {
    echo "<h2>Пользователь найден:</h2>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
    echo "<h2>Проверка пароля 'admin123':</h2>";
    $password = 'admin123';
    if (password_verify($password, $user['password_hash'])) {
        echo "✅ Пароль ВЕРНЫЙ!<br>";
    } else {
        echo "❌ Пароль НЕВЕРНЫЙ!<br>";
        echo "Хеш в БД: " . $user['password_hash'] . "<br>";
        echo "Временный хеш для 'admin123': " . password_hash('admin123', PASSWORD_DEFAULT) . "<br>";
    }
} else {
    echo "❌ Пользователь 'admin' НЕ НАЙДЕН в базе данных!";
}
?>