# Manga Shelf 利用マニュアル

Manga Shelfで漫画作品を登録し、個別ページと作品一覧を公開するまでの手順をまとめています。

## 最初に確認すること

### `single-manga.html`は必須ではありません

ブロックテーマを利用している場合、次のテンプレートはManga Shelfが自動的に登録します。

- `single-manga`: 漫画作品の個別ページ
- `archive-manga`: 漫画作品の一覧ページ

そのため、テーマ内に`single-manga.html`や`archive-manga.html`を作らなくても表示できます。テーマ側でレイアウトを管理したい場合や、Gitでテンプレートを管理したい場合だけ作成してください。

| 利用環境 | 必須ファイル | カスタマイズ方法 |
| --- | --- | --- |
| ブロックテーマ | なし | 「外観 → エディター → テンプレート」で編集 |
| ブロックテーマでファイル管理 | `templates/single-manga.html`、`templates/archive-manga.html` | 子テーマまたは独自テーマに追加 |
| クラシックテーマ | テーマの既存`single.php`・`archive.php`でも表示可能 | 必要に応じて`single-manga.php`、`archive-manga.php`を追加 |

## 1. 初期設定

### プラグインを有効化する

WordPress管理画面の「プラグイン」からManga Shelfを有効化します。有効化すると、管理画面に「漫画」メニューが追加されます。

### 楽天Web Serviceを設定する

1. 楽天Web ServiceでManga Shelf用のアプリを作成します。
2. アプリケーションURLに、Manga Shelfを利用するサイトのURLを設定します。
3. 「許可されたWebサイト」には、実際にAPIを呼び出すWordPressサイトのホスト名を設定します。
4. WordPress管理画面の「漫画 → 楽天API設定」を開きます。
5. Access KeyとアプリケーションIDを入力します。
6. 楽天アフィリエイトを利用する場合は、アフィリエイトIDも入力します。
7. 「変更を保存」を押し、「接続を確認」を実行します。

たとえばサイトURLが`https://manga.example.com/`の場合、「許可されたWebサイト」には`manga.example.com`を設定します。`example.com`だけでは、楽天側の送信元確認で拒否される場合があります。

認証情報を`wp-config.php`で管理する場合は、次の定数を利用できます。定数が設定されている場合は、管理画面の保存値より優先されます。

```php
define( 'MANGA_SHELF_RAKUTEN_APPLICATION_ID', 'your-application-id' );
define( 'MANGA_SHELF_RAKUTEN_ACCESS_KEY', 'your-access-key' );
define( 'MANGA_SHELF_RAKUTEN_AFFILIATE_ID', 'your-affiliate-id' );
```

### Amazonリンクを設定する

各巻のAmazonリンクは、保存済みのISBN-13を検証してISBN-10へ変換し、Amazon.co.jpの商品ページURLを作成します。変換できないISBNは書籍検索へ移動します。Amazonの商品情報APIは利用しないため、APIキーは不要です。

1. Amazonアソシエイトを利用する場合は、Amazonアソシエイト・セントラルでトラッキングIDを確認します。
2. WordPress管理画面の「漫画 → Amazon設定」を開きます。
3. 「アソシエイト・トラッキングID」を入力して保存します。
4. サイトエディターの「漫画：巻一覧」の内側へ標準の「ボタン」ブロックを追加し、その個別ボタンとして「漫画：各巻のAmazonボタン」を選びます。

トラッキングIDを設定しなくても通常のAmazon商品リンクとして利用できます。`wp-config.php`で管理する場合は次の定数を利用できます。

```php
define( 'MANGA_SHELF_AMAZON_TRACKING_ID', 'your-tracking-id-22' );
```

`978`から始まる有効なISBN-13はISBN-10へ変換し、Amazonが書籍向けに案内している`/dp/ISBN-10/ref=nosim`形式の商品ページへリンクします。`979`から始まるISBNなど変換できない場合は、ISBNを使った書籍検索へフォールバックします。ボタンの文言は標準のボタンと同じように編集できます。トラッキングID使用時はリンクの`rel`属性へ`sponsored`を自動設定します。

Amazon指定のアソシエイト開示文は公開画面へ自動出力しません。「漫画 → Amazon設定」に表示される文面を、「このサイトについて」やプライバシーポリシーなど、サイト内の分かりやすい場所へ手動で掲載してください。Amazonアソシエイトの登録サイト、リンク確認、開示、プライバシーポリシーなどは最新の規約に合わせて確認してください。

## 2. 楽天から漫画を登録する

1. 「漫画 → 楽天から追加」を開きます。
2. 作品名を入力して「検索」を押します。
3. 紙の通常版コミックスを選び、「この作品を追加」を押します。
4. 作成された下書きの編集画面を確認します。
5. 本文、抜粋、作者、ジャンル、作品情報を調整します。
6. 内容を確認して公開します。

登録時には、選択した書籍からシリーズ名を判定し、同じシリーズとして取得できた既刊も保存します。特装版、限定版、文庫版、完全版、新装版、電子版、Kindle版は既刊の一括登録から除外されます。自動判定のため、公開前に巻一覧を確認してください。

楽天から登録した作品は、初期状態では次のようになります。

- 投稿状態: 下書き
- 読書状態: 読みたい
- 新刊追跡: 有効
- 作者: 楽天の書籍情報から登録
- 出版社: 楽天の書籍情報から登録
- 楽天書影: メディアライブラリへ保存せず、商品リンク付きの外部画像として表示
- 巻情報: 同じシリーズとして取得できた紙の通常版を保存

## 3. 作品情報を編集する

漫画の編集画面では、通常の投稿項目に加えて次の情報を設定できます。

| 項目 | 用途 |
| --- | --- |
| タイトル | 作品名 |
| 本文 | 感想、紹介文、メモなど |
| 抜粋 | 一覧やテーマ側で利用する短い説明 |
| アイキャッチ画像 | 自分で権利を持つ画像や、利用許諾を得た画像を表示する場合に使用 |
| 作者 | 作者タクソノミー。複数指定可能 |
| ジャンル | 階層型のジャンルタクソノミー |
| 読書状態 | 読みたい、読書中、読了、保留 |
| 評価 | 0〜5、0.5刻み |
| 公式サイト | 作品の公式URL |
| 試し読みURL | 試し読みページのURL |
| 新刊追跡 | 今後の新刊確認に利用する設定 |

「漫画を手動で追加」から作品を作成することもできます。ただし、現行版には巻情報を管理画面から手入力・編集する専用画面はありません。巻一覧を利用する場合は、基本的に「楽天から追加」を利用してください。

## 4. 公開ページを確認する

標準のURLは次のとおりです。

- 作品一覧: `https://example.com/manga/`
- 作品個別: `https://example.com/manga/作品スラッグ/`
- 作者別一覧: 作者タクソノミーのアーカイブ
- ジャンル別一覧: ジャンルタクソノミーのアーカイブ

有効化直後に404になる場合は、「設定 → パーマリンク」を開き、設定を変更せずに「変更を保存」を押してください。

## 5. ブロックテーマでテンプレートを編集する

### プラグイン提供テンプレートをサイトエディターで編集する

1. 「外観 → エディター」を開きます。
2. 「デザイン → テンプレート」を開きます。
3. 「Manga Shelf：個別作品」または「Manga Shelf：作品一覧」を選びます。
4. 必要なブロックを追加・移動・削除します。
5. 保存します。

サイトエディターで保存した内容はデータベースに保存され、プラグインの初期テンプレートより優先されます。プラグインを更新しても、編集したテンプレートは通常そのまま残ります。

### 個別作品の初期テンプレート

プラグインが提供する個別作品テンプレートには、次の要素が含まれます。

1. テンプレートパーツ`header`
2. 楽天書影（商品ページへのリンク付き）
3. 投稿タイトル
4. 投稿本文
5. 見出し「刊行情報」
6. 「漫画：巻一覧」ブロック
7. テンプレートパーツ`footer`

テーマに`header`または`footer`というスラッグのテンプレートパーツがない場合は、サイトエディターで利用中テーマのテンプレートパーツに差し替えてください。

### 作品一覧の初期テンプレート

作品一覧テンプレートは、漫画を新しい順に12件ずつ、3列のグリッドで表示します。各作品には楽天書影とリンク付きタイトルが表示され、末尾にページ送りが入ります。

## 6. テーマファイルでテンプレートを管理する

ブロックテーマでテンプレートをGit管理したい場合は、利用中の子テーマまたは独自テーマに次のファイルを作成します。

```text
your-theme/
└── templates/
    ├── single-manga.html
    └── archive-manga.html
```

### `templates/single-manga.html`の最小例

```html
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->
<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
    <!-- wp:manga-shelf/cover-image /-->
    <!-- wp:post-title {"level":1} /-->
    <!-- wp:post-content /-->
    <!-- wp:heading {"level":2} -->
    <h2 class="wp-block-heading">刊行情報</h2>
    <!-- /wp:heading -->
    <!-- wp:manga-shelf/volume-list -->
    <!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
    <div class="wp-block-group">
        <!-- wp:manga-shelf/volume-cover {"width":120} /-->
        <!-- wp:group {"layout":{"type":"constrained"}} -->
        <div class="wp-block-group">
            <!-- wp:manga-shelf/volume-title {"level":3} /-->
            <!-- wp:manga-shelf/volume-number /-->
            <!-- wp:manga-shelf/volume-release-date /-->
            <!-- wp:buttons -->
            <div class="wp-block-buttons">
                <!-- wp:button {"linkTarget":"_blank","className":"manga-shelf-volume-rakuten-button","metadata":{"bindings":{"url":{"source":"manga-shelf/volume-store","args":{"store":"rakuten"}},"rel":{"source":"manga-shelf/volume-store","args":{"store":"rakuten"}}}}} -->
                <div class="wp-block-button manga-shelf-volume-rakuten-button"><a class="wp-block-button__link wp-element-button" target="_blank">楽天で見る</a></div>
                <!-- /wp:button -->
                <!-- wp:button {"linkTarget":"_blank","className":"manga-shelf-volume-amazon-button","metadata":{"bindings":{"url":{"source":"manga-shelf/volume-store","args":{"store":"amazon"}},"rel":{"source":"manga-shelf/volume-store","args":{"store":"amazon"}}}}} -->
                <div class="wp-block-button manga-shelf-volume-amazon-button"><a class="wp-block-button__link wp-element-button" target="_blank">Amazonで見る</a></div>
                <!-- /wp:button -->
            </div>
            <!-- /wp:buttons -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
    <!-- /wp:manga-shelf/volume-list -->
</main>
<!-- /wp:group -->
<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
```

### `templates/archive-manga.html`の最小例

```html
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->
<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group">
    <!-- wp:query-title {"type":"archive","showPrefix":false} /-->
    <!-- wp:query {"query":{"perPage":12,"postType":"manga","order":"desc","orderBy":"date","inherit":true}} -->
    <div class="wp-block-query">
        <!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
        <!-- wp:manga-shelf/cover-image /-->
        <!-- wp:post-title {"isLink":true} /-->
        <!-- /wp:post-template -->
        <!-- wp:query-pagination /-->
    </div>
    <!-- /wp:query -->
</main>
<!-- /wp:group -->
<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
```

テンプレートの主な優先順位は、サイトエディターで保存したカスタマイズ、利用中テーマのテンプレート、プラグイン提供テンプレート、WordPressの汎用テンプレートの順です。テーマファイルを追加したのに反映されない場合は、サイトエディターに保存済みの同名テンプレートがないか確認してください。

## 7. クラシックテーマでテンプレートを作る

クラシックテーマでは、専用ファイルを作らなくても`single.php`や`archive.php`へフォールバックします。漫画専用のレイアウトにする場合は、子テーマに次のファイルを作成します。

```text
your-child-theme/
├── single-manga.php
└── archive-manga.php
```

### `single-manga.php`の最小例

```php
<?php
get_header();
?>
<main>
    <?php while ( have_posts() ) : ?>
        <?php the_post(); ?>
        <article <?php post_class(); ?>>
            <?php echo do_blocks( '<!-- wp:manga-shelf/cover-image /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
            <h2>刊行情報</h2>
            <?php echo do_blocks( '<!-- wp:manga-shelf/volume-list /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </article>
    <?php endwhile; ?>
</main>
<?php
get_footer();
```

`archive-manga.php`では通常のループを利用し、楽天書影には`do_blocks( '<!-- wp:manga-shelf/cover-image /-->' )`、タイトルとリンクには`the_title()`、`the_permalink()`などを利用できます。

## 8. Manga Shelfのブロックを使う

### 漫画：楽天書影

楽天Books APIから取得した書影を、メディアライブラリへ複製せず、楽天の商品ページへのリンク付き外部画像として表示する動的ブロックです。リンク先がない場合は画像も出力しません。プラグインの個別作品・作品一覧テンプレートには最初から配置されています。

サイトエディターで以前のテンプレートを保存済みの場合、プラグイン更新後も古い「アイキャッチ画像」ブロックが残ります。その場合は「アイキャッチ画像」を削除し、「漫画：楽天書影」ブロックへ置き換えてください。

### 漫画：巻一覧

現在の漫画に保存されている巻を、巻数の昇順で表示する動的ブロックです。巻一覧の内側に作った「1巻分のレイアウト」が、公開画面では登録済みの各巻へ繰り返して適用されます。

内側では、グループ、行、カラム、余白などの標準ブロックと、次の巻情報ブロックを自由に組み合わせられます。

- 漫画：各巻の書影
- 漫画：各巻のタイトル
- 漫画：各巻の巻数
- 漫画：各巻の発売日
- 漫画：各巻のISBN
- 漫画：各巻の楽天ボタン（標準の個別ボタン`core/button`のバリエーション）
- 漫画：各巻のAmazonボタン（標準の個別ボタン`core/button`のバリエーション）

たとえば「左側に書影、右側にタイトルと発売日」「書影を上、楽天・Amazonボタンを下」「タイトルから楽天へリンク」といった構成にできます。書影ブロックでは幅、タイトルブロックではHTML要素と商品リンク、巻数・発売日・ISBNでは前後の文字を設定できます。楽天・Amazonボタンは標準の個別ボタンなので、文言、色、枠線、余白、幅、塗りつぶし・輪郭などを通常どおり変更できます。商品URLとリンクの`rel`属性だけが各巻のデータへ自動接続されます。

編集手順は次のとおりです。

1. 「外観 → エディター → テンプレート」で個別作品テンプレートを開きます。
2. 「漫画：巻一覧」を選択します。
3. 内側のグループや巻情報ブロックを移動・追加・削除します。
4. 商品リンクを追加する場合は、標準の「ボタン」コンテナ（`core/buttons`）を追加し、その内側の個別ボタン（`core/button`）として「漫画：各巻の楽天ボタン」または「漫画：各巻のAmazonボタン」を選びます。
5. 保存し、作品ページで複数巻に同じレイアウトが適用されていることを確認します。

楽天・Amazonの機能は複数ボタンを束ねる`core/buttons`のバリエーションではなく、内側に入る個別の`core/button`のバリエーションとして登録されています。横並びや縦並びを管理するために`core/buttons`が外側のコンテナとして使われますが、各商品ボタン自体は1個ずつ自由に移動・複製・装飾できます。安全な商品URLを生成できない巻では、そのストアのボタンは公開画面へ出力されません。

0.1.7以前のテンプレートに保存済みの「漫画：各巻の楽天リンク」「漫画：各巻のAmazonリンク」は、表示互換のため引き続き動作します。新しく追加する場合は個別ボタンのバリエーションを利用してください。

巻情報がない作品では何も出力しません。テンプレートと投稿本文の両方に配置すると二重表示になるため、どちらか一方に配置してください。

0.1.5以前から残っている空の「漫画：巻一覧」は互換表示になり、従来のタイトル・発売日・楽天リンクを表示します。テンプレートを編集すると初期レイアウトが追加されます。内側のブロックが現れない場合は、巻一覧を一度削除して挿入し直してください。

### 漫画：作品詳細パターン

漫画の編集画面またはサイトエディターで、パターンの「Manga Shelf」カテゴリーから利用できます。楽天書影、タイトル、本文、刊行情報、巻一覧をまとめて配置します。

### 漫画用の段落バリエーション

漫画の編集画面では、次の動的な段落ブロックを挿入できます。

- 漫画：最新巻
- 漫画：最新巻発売日
- 漫画：出版社
- 漫画：評価

これらは保存済みの作品情報や巻情報を参照するため、値を直接入力する必要はありません。

## 9. 楽天書影を安全な表示へ移行する

Manga Shelf 0.1.4以前から更新したサイトでは、過去に楽天の書影がメディアライブラリへ保存されている場合があります。更新しただけではファイルを削除しません。次の順番で移行してください。

1. サイトとデータベースのバックアップを作成します。
2. 「漫画 → 楽天書影の移行」を開きます。
3. 対象作品を確認し、「外部URL表示へ切り替える」を実行します。
4. 個別作品・作品一覧で書影と楽天の商品リンクが正しく表示されることを確認します。
5. サイトエディターで保存済みテンプレートがある場合は、「アイキャッチ画像」を「漫画：楽天書影」へ置き換えます。
6. 問題がなければ、同じ画面で確認チェックを入れ、「旧ローカル書影を完全削除」を実行します。
7. 各巻の書影がない作品は、同じ画面の「手順3：各巻の書影を取得する」から「巻情報を再取得」を実行します。

最後の操作は元に戻せません。移行画面が自動判定するのは、以前のManga Shelfが楽天の既知の画像ホストから取り込んだと確認できる添付ファイルだけです。手動でダウンロード・登録した画像や、別の提供元の画像は自動削除しません。

### 書影と権利について

楽天Books APIの書影は楽天または権利者が管理する情報です。Manga Shelfでは、APIから得た画像URLを外部参照し、画像を必ず楽天の商品ページへリンクし、漫画の個別・一覧・作者・ジャンル画面に公式の楽天Web Servicesクレジットを自動表示します。

楽天の書影をダウンロードして編集・加工・再アップロードする運用は避けてください。アイキャッチ画像を別途使う場合は、自作画像、出版社などから利用許諾を得た画像、または利用条件上掲載できる画像だけを登録してください。楽天Web Serviceの規約や提供条件が変更された場合は、最新の内容を確認してください。

## 10. よくある問題

### 漫画ページが404になる

「設定 → パーマリンク」で「変更を保存」を押し、リライトルールを更新します。

### 個別作品が通常投稿と同じ見た目になる

- WordPress 6.7以上か確認します。
- ブロックテーマの場合は「外観 → エディター → テンプレート」にManga Shelfのテンプレートがあるか確認します。
- テーマに`single-manga.html`または`single-manga.php`がある場合は、その内容が優先されていないか確認します。

### 巻一覧が表示されない

- 作品を「楽天から追加」で登録したか確認します。
- 楽天検索時に対象シリーズの巻が取得できたか確認します。
- 「漫画：巻一覧」ブロックがテンプレートまたは本文にあるか確認します。

### 各巻の書影が表示されない

- 0.1.5以前に登録した作品は「漫画 → 楽天書影の移行」を開き、「手順3：各巻の書影を取得する」から巻情報を再取得します。
- 巻一覧の内側に「漫画：各巻の書影」ブロックがあるか確認します。
- 楽天から書影URLまたは商品URLが提供されていない巻では、書影を表示しません。

### 更新後も楽天書影がアイキャッチ画像として表示される

- 「漫画 → 楽天書影の移行」で手順1を実行したか確認します。
- 「外観 → エディター → テンプレート」に保存済みのカスタマイズがある場合は、「アイキャッチ画像」を「漫画：楽天書影」へ置き換えます。
- テーマに`single-manga.html`、`archive-manga.html`、`single-manga.php`、`archive-manga.php`がある場合は、このマニュアルの例に合わせて書影の出力を変更します。

### 巻一覧が二重に表示される

サイトテンプレートと投稿本文の両方に「漫画：巻一覧」が配置されています。片方を削除してください。

### 楽天APIで403になる

- Access KeyとアプリケーションIDの組み合わせを確認します。
- 楽天アプリのアプリケーションURLを確認します。
- 「許可されたWebサイト」がWordPressサイトの正確なホスト名になっているか確認します。
- サブドメインを利用している場合は、サブドメインまで含めます。

## 11. データの削除

通常のアンインストールでは、漫画、巻情報、楽天API設定を保持します。すべて削除したい場合のみ、アンインストール前に`wp-config.php`へ次を追加します。

```php
define( 'MANGA_SHELF_DELETE_DATA', true );
```

この設定を有効にした状態でアンインストールするとデータは復元できません。事前にバックアップしてください。

## 参考資料

- [WordPressのテンプレート階層](https://developer.wordpress.org/themes/templates/template-hierarchy/)
- [プラグインからのブロックテンプレート登録](https://developer.wordpress.org/news/2024/08/registering-block-templates-via-plugins-in-wordpress-6-7/)
- [ブロックテーマのテンプレート](https://developer.wordpress.org/themes/templates/templates/)
