# WorkLogger — 作業ログ記録ツール

日々の作業内容・時間をシンプルに記録・管理するPHP製Webアプリです。
「何にどれだけ時間を使ったか」を可視化し、業務効率の改善に役立てます。

---

## 主な機能

- 作業ログの登録・編集・削除
- 作業カテゴリ・プロジェクト別の分類
- 日付・期間での絞り込み表示
- 作業時間の集計・一覧表示

---

## 技術スタック

| カテゴリ | 使用技術 |
|----------|---------|
| 言語 | PHP |
| フロントエンド | HTML / CSS / JavaScript |
| データベース | MySQL / SQLite |

---

## 動作環境

- PHP 8.0 以上
- Webサーバー（Apache / Nginx）またはPHP組み込みサーバー
- MySQL 8.0 以上 / SQLite 3

---

## セットアップ

```bash
# 1. リポジトリをクローン
git clone https://github.com/TarkMatter/WorkLogger.git
cd WorkLogger

# 2. 設定ファイルを編集
cp config.sample.php config.php
# config.php にDB接続情報を記入

# 3. データベースの初期化
php migrate.php

# 4. PHP組み込みサーバーで起動（開発用）
php -S localhost:8000

# 5. ブラウザで http://localhost:8000 にアクセス
```

---

## 注意事項

- 本リポジトリは開発・学習用のサンプルです
- 本番環境で使用する場合は認証機能の追加を推奨します
