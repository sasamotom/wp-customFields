<?php get_header(); ?>
<?php
  $str = $_GET['s']; // 検索文字列の取得
  $e_str = htmlspecialchars($str, ENT_QUOTES, "utf-8"); // 検索文字列のエスケープ処理
  $page_title = !empty($str) ? esc_html(get_search_query())."の検索結果": "検索結果";
?>

<header class="searchHeader">
  <h1 class="searchHeader_ttl">検索結果ページ</h1>
</header>

<main>
  <section class="sec-searchResult">
    <!-- ここに検索結果を表示する -->
    <div class="container">
    <!-- <h2><?php echo $page_title ?></h2> -->
    <p class="resultCnt">検索結果：全<span><?php echo $wp_query->found_posts; ?></span>件</p>
    <ul class="resultList">
<?php
// var_dump($wp_query->request);
  if (have_posts()) :

    while ( have_posts() ) :
      the_post();
      $post_name = '';
      switch ($post->post_type) {
        case 'page':
          $post_name = '';
          break;
        case 'post':
          $post_name ='BLOG';
          break;
        default:  // カスタム投稿タイプ
          $post_name = esc_html(get_post_type_object($post->post_type)->label);
          break;
      }
      // var_dump($post);
 ?>
      <li>
        <a href="<?php echo esc_url(get_permalink()); ?>">
          <p class="resultList_date">
            <time datatime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y/n/j'); ?></time>
            <?php if ($post_name): ?>
            <span class="postName"><?php echo $post_name; ?></span>
            <?php endif; ?>
          </p>
          <p class="resultList_ttl"><?php esc_html(the_title()); ?></p>
          <p class="resultList_txt"><?php esc_html(the_excerpt()); ?></p>
        </a>
      </li>
<?php
    endwhile;
  else:
?>
      <li>検索結果はありません。</li>
<?php
  endif;
?>
    </ul>
  </div>
</section>

<form method="get" class="searchForm" action="" id="searchForm" role="search" action="<?php echo esc_url(home_url('/')); ?>">
  <section class="sec-searchItem">
    <div class="container">
      <h2 class="searchSecTtl">キーワード指定</h2>
      <input class="searchTextBox" type="search" name="s" placeholder="検索したい言葉を入力してください" value="<?php echo get_search_query(); ?>"><br>
    </div>
  </section>
  <section class="sec-searchItem">
    <div class="container">
      <h2 class="searchSecTtl">詳細条件指定</h2>
<?php
  $post_info = [
    [
      'site' => 'パステルアート教室（main site）',
      'site_slug' => 'main',
      'site_post_id' => '-1',
      'post' => ['course', 'faq', 'post'],
      'pages' => '2,35' // 検索対象となる固定ページ（この場合、TOPページとパステルアートとはページを対象とし、講座申込は除外している）
    ],
    [
      'site' => 'アクセサリーブランド（site2）',
      'site_slug' => 'site2',
      'site_post_id' => '117',
      'post' => ['news', 'item', 'feature_story'],
      'pages' => '117,121,264'
    ],
    [
      'site' => 'DONUTS SHOP（site3）',
      'site_slug' => 'site3',
      'site_post_id' => '329',
      'post' => ['menu'],
      'pages' => '329,402,332,339,413,415,341'
    ],
  ];
?>
      <ul class="searchKindList">
        <li>
          <h3 class="searchKindList_ttl">サイト / ページ種別 / カテゴリ・タグ</h3>
          <ul class="searchCheckList -site">
            <!-- <input type="hidden" name="cat_menu[]" value="fuwafuwa"> -->
            <!-- <input type="hidden" name="cat_menu[]" value="limited"> -->
            <!-- <input type="hidden" name="cat_item[]" value="ring"> -->
<?php
  foreach ($post_info as $info) :
?>
            <li>
              <input type="checkbox" checked name="site[]" value="<?php echo $info['site_post_id']; ?>" id="site-<?php echo $info['site_slug']; ?>"><label for="site-<?php echo $info['site_slug']; ?>"><?php echo $info['site']; ?></label>
              <ul class="searchCheckList">
<?php
    // 投稿タイプ一覧表示
    foreach ($info['post'] as $p) :
      $posttype = get_post_type_object($p);         // post_type
      $taxs = get_object_taxonomies($p, 'objects'); // post_typeの投稿タイプで使用しているタクソノミー
?>
                <li>
                  <input type="checkbox" checked name="post_type[]" value="<?php echo $p; ?>" id="post_<?php echo $p; ?>"><label for="post_<?php echo $p; ?>"><?php echo $posttype->label; ?></label>
<?php
      if ($taxs && $p !== 'post') :  // タクソノミーを使用している場合
        // タクソノミーの値を一覧表示
        foreach ($taxs as $taxname => $t) :
          $args = array(
            'orderby'       => 'menu_order',
            'order'         => 'DESC',
          );
          $terms = get_terms($taxname, $args);
          if (count($terms) > 1) :
?>
                  <ul class="searchCheckList -child">
<?php
            $checkname = 'tax[]';
            if (count($taxs) > 1) :
              // カスタムタクソノミーとタグを使用している場合にはそれぞれの名称を表示する
              if ($t->name === 'post_tag') :
                $checkname = 'tag[]';
              else:   //// タグは表示しない
?>
                    <!-- <li><span class="childKindName"><?php echo $t->label; ?>：</span></li> -->
<?php
              endif;   //// タグは表示しない
            endif;
            if ($checkname !== 'tag[]') :   //// タグは表示しない
?>
                    <li><input type="checkbox" checked name="<?php echo $checkname; ?>" value="<?php echo $taxname; ?>^ALL" id="<?php echo $checkname . $taxname; ?>"><label for="<?php echo $checkname . $taxname; ?>">全て</label></li>
<?php
            endif;   //// タグは表示しない
            foreach($terms as $tm) :
              if ($checkname !== 'tag[]') :   //// タグは表示しない
?>
                    <li><input type="checkbox" checked name="<?php echo $checkname; ?>" value="<?php if ($t->name !== 'post_tag') { echo $taxname.'^'; }?><?php echo $tm->term_id; ?>" id="<?php echo $tm->slug; ?>"><label for="<?php echo $tm->slug; ?>"><?php echo $tm->name; ?></label></li>
<?php
              endif;   //// タグは表示しない
            endforeach;
?>
                  </ul>
<?php
          endif;
        endforeach;
      endif;
?>
                </li>
<?php
    endforeach;
?>
                <li><input type="checkbox" checked name="pages[]" value="<?php echo $info['pages']; ?>" id="other-<?php echo $info['site_slug']; ?>"><label for="other-<?php echo $info['site_slug']; ?>"">上記以外</label></li>
              </ul>
            </li>
<?php
  endforeach;
?>
          </ul>
        </li>
        <li>
          <h3 class="searchKindList_ttl">投稿期間</h3>
          <input type="date" name="date_from">　〜　<input type="date" name="date_to">
        </li>
      </ul>
    </div>
  </section>
  <div class="sec-searchItem">
    <div class="container">
      <button class="searchBtn" type="submit">検索する</button>
    </div>
  </div>
</form>

<div class="sec-searchDescription">
  <div class="container">
    <p>【自分用MEMO】検索ページ、こんな仕様。</p>
    <p>
      キーワード有無に関わらず、詳細条件でフィルタをかけた状態で検索する。表示している項目全てを重ねて検索条件として設定できるはず。<br>
      カスタム投稿タイプを選んで検索できる。「上記以外」は固定ページ。（ただしフォーム関連のページは除外した）<br>
      カスタムタクソノミーを選んで検索できる。「全て」を選んだ場合、そのカスタム投稿タイプ全てが検索対象となる。（ラジオボタンにすべきだった。）<br>
      検索結果は全結果を１ページで表示するようにした。<br>
      処理はfunctions.phpにめっちゃ追加した。<br>
      正直、それほど完璧なテストを行なっていないため、どこかバグっているかも。。。検索機能の仕様や編集すべき場所は理解できたから、良しとしちゃった。<br>
      タグ（site2のItemで使用している）でも検索できるようにしたかったが、カスタムタクソノミーを検索条件に含めた時点で異常な複雑さだったためやめた。<br>
      もう、SQLの知識がないと構築不可なレベルになっている。こんなの、SQL知らない人が自分で構築するの無理じゃない？どうやってやるんだろ。知っててよかったSQL。<br>
      一応、"var_dump($wp_query->request);"にて、実行するSQL文がわかるので、構築中は役に立った。<br>
      ・・・複雑な検索機能は、プラグインを使った方が良さそうね！<br>
      ただ、今村さんが前に「WPの標準機能で検索するとくっそ遅い」と強めに言っていたが、それほど気にならなかった。
    </p>
  </div>
</div>
</main>

<?php get_footer(); ?>
