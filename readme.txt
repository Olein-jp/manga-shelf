=== Manga Shelf ===
Contributors: olein
Tags: manga, books, library, block-editor
Requires at least: 6.7
Requires PHP: 7.4
Stable tag: 0.1.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

漫画作品、既刊情報、読書状態を管理し、ブロックテーマで自由に表示します。

== Description ==

Manga Shelf は、漫画作品を自分の本棚として登録・公開するためのプラグインです。
楽天ブックス書籍検索APIから作品と既刊を取り込み、巻一覧ブロック、Block Bindings、パターン、プラグインテンプレートで表示できます。

v0.1 は紙の通常版コミックスを対象とします。定期新刊監視とAmazon連携は今後のバージョンで追加予定です。

== Installation ==

1. プラグインをアップロードして有効化します。
2. 「漫画 → 楽天API設定」で楽天Web ServiceのアプリケーションIDとAccess Keyを設定します。
3. 「漫画 → 楽天から追加」で作品を検索・登録します。

== Frequently Asked Questions ==

= アンインストールでデータは消えますか？ =

既定では保持されます。`MANGA_SHELF_DELETE_DATA` を `true` に設定した場合のみ、アンインストール時に作品、巻テーブル、設定を削除します。

== Changelog ==

= 0.1.5 =

* 楽天書影のローカル保存を廃止し、商品リンク付きの外部画像として表示。
* 漫画ページへ楽天Web Serviceの公式クレジットを追加。
* 旧ローカル書影を確認しながら移行・削除する管理画面を追加。

= 0.1.4 =

* 楽天Web Serviceのリクエストコンテキスト検証に必要なOriginヘッダーを追加。
* APIリクエストのUser-Agentをブラウザ互換形式へ変更。

= 0.1.3 =

* 楽天Web Serviceへのリクエストに登録サイトを示すRefererヘッダーを追加。

= 0.1.2 =

* Access Keyをクエリパラメータとして送信し、現行の楽天Web Service認証方式に対応。
* 現行APIのエラー応答から詳細メッセージを表示できるよう改善。

= 0.1.1 =

* 楽天Web Serviceの必須Access Keyに対応。
* 現行APIエンドポイントへ更新し、APIエラーの詳細表示を改善。

= 0.1.0 =

* MVPを実装。
