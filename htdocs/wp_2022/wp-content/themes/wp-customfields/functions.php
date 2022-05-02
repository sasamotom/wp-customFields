<?php
// --------------------------------------------------------------
// WordPress標準機能設定
// @codex https://wpdocs.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/add_theme_support
// --------------------------------------------------------------
function my_setup() {
  add_theme_support( 'post-thumbnails' ); /* アイキャッチ */
  add_theme_support( 'automatic-feed-links' ); /* RSSフィード */
  add_theme_support( 'title-tag' ); /* タイトルタグ自動生成 */
  add_theme_support( 'html5', array( /* HTML5のタグで出力 */
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
  ) );
}
add_action( 'after_setup_theme', 'my_setup' );


// ************************ セキュリティ設定 **********************************************
// --------------------------------------------------------------
// REST API設定（ユーザーの情報を取得不可とする）
// --------------------------------------------------------------
function wp_filter_rest_endpoints( $endpoints ) {
  // /* REST APIで投稿一覧取得を無効にする */
  // if ( isset( $endpoints['/wp/v2/posts'] ) ) {
  //     unset( $endpoints['/wp/v2/posts'] );
  // }
  // /* REST APIで投稿記事取得（単記事）を無効にする */
  // if ( isset( $endpoints['/wp/v2/posts/(?P<id>[d]+)'] ) ) {
  //     unset( $endpoints['/wp/v2/posts/(?P<id>[d]+)'] );
  // }
  /* REST APIでユーザー情報取得を無効にする */
  if ( isset( $endpoints['/wp/v2/users'] ) ) {
      unset( $endpoints['/wp/v2/users'] );
  }
  if ( isset( $endpoints['/wp/v2/users/(?P<id>[d]+)'] ) ) {
      unset( $endpoints['/wp/v2/users/(?P<id>[d]+)'] );
  }
  return $endpoints;
}
add_filter( 'rest_endpoints', 'wp_filter_rest_endpoints', 10, 1 );

// --------------------------------------------------------------
// v2/user/リダイレクト
// --------------------------------------------------------------
function disable_users_query() {
  if( preg_match('/wp\/v2\/users/i', $_SERVER['REQUEST_URI'])  || preg_match('/wp\/v2\/users/i', $_SERVER['QUERY_STRING']) ){
    wp_redirect( home_url() );
    exit;
  }
}
add_action('init', 'disable_users_query');

// --------------------------------------------------------------
// ユーザー一覧非表示
// --------------------------------------------------------------
if (!is_admin()) {
  // default URL format
  if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])) wp_redirect( home_url() );;
  add_filter('redirect_canonical', 'shapeSpace_check_enum', 10, 2);
}
function shapeSpace_check_enum($redirect, $request) {
  // permalink URL format
  if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) wp_redirect( home_url() );
  else return $redirect;
}


// ************************ 一般的な設定 **********************************************
// --------------------------------------------------------------
// CSSとJavaScriptの読み込み
// @codex https://wpdocs.osdn.jp/%E3%83%8A%E3%83%93%E3%82%B2%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3%E3%83%A1%E3%83%8B%E3%83%A5%E3%83%BC
// --------------------------------------------------------------
function my_script_init() {
  wp_enqueue_style( 'style-name', get_theme_file_uri() . '/_assets/css/style.css', array(), '1.0.0', 'all' );
  if (check_sub_site('site2')) {
    // site2の場合は、site2のCSSを読み込む
    wp_enqueue_style( 'site2', get_theme_file_uri() . '/_assets/css/site2.css', array('style-name'), '1.0.0', 'all' );
  }
  elseif (check_sub_site('site3')) {
    // site3の場合は、site3のCSSを読み込む
    wp_enqueue_style( 'site3', get_theme_file_uri() . '/_assets/css/site3.css', array('style-name'), '1.0.0', 'all' );
  }
  // wp_enqueue_script( 'script-name', get_theme_file_uri() . '/_assets/js/script.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'my_script_init' );

// --------------------------------------------------------------
// メニューの登録
// @codex https://wpdocs.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/register_nav_menus
// --------------------------------------------------------------
function my_menu_init() {
  register_nav_menus( array(
    'global'  => 'グローバルメニュー',
    // 'utility' => 'ユーティリティメニュー',
    // 'drawer'  => 'ドロワーメニュー',
    'site3nav' => 'サイト３メニュー',
    'site3recruit' => 'サイト３採用メニュー',
    'footNavi'  => 'フッターメニュー',
  ) );
}
add_action( 'init', 'my_menu_init' );

// --------------------------------------------------------------
// ウィジェットの登録
// @codex http://wpdocs.osdn.jp/%E9%96%A2%E6%95%B0%E3%83%AA%E3%83%95%E3%82%A1%E3%83%AC%E3%83%B3%E3%82%B9/register_sidebar
// --------------------------------------------------------------
function my_widget_init() {
  register_sidebar( array(
    'name'          => 'サイドバー',
    'id'            => 'sidebar',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ) );
}
add_action( 'widgets_init', 'my_widget_init' );

// --------------------------------------------------------------
// 画像サイズの追加
// --------------------------------------------------------------
// add_image_size( 'my_thumbnail', 420, 300, true );

//----------------------------------------------------------不要な読み込み整理
// --------------------------------------------------------------
// グーテンベルグ解除
// --------------------------------------------------------------
// add_filter('use_block_editor_for_post', '__return_false', 10);

// --------------------------------------------------------------
// 不要なところでは指定したプラグインを読み込まないように設定する
// --------------------------------------------------------------
function dequeue_plugins_style() {
    // プラグインIDを指定し解除する
    wp_dequeue_style('wp-block-library');
}
add_action( 'wp_enqueue_scripts', 'dequeue_plugins_style', 9999);
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_head','wp_generator');
remove_action( 'wp_head', 'rsd_link');
remove_action( 'wp_head', 'wp_shortlink_wp_head');
remove_action( 'wp_head', 'wlwmanifest_link');
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_enqueue_scripts', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_print_styles', 'print_emoji_styles', 10 );

// --------------------------------------------------------------
// 自動変換を無効
// --------------------------------------------------------------
remove_filter( 'the_title', 'wptexturize' );
remove_filter( 'the_content', 'wptexturize' );

function remove_cssjs_ver( $src ) { if( strpos( $src, '?ver=' ) ) $src = remove_query_arg( 'ver', $src ); return $src; } add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 ); add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );

// --------------------------------------------------------------
// ローカルのjQueryではなくCDNからjQueryを読み込む（ただしWP管理画面以外）
// --------------------------------------------------------------
function load_google_cdn() {
  if ( !is_admin() ){
    // jQueryを登録解除
    wp_deregister_script( 'jquery' );
    // Google CDNのjQueryを出力
    wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', array(), NULL, true );
  }
}
add_action('wp_enqueue_scripts', 'load_google_cdn');

// --------------------------------------------------------------
// ダッシュボードにて不要なものを表示しないようにする
// --------------------------------------------------------------
function remove_dashboard_widget() {
	remove_action( 'welcome_panel','wp_welcome_panel' );              // ようこそ
 	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );  // 概要
 	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );   // アクティビティ
 	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );  // クイックドラフト
 	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );      // WordPress イベントとニュース
}
add_action('wp_dashboard_setup', 'remove_dashboard_widget' );

// --------------------------------------------------------------
// コメント機能無効化
// --------------------------------------------------------------
add_filter( 'comments_open', '__return_false' );
//コメントHTMLタグエスケープ
// function html_to_text($comment_content) {
//   if ( get_comment_type() == 'comment' ) {
//     $comment_content = htmlspecialchars($comment_content, ENT_QUOTES);
//   }
//   return $comment_content;
// }
// add_filter('comment_text', 'html_to_text', 9);

// --------------------------------------------------------------
// 固定ページの自動整形無効化（pタグを自動で入らないようにする）
// --------------------------------------------------------------
function wpautop_filter($content) {
  global $post;
  $remove_filter = false;
  $arr_types = array('page'); // 自動整形を無効にする投稿タイプを記述
  $post_type = get_post_type( $post->ID );
  if (in_array($post_type, $arr_types)) {
    $remove_filter = true;
  }
  if ( $remove_filter ) {
    remove_filter('the_content', 'wpautop');
    remove_filter('the_excerpt', 'wpautop');
  }
  return $content;
}
add_filter('the_content', 'wpautop_filter', 9);

// --------------------------------------------------------------
// ビジュアルエディタから見出し1を削除
// --------------------------------------------------------------
function custom_tiny_mce_formats( $settings ){
  $settings[ 'block_formats' ] = '段落=p;見出し2=h2;見出し3=h3;見出し4=h4;見出し5=h5;見出し6=h6;整形済みテキスト=pre;';
  return $settings;
}
add_filter( 'tiny_mce_before_init', 'custom_tiny_mce_formats' );

// --------------------------------------------------------------
// 管理画面スタイル調整（AIOSEOの不要なものを非表示にする）
// --------------------------------------------------------------
function wp_custom_admin_css() { ?>
  <style type="text/css">
  #aioseo-settings {
    display:none;
  }
  </style>
  <?php }
add_action('admin_head', 'wp_custom_admin_css', 100);


// ************************ 追加の設定や関数 **********************************************
// --------------------------------------------------------------
// よく使用するショートコード
// --------------------------------------------------------------
// [url]・・・トップページのディレクトリ
function shortcode_url() {
  return get_bloginfo('url');
}
add_shortcode('url', 'shortcode_url');

// [temp]・・・テーマディレクトリ
function shortcode_templateurl() {
  return get_bloginfo('template_url');
}
add_shortcode('temp', 'shortcode_templateurl');

// [php file="xxxx"]     （xxxxのところに拡張子無しのphpファイル名を入力「/include/list.php」なら[php file="list"]）
function phpInclude($params = array()) {
  extract(shortcode_atts(array(
    'file' => 'default'
  ), $params));
  ob_start();
  include(get_theme_root() . '/' . get_template() . "/include/$file.php");
  return ob_get_clean();
}
add_shortcode('php', 'phpInclude');

// --------------------------------------------------------------
// bodyにスラッグ名クラスを追加（class="p-スラッグ名"）
// --------------------------------------------------------------
function pagename_class($classes) {
  if (is_page()) { //slugを追加
    $page = get_post(get_the_ID());
    $classes[] = 'p-'.$page->post_name;
  }
  elseif(get_post_type() === 'post') {
    $category = get_the_category();
    $classes[] .= 'p-'.$category[0]->slug;
  }
  elseif (is_post_type_archive() || is_single()) { //slugを追加
    global $post;
    $classes[] .= 'p-'.$post->post_type;
  }
  elseif (is_front_page()) {
    $classes[] = 'p-front-page';
  }
  elseif (is_search()) {
    $classes[] = 'p-search';
  }
  elseif (is_404()) {
    $classes[] = 'p-404';
  }
  if (wp_is_mobile()) {
    $classes[] .= 'mobile'; //mobileの場合classを追加
  }
  // どのサイトであるかの判断用
  if (check_sub_site('site2')) {
    $classes[] = 's-site2';
  }
  elseif (check_sub_site('site3')) {
    $classes[] = 's-site3';
  }
  else {
    $classes[] = 's-main';
  }
  //$classes[] .= '任意のクラス名'; //その他必要なクラス名があれば追加
  return $classes;
}
add_filter('body_class','pagename_class');

// --------------------------------------------------------------
// パンくずリスト作成
// --------------------------------------------------------------
function breadcrumb(){
  global $post;
  $title = '';
  $str ='';
  $contentNo = 1; // 何個目か
  if(!is_front_page() && !is_admin()) {
    // TOPページ（必ず表示）
    $str.= '<ol id="breadcrumb" class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li itemscope class="breadcrumb_item" itemprop="itemListElement" itemtype="https://schema.org/ListItem">';
    $str.= '<a itemprop="item" href="' . home_url() . '"><span itemprop="name">TOP</span></a><meta itemprop="position" content="1" /></li>';
    $contentNo++;

    // 以下条件分岐
    if(is_category()) { // カテゴリー
      $cat = get_queried_object();
      if($cat -> parent != 0){
        $ancestors = array_reverse(get_ancestors( $cat -> cat_ID, 'category' ));
        foreach($ancestors as $ancestor){
          $str.='<li itemscope class="breadcrumb_item" itemprop="itemListElement" itemtype="https://schema.org/ListItem">';
          $str.='<a itemprop="item" href="' . get_category_link($ancestor) . '"><span itemprop="name">'. get_cat_name($ancestor) .'</span></a><meta itemprop="position" content="' . (string)$contentNo .'" />';
          $str.='</li>';
          $contentNo++;
        }
      }
      $title = single_cat_title('',false);
    }
    elseif(is_tag()) { // タグ
      $title = single_tag_title('',false);
    }
    elseif(is_date()) {
      $title = get_the_time('Y年n月');
    }
    elseif(is_page()) { // 固定ページ
      if($post -> post_parent != 0 ){
        $ancestors = array_reverse(get_post_ancestors( $post->ID ));
        foreach($ancestors as $ancestor){
          $str.='<li itemscope class="breadcrumb_item" itemprop="itemListElement" itemtype="https://schema.org/ListItem">';
          $str.='<a itemprop="item" href="' . get_permalink($ancestor) . '"><span itemprop="name">'. preg_replace('/<span>(.*?)<\/span>/', '',get_the_title($ancestor)) .'</span></a><meta itemprop="position" content="' . (string)$contentNo .'" />';
          $str.='</li>';
          $contentNo++;
        }
      }
      $title = preg_replace('/<span>(.*?)<\/span>|<br.*>/', '', get_the_title());
    }
    elseif(is_singular('post')) { // ブログ投稿
      $categories = get_the_category($post->ID);
      $cat = $categories[0];
      if($cat -> parent != 0){
        $ancestors = array_reverse(get_ancestors( $cat -> cat_ID, 'category' ));
        foreach($ancestors as $ancestor){
          $str.='<li itemscope class="breadcrumb_item" itemprop="itemListElement" itemtype="https://schema.org/ListItem">';
          $str.='<a itemprop="item" href="' . get_category_link($ancestor) . '"><span itemprop="name">'. get_cat_name($ancestor) .'</span></a><meta itemprop="position" content="' . (string)$contentNo .'" />';
          $str.='</li>';
          $contentNo++;
        }
      }
      $str.='<li itemscope class="breadcrumb_item" itemprop="itemListElement" itemtype="https://schema.org/ListItem">';
      $str.='<a itemprop="item" href="' . get_category_link($cat -> term_id) . '"><span itemprop="name">'. $cat-> cat_name .'</span></a><meta itemprop="position" content="' . (string)$contentNo .'" />';
      $str.='</li>';
      $contentNo++;
      $title = preg_replace('/<br>/', '', get_the_title());
    }
    // 以下カスタム投稿
    elseif(is_tax()) { // タクソノミー
      $query = get_queried_object();
      $taxonomy_slug = $query->taxonomy;
      $terms = array_reverse(get_the_terms($post->ID, $taxonomy_slug));
      $term = $terms[0];
      if($term -> parent != 0){
        $ancestors = get_ancestors( $term -> term_id, $taxonomy_slug);
        foreach($ancestors as $ancestor){
        $term_name = get_term_by('term_taxonomy_id', $ancestor, $taxonomy_slug);
          $str.='<li itemscope class="breadcrumb_item" itemprop="itemListElement" itemtype="https://schema.org/ListItem">';
          $str.='<a itemprop="item" href="' . get_term_link($ancestor, $taxonomy_slug) . '"><span itemprop="name">'. $term_name->name .'</span></a><meta itemprop="position" content="' . (string)$contentNo .'" />';
          $str.='</li>';
          $contentNo++;
        }
      }
      $title = esc_html($query->name);
    }
    elseif(is_singular()) { // カスタム投稿
      $query = get_queried_object();
      $typelink = get_post_type_archive_link($query->post_type);
      $typename = get_post_type_object($query->post_type);
      $str.='<li itemscope class="breadcrumb_item" itemprop="itemListElement" itemtype="https://schema.org/ListItem">';
      $str.='<a itemprop="item" href="' . $typelink . '"><span itemprop="name">'. $typename->labels->name .'</span></a><meta itemprop="position" content="' . (string)$contentNo .'" />';
      $str.='</li>';
      $contentNo++;
      $title = preg_replace('/<br>/', '', get_the_title());
    }
    elseif (is_post_type_archive()) { // カスタム投稿アーカイブ
      $posttypeTitle = post_type_archive_title('', false );
      $title = esc_html($posttypeTitle);
    }
    elseif (is_404()) { // 404ページ
      $title = '404 ページが見つかりません';
    }
    else {
    }
    $str.='<li class="breadcrumb_item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span itemprop="name">'. $title .'</span><meta itemprop="position" content="' . (string)$contentNo .'"></li>';
    $str.='</ol>';
  }
  echo $str;
}

// --------------------------------------------------------------
// 記事抜粋の文字数設定
// --------------------------------------------------------------
function twpp_change_excerpt_length( $length ) {
  return 30;
}
add_filter( 'excerpt_length', 'twpp_change_excerpt_length', 999 );

// --------------------------------------------------------------
// ページネーション作成
// --------------------------------------------------------------
function category_link_custom( $query = array()) {
  // 子カテゴリーの404を回避
  if (isset($query['category_name']) && strpos($query['category_name'], '/') === false && isset($query['name'])) {
    $parent_category = get_category_by_slug($query['category_name']);
    if ($parent_category) {
      $child_categories = get_categories('child_of='.$parent_category->term_id);
      foreach ($child_categories as $child_category) {
        if ($query['name'] === $child_category->category_nicename) {
          $query['category_name'] = $query['category_name'].'/'.$query['name'];
          unset($query['name']);
        }
      }
    }
  }
  // カテゴリーのページ送りを修正して404を回避
  if(isset($query['name']) && $query['name'] === 'page' && isset($query['page'])) {
    $paged = $query['page'];
    if(is_numeric($paged)) {
      $query['paged'] = (int) $paged;
      unset($query['name']);
      unset($query['page']);
    }
  }
  return $query;
}
add_filter('request', 'category_link_custom');

function pagination($pages = '', $range = 1, $anchor = '') {
  // 引数  pages 全ページ数
  //      range 現在のページ番号を中心にした時前後にいくつずつ表示するか
  //      anchor  リンク先にアンカーをつけたい場合は指定する
  global $paged;  // 現在のページ番号
  if (empty($paged)) {
    $paged = 1; // デフォルトのページ
  }
  if ($pages == '') {
    global $wp_query;
    $pages = $wp_query->max_num_pages;  // 全ページ数を取得
    if(!$pages) {
      $pages = 1; // 全全ページ数が空の場合は、１とする
    }
  }
  $str = '';    // ページネーションのHTML
  if(1 != $pages) {
    // 全ページ数が１でない場合はページネーションを表示する
    $str.= '<ol class="pagination">';
    // 最初のページへのリンク
    $str.= '<li class="first"><a href="' . get_pagenum_link(1).$anchor . '">FIRST</a></li>';
    // 一つ前のページへのリンク
    $str.= '<li class="prev">';
    if (1 < $paged) {
      $str.= '<a href="' . get_pagenum_link($paged - 1).$anchor .'">Prev</a>';
    }
    else {
      $str.= '<span>Prev</span>';
    }
    $str.= '</li>';
    // ページ番号のリンク
    for ($i = $paged - $range; $i < $paged; $i++) {
      if (0 < $i) {
        $str.= '<li><a href="' . get_pagenum_link($i).$anchor . '">' . (string)$i . '</a></li>';
      }
    }
    $str.= '<li><span>' . (string)$paged . '</span></li>';
    for ($i = $paged + 1 ; $i <= $paged + $range; $i++) {
      if ($i <= $pages) {
        $str.= '<li><a href="' . get_pagenum_link($i).$anchor . '">' . (string)$i . '</a></li>';
      }
    }
    // 次のページへのリンク
    $str.= '<li class="next">';
    if ($paged < $pages) {
      $str.= '<a href="' . get_pagenum_link($paged + 1).$anchor .'">Next</a>';
    }
    else {
      $str.= '<span>Next</span>';
    }
    $str.= '</li>';
    // 最後のページへのリンク
    $str.= '<li class="last"><a href="' . get_pagenum_link($pages).$anchor . '">LAST</a></li>';
    $str.= '</ol>';
  }
  echo $str;
}

// --------------------------------------------------------------
// メニュー設定（自動付加される不要なクラスやIDを削除）
// --------------------------------------------------------------
function my_nav_menu_id( $menu_id ){
  // liタグのidを削除
  $id = NULL;
  return $id;
}
add_filter( 'nav_menu_item_id', 'my_nav_menu_id' );

function my_nav_menu_class( $classes, $item ) {
  // 管理画面からメニューにclassを設定した場合
  if( isset($classes[0]) ) {
    // 管理画面から設定したclass以外を削除
    array_splice( $classes, 1 );
  }
  else {
    // 上記以外の場合は、すべてのclassを削除
    $classes = [];
  }
  // 現在のページのliタグの場合
  if( $item -> current == true ) {
    // classの値に-currentを付与
    $classes[] = '-current';
  }
  return $classes;
}
add_filter( 'nav_menu_css_class', 'my_nav_menu_class', 10, 2 );

// --------------------------------------------------------------
// 新着情報
// --------------------------------------------------------------
// function getNewItems($atts) {
//   extract(shortcode_atts(array(
//       "num" => '',	//最新記事リストの取得数
//       "cat" => '',	//表示する記事のカテゴリー指定
//       "count" => '10',
//       "tag" => ''
//   ), $atts));
//   global $post;
//   $oldpost = $post;
//   // $myposts = get_posts('numberposts='.$num.'&order=asc&orderby=menu_order&post_type='.$cat.'&tag='.$tag);
//   $myposts = get_posts('numberposts='.$num.'&order=DESC&orderby=date&post_type='.$cat.'&tag='.$tag);
//   if (! $myposts) {
//     return false;
//   }
//   else {
//     $retHtml='<section class="sec-news" id="news">'.
//     '<h2 class="sec-ttl">お知らせ<span class="sec-ttl__en">NEWS</span></h2>'.
//     '<ul class="news-list">';
//     $count=1;
//     foreach($myposts as $post) :
//       $cat = get_the_category();
//       $fieldData = get_field('check_new1');
//       $catname = $cat[0]->cat_name;
//       $catslug = $cat[0]->slug;
//       $post_time = (integer) get_post_time( 'U', false );
//       $days = 7; //New!を表示させる日数
//       $last = time() - ($days * 24 * 60 * 60);
//       $tags = get_the_tags();

//       setup_postdata($post);
//       // if ($post_time > $last) {
//       if ($fieldData && $count < 4) {
//           $retHtml.='<li class="news-list__item new-news">';
//       }
//       else {
//         $retHtml.='<li class="news-list__item">';
//       }
//       $retHtml.='<dl class="news-detail">
//       <dt class="news-detail__day"><time datetime="'.get_post_time('Y-m-d').'">'.get_post_time( get_option( 'date_format' )).'</time></dt>
//       <dd class="news-detail__txt"><a class="breadcrumb__link" href="'.esc_url( get_permalink()).'" class="news-detail__txt--link">'.esc_html(get_the_title("","",false)).'</a>';
//       // foreach ( $tags as $tag ) :
//       // $retHtml.='<a href="'. get_tag_link($tag->term_id) .'"><span class="'.$tag->slug.'">'.$tag->name.'</span></a>';
//       // endforeach;
//       $retHtml.='</dd></dl></li>';
//       $count++;
//     endforeach;
//     $retHtml.='</ul>'.'<div class="green-btn"><a href="/news/">お知らせ一覧を見る</a></div>'.'
//     </section>';
//     $post = $oldpost;
//     wp_reset_postdata();
//     return $retHtml;
//   }
// }
// add_shortcode("news", "getNewItems");

// --------------------------------------------------------------
// 画像の幅&高さを取得する
// --------------------------------------------------------------
function get_image_size($image_url){
  $res = null;
  $wp_content_dir = WP_CONTENT_DIR;
  $wp_content_url = content_url();
  $image_file = str_replace($wp_content_url, $wp_content_dir, $image_url);
  // 画像サイズを取得
  $imagesize = getimagesize($image_file);
  if ($imagesize) {
    $res = array();
    $res = "width=".$imagesize[0]." height=".$imagesize[1];
    // $res['width'] = $imagesize[0];
    // $res['height'] = $imagesize[1];
    return $res;
  }
}

// --------------------------------------------------------------
// 親ページのIDを取得する
// --------------------------------------------------------------
function get_page_parent( $parent_id , $object = true , $root = true ) {
  // parent_idが0の場合何もしない
  if( $parent_id == false ) {
    return false;
  }

  if( $object == true ) { // 返り値がpostオブジェクト
    while( $parent_id ) {
      $page = get_post( $parent_id );
      $result[] = $page;
      $parent_id = $page->post_parent;
    }
  }
  else { // 返り値がpostID
    while( $parent_id ) {
      $page_id = get_post_field( 'post_parent' , $parent_id );
      $result[] = $parent_id;
      $parent_id = $page_id;
    }
  }
  // 配列を逆順に(rootを0に)
  $result = array_reverse( $result );

  // rootがtureの場合0番目(rootページのみ)をセット
  if( $root == true) {
    $result = $result[0];
  }
  return $result;
}

// --------------------------------------------------------------
// 投稿にて一番始めに出てくる画像のURLを取得
// --------------------------------------------------------------
function first_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  if (count($matches[1]) > 0) {
    $first_img = $matches[1][0];
  }
  // if(empty($first_img)){
  //   // 記事内で画像がなかったときのためのデフォルト画像を指定
  //   $first_img = "/images/default.jpg";
  // }
  return $first_img;
}

// --------------------------------------------------------------
// 返信メールに送信日時を表示
// --------------------------------------------------------------
date_default_timezone_set('Asia/Tokyo');  // タイムゾーンの指定
function send_date_time( $value, $key, $insert_contact_data_id ) {
  if ( $key === 'send_datetime' ) {
    return date( 'Y/m/d l H:i:s' );
  }
  return $value;
}
add_filter( 'mwform_custom_mail_tag', 'send_date_time', 10, 3 );

// --------------------------------------------------------------
// 本番環境でプレビュー表示
// --------------------------------------------------------------
// function replace_preview_post_link ( $url ) {
//   $replace_url = str_replace('https://www.ceremonykiuchi.com', 'https://ceremonykiuchi.sakuraweb.com/wp_2021', $url);
//   return $replace_url;
// }
// add_filter('preview_post_link', 'replace_preview_post_link');

// --------------------------------------------------------------
// 機能： サブディレクトリ内のサブサイトであるか調べる
// 引数： $directory_name サブディレクトリ名を指定する
// 戻値： true サブサイトである　 / false サブサイトではない
// --------------------------------------------------------------
function check_sub_site($directory_name) {
  // 検索画面の場合はサブディレクトリではない
  if (is_search()) {
    return false;
  }
  // URLの最初がサブサイトのディレクトリ名かどうかをチェックする
  $this_page = str_replace(home_url(), '', get_the_permalink());
  if (strpos($this_page, '/'.$directory_name) === 0) {
    return true;
  }
  return false;
}

// --------------------------------------------------------------
// いいねボタンを使用するページでAjaxを使用可能とする
// --------------------------------------------------------------
function my_enqueue() {
  if (is_singular('item') ) {
    // Ajaxの処理を書いたjsの読み込み
    wp_enqueue_script( 'ajax-script', get_theme_file_uri() . '/_assets/ajax/ajax.js', array('jquery'), null, true );
    // 「ad_url.ajax_url」のようにしてURLを指定できるようになる
    wp_localize_script( 'ajax-script', 'ad_url',array( 'ajax_url' => admin_url( 'admin-ajax.php') ) );
  }
}
add_action( 'wp_enqueue_scripts', 'my_enqueue' );

// --------------------------------------------------------------
// 検索設定（かなりエグめ。多分、他の人には調整できない）
// --------------------------------------------------------------
// JOIN
function my_search_join($join) {
  global $wpdb;

  if( is_search() ) {
    $join .= " LEFT JOIN wp_term_relationships
      ON ({$wpdb->posts}.ID = wp_term_relationships.object_id) ";
  }
  return $join;
}
add_filter('posts_join', 'my_search_join');

// GROUP BY
function my_search_groupby($groupby) {
  global $wpdb;

  if( !is_search() ) {
    return $groupby;
  }

  // 投稿IDでグルーピング
  $mygroupby = "{$wpdb->posts}.ID";

  // グルーピング済の場合は現状のグルーピングを返す
  if( preg_match( "/$mygroupby/", $groupby )) {
    return $groupby;
  }

  // グルーピングする項目が現状ない場合は、自作グルーピングを返す
  if(!strlen(trim($groupby))) {
    return $mygroupby;
  }

  // 既にグルーピングする項目がある場合は、','をつけて返す
  return $groupby . ", " . $mygroupby;
}
add_filter('posts_groupby', 'my_search_groupby' );

// WHERE
function my_search($search, $query) {
  // 検索ページ以外は変更しない
  if (!$query->is_search) {
    return;
  }

  global $wp_query;
  // キーワードがなくても検索ページを表示させる
  if (isset($wp_query->query['s'])) {
    $wp_query->is_search = TRUE;
  }

  global $wpdb;
  $search = '';   // 追加するクエリ条件式

  // タクソノミーの条件追加
  if(!empty($_REQUEST['tax'])) {
    $taxArr = [];   // タクソノミーの配列
    $termArr = [];  // termの配列
    $pre_taxslug = "";
    $pre_termArr = [];
    foreach ($_REQUEST['tax'] as $t) {
      // 取得した文字列から、タクソノミー配列＆term配列を作成する
      $info = explode("^", $t);
      if (count($info) === 2) {
        $taxslug = $info[0];
        $termid = $info[1];
        // 最初or次のタクソノミーの時
        if ($pre_taxslug !== $taxslug && $pre_taxslug !== '') {
          // 直前の情報を保存
          array_push($taxArr, $pre_taxslug);
          array_push($termArr, $pre_termArr);
          $pre_termArr = [];
          array_push($pre_termArr, $termid);
          $pre_taxslug = $taxslug;
        }
        else {
          array_push($pre_termArr, $termid);
          $pre_taxslug = $taxslug;
        }
      }
    }
    array_push($taxArr, $pre_taxslug);
    array_push($termArr, $pre_termArr);

    $terms = '';    // 指定されたターム
    $taxs = '';     // 指定されたタクソノミー
    for ($i = 0; $i < count($taxArr); $i++) {
      if (!in_array('ALL', $termArr[$i])) {
        // 「全て」では無い場合（絞り込む対象を取得）
        foreach($termArr[$i] as $t) {
          if ($terms !== '') {
            $terms = $terms . ',';
          }
          $terms = $terms . $t;
        }
        if ($taxs !== '') {
          $taxs = $taxs . ',';
        }
        $taxs = $taxs . "'" . $taxArr[$i] . "'";
      }
    }
    if ($taxs !== '') {
      $search.= " AND (
        wp_term_relationships.term_taxonomy_id IN ({$terms})
        OR NOT EXISTS (
        SELECT 1
        FROM wp_term_relationships
        INNER JOIN wp_term_taxonomy
        ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
        WHERE wp_term_taxonomy.taxonomy IN ({$taxs})
        AND wp_term_relationships.object_id = wp_posts.ID)
      ) ";
    }
  }

  // カスタムフィールドの内容も検索結果に含める
  $search_words = explode(' ', isset($wp_query->query_vars['s']) ? $wp_query->query_vars['s'] : '');
  if (count($search_words) > 0) {
    // $search .= "AND post_type = 'course'";     // post_typeを指定したい場合はここで条件追加
    foreach ($search_words as $word) {
      if (!empty($word)) {
        $search_word = '%' . esc_sql($word) . '%';
        $search .= " AND (
          {$wpdb->posts}.post_title LIKE '{$search_word}'
          OR {$wpdb->posts}.post_content LIKE '{$search_word}'
          OR {$wpdb->posts}.ID IN (
            SELECT distinct post_id
            FROM {$wpdb->postmeta}
            WHERE meta_value LIKE '{$search_word}'
            )
        ) ";
      }
    }
  }

  // 期間検索の条件式追加
  if (!empty($_REQUEST['date_from']) && !empty($_REQUEST['date_to'])) {
    $from = "'". date('Y-m-d',  strtotime($_REQUEST['date_from'])). "'";
    $to   = "'". date('Y-m-d',  strtotime($_REQUEST['date_to'])). "'";
    $search .= " AND DATE(post_date) BETWEEN $from AND $to";
  }
  return $search;
}
add_filter('posts_search','my_search', 10, 2);


// --------------------------------------------------------------
// 【検索】検索フィルターの追加
// --------------------------------------------------------------
function search_filter($query) {
  if (!is_admin() && $query->is_main_query() && $query->is_search()) {
    // post_type条件の追加
    if(!empty($_REQUEST['post_type'])) {
      $pt = $_REQUEST['post_type'];
      array_push($pt, 'page');    // 固定ページも検索対象
      $query->set('post_type', $pt);
    }
    // 固定ページ条件の追加（検索対象外固定ページを指定する）
    // 固定ページ一覧を作成
    $args = array(
      'post_type' => 'page', //固定ページ
      'posts_per_page' => -1 //これを入れないと固定ページ数分表示されない
    );
    $my_posts = get_posts($args);
    $allpages = [];
    if ($my_posts) {
      global $post;
      foreach($my_posts as $post) {
        setup_postdata($post);
        array_push($allpages, $post->ID);
      }
      wp_reset_postdata();
    }
    // 検索対象のページ一覧を作成
    $ids = '';
    if (!empty($_REQUEST['pages'])) {
      foreach ($_REQUEST['pages'] as $pages) {
        if ($ids !== '') {
          $ids = $ids . ',';
        }
        $ids = $ids . $pages;
      }
    }
    $showpages = explode(',',$ids);   // 検索対象に入れたい固定ページ
    // 全ての固定ページと検索対象に入れたい固定ページから、削除したい対象から削除したい固定ページを求める
    $hiddenpages = array_diff($allpages, $showpages);
    if (count($hiddenpages) > 0) {
      // 検索対象から削除したい固定ページのIDを登録
      $query->set('post__not_in', $hiddenpages);
    }

    // 検索結果の１ページあたりの表示件数指定
    $query->set('posts_per_page', -1);
  }
  return $query;
}
add_action( 'pre_get_posts','search_filter' );

// --------------------------------------------------------------
// 【検索】検索から除外するページを指定する
// --------------------------------------------------------------
// function search_filter($query) {
//   if (!is_admin() && $query->is_main_query() && $query->is_search()) {
//     $query->set('post__not_in', array(2, 30, 78));  // 除外したいページのpostIDを配列に入れる
//   }
//   return $query;
// }
// add_action( 'pre_get_posts','search_filter' );

// ************************ カスタムフィールド **********************************************
// --------------------------------------------------------------
// ACF関連のファイル読み込み
// --------------------------------------------------------------
require_once ( 'acf/acf-faq.php' );
require_once ( 'acf/acf-course.php' );
require_once ( 'acf/acf-item.php' );
require_once ( 'acf/acf-news.php' );
require_once ( 'acf/acf-feature_story.php' );
require_once ( 'acf/acf-menu.php' );
require_once ( 'customfield/cf-coupon.php' );

?>
