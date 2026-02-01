<?php
session_start();

if (!empty($_SESSION['login_user_id'])) {
    header("HTTP/1.1 302 Found");
    header("Location: ./index.html");
    return;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームからの入力を受け取る
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
    $select_sth = $dbh->prepare("SELECT * FROM users WHERE email = :email");
    $select_sth->execute([':email' => $email]);
    $user = $select_sth->fetch();

    if (empty($user)) {
        $message = "メールアドレスまたはパスワードが間違っています。";
    } else {
        if (password_verify($password, $user['password'])) {
            $_SESSION['login_user_id'] = $user['id'];
            header("HTTP/1.1 302 Found");
            header("Location: ./index.html");
            return;
        } else {
            $message = "メールアドレスまたはパスワードが間違っています。";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFocus - ログイン</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #1c1e21;
        }
        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 { margin-bottom: 30px; font-size: 1.8rem; color: #1d9bf0; }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #cfd9de;
            border-radius: 6px;
            font-size: 1rem;
            outline: none;
            transition: 0.2s;
        }
        input:focus { border-color: #1d9bf0; box-shadow: 0 0 0 3px rgba(29,155,240,0.1); }
        button {
            width: 100%;
            padding: 12px;
            background-color: #1d9bf0;
            color: #fff;
            border: none;
            border-radius: 9999px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
        }
        button:hover { background-color: #1a8cd8; }
        .error { color: #f4212e; font-size: 0.9rem; margin-bottom: 15px; text-align: left; }
        .link { margin-top: 20px; font-size: 0.9rem; }
        .link a { color: #1d9bf0; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>TechFocus</h1>
        
        <?php if (!empty($message)): ?>
            <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="メールアドレス" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <button type="submit">ログイン</button>
        </form>

        <div class="link">
            アカウントをお持ちでないですか？<br>
            <a href="/register.php">会員登録はこちら</a>
        </div>
    </div>
</body>
</html>
