PHPとMySQLで構築された、画像投稿機能付きのSNSアプリケーションです。

## 📖 概要
- **ユーザー認証**: 会員登録、ログイン、ログアウト
- **投稿**: 画像付き投稿（自動リサイズ機能あり）
- **タイムライン**: フォロー中ユーザーのみの表示 / 全ユーザー表示の切り替え
- **UI/UX**: レスポンシブデザイン、無限スクロール実装

---

## 🚀 デプロイ手順 (AWS EC2構築ガイド)

本アプリケーションをAWS EC2 (Amazon Linux 2023) 上にゼロから構築する手順です。

### 1. AWS EC2インスタンスの作成 (AWSコンソール操作)
WebブラウザでAWSマネジメントコンソールにログインし、以下の手順でサーバーを用意します。

1.  **EC2ダッシュボードを開く**
    * 検索バーで「EC2」と入力し、サービスを選択します。
2.  **インスタンスの起動**
    * 画面上の「インスタンスを起動」ボタンをクリックします。
3.  **設定項目**
    * **名前**: 任意の名前（例: `TechFocus-Server`）
    * **OSイメージ**: `Amazon Linux 2023 AMI` を選択（無料枠対象）
    * **インスタンスタイプ**: `t2.micro` または `t3.micro`（無料枠対象）
    * **キーペア**: 「新しいキーペアの作成」から作成し、`.pem` ファイルをPCに保存します。
    * **ネットワーク設定**:
        * 「インターネットからのHTTPSトラフィックを許可」にチェック
        * 「インターネットからのHTTPトラフィックを許可」にチェック
4.  **起動**
    * 「インスタンスを起動」をクリックします。

### 2. 環境構築 (SSH接続 & セットアップ)
PCのターミナルからサーバーに接続し、必要なツール（Docker, Git）をインストールします。

**接続コマンド例:**
```bash

# キーの権限変更（Mac/Linuxの場合）
chmod 400 your-key.pem

# SSH接続
ssh -i "your-key.pem" ec2-user@<EC2のパブリックIP>

# git,Dockerのインストール
# 1. システム更新とGit, Dockerのインストール
sudo yum update -y
sudo yum install git docker -y

# 2. Dockerの起動と権限設定
sudo service docker start
sudo usermod -a -G docker ec2-user

# 3. Docker Composeのインストール
sudo mkdir -p /usr/local/lib/docker/cli-plugins/
sudo curl -SL [https://github.com/docker/compose/releases/latest/download/docker-compose-linux-x86_64](https://github.com/docker/compose/releases/latest/download/docker-compose-linux-x86_64) -o /usr/local/lib/docker/cli-plugins/docker-compose
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose

# 4. 設定反映のため一度ログアウト
exit

# アプリケーションの起動
# 1. リポジトリのクローン
git clone [https://github.com/2350ST0009/2025_webSystem.git](https://github.com/2350ST0009/2025_webSystem.git)

# 2. ディレクトリ移動
cd 2025_webSystem

# 3. 画像保存用ディレクトリの作成と権限設定
mkdir -p public/image
chmod 777 public/image

# 4. Dockerコンテナの起動
docker compose up -d --build

# MySQLにログイン
docker compose exec mysql mysql -u root -p

# SQLの実行
USE example_db;

-- ユーザーテーブル
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    icon_filename TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 投稿テーブル
CREATE TABLE IF NOT EXISTS bbs_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    body TEXT,
    image_filename TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- フォロー関係テーブル
CREATE TABLE IF NOT EXISTS user_relationships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_user_id INT NOT NULL,
    followee_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_follow (follower_user_id, followee_user_id)
);

# アプリケーションの起動
  以下のコマンドを実行して、アプリケーションを起動します。
    ```bash
    docker compose up -d
    ```
3.  WebブラウザでサーバーのIPアドレスにアクセスします。
