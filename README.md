# Manga Shelf

漫画を管理するための WordPress プラグインです。

## 必要環境

- WordPress 5.9 以上
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

## リリース

プラグインの開発版バージョンは `0.0.0-dev` です。SemVer 形式のタグを push すると GitHub Actions が次を実行します。

1. Composer の本番依存関係を含む `manga-shelf-<tag>.zip` を生成する
2. ZIP 内のプラグインバージョンをタグ名に置換する
3. 同名の GitHub Release を作成し、ZIP を添付する

最初のリリースは次のように作成できます。

```bash
git tag 0.0.0
git push origin 0.0.0
```

GitHub Release が公開されると、`0.0.0-dev` をインストールした WordPress の管理画面に `0.0.0` の更新通知が表示され、Release に添付された ZIP から更新できます。
