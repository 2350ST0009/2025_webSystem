## 概要

Docker Compose を利用して構築された、Twitterっぽいアプリケーションです。

---

## 主な機能

-   **ユーザー認証**: 会員登録、ログイン、ログアウト機能。
-   **タイムライン**: 「フォロー中」と「すべての投稿」をタブで切り替え可能です。
-   **画像投稿**: 複数枚の画像投稿に対応し、ブラウザ側で自動リサイズを行ってから送信します。
-   **無限スクロール**: 画面下部に到達すると、過去の投稿を自動的に読み込みます。
-   **レスポンシブ**: スマートフォンでも見やすいように対応させました。

---

## ディレクトリ構成

```
2025_webSystem/
├── docker-compose.yml
├── .gitignore
└── public/
    ├── index.html      # メイン画面 (タイムライン)
    ├── login.php       # ログイン画面
    ├── register.php    # 会員登録画面
    ├── style.css       # デザイン定義
    ├── post.php        # 投稿処理API
    └── image/          # 画像保存ディレクトリ
```
---

## 構築方法

1.  AWS EC2などのサーバーにSSH接続します。
2.  以下のコマンドを実行して、環境をセットアップし起動します。

    ```bash
    # リポジトリの取得
    git clone [https://github.com/2350ST0009/2025_webSystem.git](https://github.com/2350ST0009/2025_webSystem.git)
    cd 2025_webSystem

    # 画像保存用フォルダの作成
    mkdir -p public/image
    chmod 777 public/image

    # アプリケーション起動
    docker compose up -d --build
    ```

3.  WebブラウザでサーバーのIPアドレスにアクセスします。

---

## データベース設定

アプリケーションを動作させるには、MySQLコンテナに接続し (`docker compose exec mysql mysql -u root -p`)、以下のSQLを実行してテーブルを作成する必要があります。

```sql
USE example_db;

-- 1. ユーザーテーブル
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    icon_filename TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. 投稿テーブル
CREATE TABLE IF NOT EXISTS bbs_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    body TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 3. 画像管理テーブル
CREATE TABLE IF NOT EXISTS entry_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entry_id INT NOT NULL,
    image_filename TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (entry_id)
);

-- 4. フォロー関係テーブル
CREATE TABLE IF NOT EXISTS user_relationships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_user_id INT NOT NULL,
    followee_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_follow (follower_user_id, followee_user_id)
);
