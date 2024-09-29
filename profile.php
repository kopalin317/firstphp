<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Получение данных пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Обновление профиля
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $hashed_password, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $_SESSION['user_id']]);
    }

    echo "Данные обновлены!";
}
?>

<h1>Профиль</h1>
<form method="POST" action="">
    Имя: <input type="text" name="name" value="<?= $user['name'] ?>" required><br>
    Телефон: <input type="text" name="phone" value="<?= $user['phone'] ?>" required><br>
    Почта: <input type="email" name="email" value="<?= $user['email'] ?>" required><br>
    Новый пароль: <input type="password" name="password"><br>
    <button type="submit">Сохранить изменения</button>
</form>
