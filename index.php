<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    if ($password != $password_repeat) {
        echo "Пароли не совпадают!";
        exit();
    }

    // Проверка уникальности email и телефона
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$email, $phone]);
    $user = $stmt->fetch();

    if ($user) {
        echo "Пользователь с таким email или телефоном уже существует!";
        exit();
    }

    // Хэширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Вставка нового пользователя
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $phone, $hashed_password])) {
        echo "Регистрация успешна!";
        header("Location: login.php");
    } else {
        echo "Ошибка регистрации!";
    }
}
?>

<form method="POST" action="">
    Имя: <input type="text" name="name" required><br>
    Телефон: <input type="text" name="phone" required><br>
    Почта: <input type="email" name="email" required><br>
    Пароль: <input type="password" name="password" required><br>
    Повтор пароля: <input type="password" name="password_repeat" required><br>
    <button type="submit">Регистрация</button>
</form>
