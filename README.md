# BookShelf - 書籍レビューアプリ

書籍を登録・管理し、レビューや読書計画を共有できる書籍レビューアプリです。  
COACHTECH 模擬クライアントワーク課題として実装しました。

---

## 使用技術

| カテゴリ | 技術 |
|---|---|
| バックエンド | PHP 8.5 / Laravel 10 |
| フロントエンド | Blade / Tailwind CSS 3 / Alpine.js |
| データベース | MySQL 8.4 |
| 認証（Web） | Laravel Fortify |
| 認証（API） | Laravel Sanctum |
| コンテナ | Docker / Laravel Sail |
| ビルドツール | Vite |
| テスト | PHPUnit / SQLite in-memory |
| コード整形 | Laravel Pint |

---

## 環境構築

### 前提条件

- Docker Desktop（または Docker Engine）
- WSL2（Windows の場合）

### 手順

```bash
# 1. リポジトリをクローン
git clone <repository-url>
cd bookshelf

# 2. 環境ファイルを作成
cp .env.example .env

# 3. Composer 依存関係インストール（Sail 初回起動前）
docker run --rm -v $(pwd):/app composer install --no-scripts

# 4. Sail でコンテナ起動
./vendor/bin/sail up -d

# 5. アプリキーを生成
./vendor/bin/sail php artisan key:generate

# 6. マイグレーション + シーダー実行
./vendor/bin/sail php artisan migrate --seed

# 7. フロントエンドビルド
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

アプリは http://localhost でアクセスできます。

### テストアカウント（シーダーで作成済み）

| 名前 | メールアドレス | パスワード |
|---|---|---|
| 山田太郎 | yamada@example.com | password |
| 鈴木花子 | suzuki@example.com | password |

---

## ER 図

`er_diagram.html` をブラウザで開いてください。

主なテーブル構成:

```
users
  ├── books (user_id FK)
  │     ├── book_genre (book_id, genre_id)
  │     └── reviews (book_id, user_id)
  │           └── review_likes (review_id, user_id)
  ├── favorites (user_id, book_id)
  ├── reading_plans (user_id, book_id)
  ├── notifications (user_id)
  └── personal_access_tokens (Sanctum)

genres
  └── book_genre (genre_id, book_id)
```

---

## Web 画面一覧

| URL | 画面 | 認証 |
|---|---|---|
| `/books` | 書籍一覧（検索・ソート・ページネーション） | 不要 |
| `/books/create` | 書籍登録（ISBN自動入力付き） | 必要 |
| `/books/{id}` | 書籍詳細・レビュー一覧 | 不要 |
| `/books/{id}/edit` | 書籍編集 | 必要（作成者のみ） |
| `/ranking` | 書籍ランキング（評価順） | 不要 |
| `/genres` | ジャンル一覧 | 不要 |
| `/favorites` | お気に入り一覧 | 必要 |
| `/report` | マイ読書レポート（統計ダッシュボード） | 必要 |
| `/reading-plans` | 読書計画一覧 | 必要 |
| `/notifications` | 通知一覧 | 必要 |

---

## API エンドポイント一覧

ベース URL: `http://localhost/api`

### 認証不要

| メソッド | エンドポイント | 説明 |
|---|---|---|
| GET | `/books` | 書籍一覧（`q`, `genre_id`, `sort`, `page` クエリ対応） |
| GET | `/books/{id}` | 書籍詳細 |
| GET | `/isbn/{isbn}` | ISBN で Google Books から書籍情報を取得 |

### トークン認証

| メソッド | エンドポイント | 説明 |
|---|---|---|
| POST | `/tokens/create` | アクセストークン発行（`email`, `password`, `device_name`） |
| DELETE | `/tokens/revoke` | アクセストークン失効（Bearer トークン必須） |

### Sanctum トークン必須（`Authorization: Bearer <token>`）

| メソッド | エンドポイント | 説明 |
|---|---|---|
| POST | `/books` | 書籍登録 |
| PUT | `/books/{id}` | 書籍更新（作成者のみ） |
| DELETE | `/books/{id}` | 書籍削除（作成者のみ） |

---

## 日次バッチ

```bash
# 読書計画の期限切れ自動失効 + リマインダー通知（毎朝 08:00 自動実行）
./vendor/bin/sail php artisan reading-plans:process

# スケジュール一覧確認
./vendor/bin/sail php artisan schedule:list
```

---

## テスト実行

```bash
# 全テスト実行
./vendor/bin/sail php artisan test

# カバレッジレポート出力（Xdebug 必要）
./vendor/bin/sail php artisan test --coverage

# 特定のテストのみ
./vendor/bin/sail php artisan test --filter=BookController
```

現在のテストスイート: **120 テスト / 235 アサーション**

---

## 主要シナリオ動作確認チェックリスト

### 認証
- [ ] ユーザー登録 → ログイン → ログアウト
- [ ] 未認証時に書籍登録ページにアクセス → ログインページにリダイレクト

### 書籍 CRUD
- [ ] 書籍を ISBN で自動入力して登録
- [ ] 書籍一覧でキーワード検索・ジャンルフィルター・ソートが機能する
- [ ] 書籍を編集・削除できる（作成者のみ）
- [ ] 他ユーザーの書籍は編集・削除できない（403）

### レビュー
- [ ] 書籍詳細ページでレビューを投稿
- [ ] レビューへのいいねが機能する
- [ ] レビューを編集・削除できる（投稿者のみ）

### お気に入り・ランキング
- [ ] お気に入りに追加・解除ができる
- [ ] ランキングページで評価順に並ぶ

### 読書計画
- [ ] 読書計画を作成・編集・削除できる
- [ ] 読書中に変更したとき開始日が自動セットされる
- [ ] 読了ボタンで完了日が自動セットされる

### マイ読書レポート
- [ ] 総レビュー数・平均評価・評価分布が正しく表示される
- [ ] 高評価書籍 TOP5・ジャンル別評価 TOP5 が表示される

### API
- [ ] `POST /api/tokens/create` でトークンを取得
- [ ] `GET /api/books` で書籍一覧を取得
- [ ] `POST /api/books`（Bearer トークン付き）で書籍を登録
- [ ] 他ユーザーの書籍を `DELETE /api/books/{id}` → 403

---

## コード整形

```bash
# Pint で全 PHP ファイルを整形
./vendor/bin/sail pint

# 整形が必要なファイルの確認（変更しない）
./vendor/bin/sail pint --test
```
