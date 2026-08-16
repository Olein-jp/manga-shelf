# Manga Shelf

漫画作品を自分の本棚として登録・公開し、既刊情報を管理するための WordPress プラグインです。

## MVP（v0.1）

- `manga` カスタム投稿タイプ
- 作者・ジャンルのタクソノミー
- 読書状態、評価、公式サイト、試し読み、出版社、追跡状態のメタデータ
- `wp_manga_volumes` 専用テーブルによる巻情報管理
- 楽天ブックス書籍検索APIの設定・接続確認
- 楽天の検索結果から作品と取得可能な既刊を下書きとして登録
- 最新巻・最新巻発売日の Block Bindings Source
- 出版社・評価・最新巻・発売日の Core Block Variation
- 動的な「漫画：巻一覧」ブロック
- 作品詳細パターンと個別作品・作品一覧のプラグインテンプレート

定期新刊監視、Action Scheduler、Amazon連携、フロント側検索・絞り込み、一般ユーザー用ライブラリは v0.2 以降の対象です。

## 必要環境

- WordPress 6.7 以上
- PHP 7.4 以上
- Docker
- Node.js 18.12 以上と npm 8.19.2 以上
- Composer 2

## 開発環境

```bash
npm install
composer install
npm run env:start
```

WordPress は `http://localhost:8888`、管理画面は `http://localhost:8888/wp-admin/` で開きます。wp-env の初期認証情報はユーザー名 `admin`、パスワード `password` です。

```bash
composer lint
npm run env:status
npm run env:stop
```

## 使い方

1. WordPress管理画面で Manga Shelf を有効化します。
2. 「漫画 → 楽天API設定」で楽天Web ServiceのアプリケーションIDとAccess Keyを保存します。
3. 「接続を確認」でAPIとの疎通を確認します。
4. 「漫画 → 楽天から追加」で作品名を検索し、紙の通常版コミックスを選びます。
5. 作成された下書きで感想、読書状態、評価などを編集して公開します。

アプリケーションIDとアフィリエイトIDは、保存値の代わりに `wp-config.php` からも設定できます。

```php
define( 'MANGA_SHELF_RAKUTEN_APPLICATION_ID', 'your-application-id' );
define( 'MANGA_SHELF_RAKUTEN_ACCESS_KEY', 'your-access-key' );
define( 'MANGA_SHELF_RAKUTEN_AFFILIATE_ID', 'your-affiliate-id' );
```

楽天検索を実行すると、検索語とサイトURLを含むリクエストが楽天Web Serviceへ送信されます。MVPでは「紙の通常版」を基準にし、特装版、限定版、文庫版、完全版、新装版、電子版を既刊一括取込から除外します。判定結果は公開前に確認してください。

## データの削除

通常のアンインストールでは作品・巻・設定を保持します。すべて削除する場合のみ、アンインストール前に次を設定してください。

```php
define( 'MANGA_SHELF_DELETE_DATA', true );
```

## リリース

プラグインの開発版バージョンは `0.1.3-dev` です。SemVer 形式のタグを push すると GitHub Actions が次を実行します。

1. Composer の本番依存関係を含む `manga-shelf-<tag>.zip` を生成する
2. ZIP 内のプラグインバージョンをタグ名に置換する
3. 同名の GitHub Release を作成し、ZIP を添付する

最初のリリースは次のように作成できます。

```bash
git tag 0.1.3
git push origin 0.1.3
```

GitHub Release が公開されると、`0.1.3-dev` をインストールした WordPress の管理画面に `0.1.3` の更新通知が表示され、Release に添付された ZIP から更新できます。
