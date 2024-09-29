<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Проверка по телефону или email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Проверка капчи
        if (check_captcha($_POST["smart-token"])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: profile.php");
        } else {
            echo "Ошибка капчи!";
        }
    } else {
        echo "Неверный логин или пароль!";
    }
}


function check_captcha($token) {
    $ch = curl_init();
    $args = http_build_query([
        "secret" => 'ysc2_6rdCWQUNLN682IJwL58C35K5lhTLdb8iK6ByOYCidaf6b4b1',
        "token" => $token,
        "ip" => $_SERVER['REMOTE_ADDR'],
    ]);
    curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/validate?$args");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
        return true;
    }
    $resp = json_decode($server_output);
    return $resp->status === "ok";
}
?>

<form method="POST" action="">
    Логин (телефон или email): <input type="text" name="login" required><br>
    Пароль: <input type="password" name="password" required><br>
    <!-- Yandex SmartCaptcha -->
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
    <div class="smart-captcha" data-sitekey="ysc1_6rdCWQUNLN682IJwL58CjtZ39SKFqiIkKEBblvdo612db595"></div>
    <!-- <div class="smart-captcha"></div> -->
    <button type="submit">Войти</button>
</form>
