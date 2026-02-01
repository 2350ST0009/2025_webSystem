<?php
session_start();

if (!empty($_SESSION['login_user_id'])) {
    header("HTTP/1.1 302 Found");
    header("Location: ./index.html");
    return;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($name) || empty($email) || empty($password)) {
        $message = "すべての項目を入力してください。";
    } else {
        $dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
        
        // 重複チェック
        $check_sth = $dbh->prepare("SELECT id FROM users WHERE email = :email");
        $check_sth->execute([':email' => $email]);
        if ($check_sth->fetch()) {
            $message = "このメールアドレスは既に登録されています。";
        } else {
            // 登録実行
            $insert_sth = $dbh->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :pass)");
            $insert_sth->execute([
                ':name' => $name,
                ':email' => $email,
                ':pass' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            
            // そのままログインさせる
            $_SESSION['login_user_id'] = $dbh->lastInsertId();
            header("HTTP/1.1 302 Found");
            header("Location: ./index.html");
            return;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFocus - 新規登録</title>
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
        input[type="text"], input[type="email"], input[type="password"] {
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
        <h1>アカウントを作成</h1>
        
        <?php if (!empty($message)): ?>
            <div class="error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="ユーザー名" required>
            <input type="email" name="email" placeholder="メールアドレス" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <button type="submit">登録する</button>
        </form>

        <div class="link">
            すでにアカウントをお持ちですか？<br>
            <a href="/login.php">ログインはこちら</a>
        </div>
    </div>
</body>
</html>
