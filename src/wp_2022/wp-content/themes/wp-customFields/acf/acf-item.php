<?php
//----------------------------------------------
// カスタム投稿を定義
//----------------------------------------------
function create_post_type_item() {
  $label = 'Item';
  $slug = 'item';
  $tax = 'cat_item';
  $taxName = 'Itemカテゴリ';
  $labels = [
    'name' => $label,
    'singular_name' => $label,
    'all_items' => $label.'一覧',
    'add_new_item' => $label.'を追加',
    'add_new' => '新規追加',
    'new_item' => $label,
    'view_item' => $label.'を表示',
    'not_found' => $label.'がありません',
    'not_found_in_trash' => 'ゴミ箱に'.$label.'情報はありません。',
    'search_items' => $label.'を検索',
  ];
  $args = [
    'labels' => $labels,
    'public' => true,
    'show_ui' => true,
    'query_var' => true,
    'hierarchical' => true, // trueでカテゴリータイプ
    'has_archive' => true,  // アーカイブページを有効にするかどうか。初期値: false
    'update_count_callback' => '_update_post_term_count',
    'menu_position' => 5,
    'supports' => [
      'title',
      // 'editor',
      'thumbnail',
      'custom-fields',
      // 'excerpt',
      // 'author',
      'revisions',
      //'page-attributes',
    ],
    'rewrite' => ["slug" => "site2/item", "with_front" => false],
  ];
  register_post_type($slug, $args);

  // カスタム投稿にカスタムタクソノミーを関連づける
  register_taxonomy(
    $tax,                 // タクソノミーの名前
    $slug,                // このタクソノミーをこのカスタム投稿に設定する
    array(
      'labels' => array(
        'name' => $taxName  // 管理画面で表示する分類名
      ),
      'hierarchical' => true,           // true  階層を持たせられる
      'show_admin_column' => true,
      'rewrite' => array('slug' => 'site2/item/category', 'with_front' => false)
    )
  );
  register_taxonomy_for_object_type($tax, $slug);
  register_taxonomy_for_object_type('post_tag', $slug); // 「投稿」のタグ
  // register_taxonomy_for_object_type('category', $slug); // 「投稿」のカテゴリー
}
add_action( 'init', 'create_post_type_item' );

//----------------------------------------------
// アーカイブページにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_tag_archive_item( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_tag()) {
    $wp_query->set( 'post_type', array('post','item'));
  }
}
add_action( 'pre_get_posts', 'add_post_tag_archive_item');

//----------------------------------------------
// カテゴリーアーカイブにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_category_archive_item( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_category()) {
  $wp_query->set( 'post_type', array('post','item'));
  }
}
add_action( 'pre_get_posts', 'add_post_category_archive_item');

//----------------------------------------------
// カスタムタクソノミーを定義
//----------------------------------------------
function add_custom_taxonomies_term_filter_item() {
  global $post_type;
  if ( $post_type == 'item' ) {
    $taxonomy = 'cat_item';
    wp_dropdown_categories( array(
      'show_option_all' => 'すべてのカテゴリー',
      'orderby'         => 'name',
      'selected'        => get_query_var( $taxonomy ),
      'hide_empty'      => 0,
      'name'            => $taxonomy,
      'taxonomy'        => $taxonomy,
      'value_field'     => 'slug',
    ) );
  }
}
add_action( 'restrict_manage_posts', 'add_custom_taxonomies_term_filter_item' );

//----------------------------------------------
// URLを変更したタクソノミーアーカイブページへアクセスできるようにする
//----------------------------------------------
function url_rewrite_rules_item() {
  add_rewrite_rule('site2/item/category/([^/]+)/page/([0-9]+)/?$', 'index.php?cat_item=$matches[1]&paged=$matches[2]', 'top');
  add_rewrite_rule('site2/item/category/([^/]+)/?$', 'index.php?cate_item=$matches[1]', 'top');
}
add_action( 'init', 'url_rewrite_rules_item' );

//----------------------------------------------
// カスタムタクソノミーのリンク調整
//----------------------------------------------
function rewrite_term_links_item($termlink, $term, $taxonomy) {
  return ($taxonomy === 'news_cat' ? home_url('/site2/item/category/'. $term->slug) : $termlink);
}
add_filter( 'term_link', 'rewrite_term_links_item', 10, 3 );

//----------------------------------------------
// ショートコード
//----------------------------------------------
// 最新アイテム
function getNewItemList($atts) {
  extract(shortcode_atts(array(
    "num" => '4',         // 取得記事数
    "cat" => 'item',       // 表示する記事のpost_type
    "tag" => '',
    "ttl" => '最新アイテム',
  ), $atts));
  global $post;
  $oldpost = $post;
  $myposts = get_posts('numberposts='.$num.'&order=desc&orderby=date&post_type='.$cat.'&tag='.$tag);
  if (!$myposts) {
    return false;
  }
  else {

    $retHtml='<section class="sec-Items">
    <div class="container">
      <h2 class="secTtl">';
      $retHtml.=$ttl;
      $retHtml.='</h2>
      <ul class="itemList">';
    foreach($myposts as $post) :
      $images = get_field('images');
      $imgPath = esc_url($images[0]);
      $name  = esc_html(get_the_title("","",false));
      $price  = esc_html(get_field('price'));
      setup_postdata($post);
      $retHtml.='<li><a href="'.esc_url(get_permalink()).'"><dl class="itemDtl">';
      $retHtml.='<dd class="itemDtl_pic"><img src="'.$imgPath.'" alt="'.$name.'" width="400" height="400" loading="lazy" /></dd>';
      $retHtml.='<dt class="itemDtl_name">'.$name.'</dt>';
      $retHtml.='<dd class="itemDtl_price">'.$price.'</dd>';
      $retHtml.='</dl></a></li>';
    endforeach;
    $retHtml.='</ul>
      <p class="btn"><a href="/site2/item/">アイテム一覧へ</a></p>
    </div>
    </section>';
    $post = $oldpost;
    wp_reset_postdata();
    return $retHtml;
  }
}
add_shortcode("newItemList", "getNewItemList"); // [newItemList]で呼び出せる

// 人気アイテム
function getPopularItemList($atts) {
  extract(shortcode_atts(array(
    "num" => '4',         // 取得記事数
    "cat" => 'item',       // 表示する記事のpost_type
    "tag" => '',
  ), $atts));
  global $post;
  $oldpost = $post;
  $myposts = get_posts('numberposts='.$num.'&meta_key=good&order=desc&orderby=meta_value_num&post_type='.$cat.'&tag='.$tag);
  if (!$myposts) {
    return false;
  }
  else {
    $retHtml='<section class="sec-Items">
    <div class="container">
      <h2 class="secTtl">人気アイテム</h2>
      <ul class="itemList">';
    foreach($myposts as $post) :
      $images = get_field('images');
      $imgPath = esc_url($images[0]);
      $name  = esc_html(get_the_title("","",false));
      $price  = esc_html(get_field('price'));
      setup_postdata($post);
      $retHtml.='<li><a href="'.esc_url(get_permalink()).'"><dl class="itemDtl">';
      $retHtml.='<dd class="itemDtl_pic"><img src="'.$imgPath.'" alt="'.$name.'" width="400" height="400" loading="lazy" /></dd>';
      $retHtml.='<dt class="itemDtl_name">'.$name.'</dt>';
      $retHtml.='<dd class="itemDtl_price">'.$price.'</dd>';
      $retHtml.='</dl></a></li>';
    endforeach;
    $retHtml.='</ul>
      <p class="btn"><a href="/site2/item/">アイテム一覧へ</a></p>
    </div>
    </section>';
    $post = $oldpost;
    wp_reset_postdata();
    return $retHtml;
  }
}
add_shortcode("popularItemList", "getPopularItemList"); // [popularItemList]で呼び出せる

// liタグの中身を取得する
function getInnerLiOfItem($atts) {
  $atts = shortcode_atts(
    array(
      'id' => '0',
    ),
  $atts);
  global $post;
  $oldpost = $post;
  $postId = $atts['id'] ;

  $mypost = get_post($postId);
  if (!$mypost) {
    return false;
  }
  $post = $mypost;
  $retHtml='';
  $images = get_field('images');
  $imgPath = esc_url($images[0]);
  $name  = esc_html(get_the_title("","",false));
  $price  = esc_html(get_field('price'));
  setup_postdata($post);
  $retHtml.='<a href="'.esc_url(get_permalink()).'"><dl class="itemDtl">';
  $retHtml.='<dd class="itemDtl_pic"><img src="'.$imgPath.'" alt="'.$name.'" width="400" height="400" loading="lazy" /></dd>';
  $retHtml.='<dt class="itemDtl_name">'.$name.'</dt>';
  $retHtml.='<dd class="itemDtl_price">'.$price.'</dd>';
  $retHtml.='</dl></a>';

  $post = $oldpost;
  wp_reset_postdata();
  return $retHtml;
}
add_shortcode("InnerLiOfItem", "getInnerLiOfItem"); // [InnerLiOfItem id="999"]で呼び出せる

// ***********************　いいねボタンの機能 ***********************
//----------------------------------------------
// いいねボタンのクッキーがあるかどうか確かめる
// 戻値： true クッキー有, false: クッキー無
//----------------------------------------------
function checkHasCookie($post_id) {
  $ret = false;
  if (isset($_COOKIE['good']) && array_key_exists($post_id, $_COOKIE['good'])) {
    $ret = true;
  }
  return $ret;
}

//----------------------------------------------
// いいねボタン表示（ショートコード）
//----------------------------------------------
function getGoodBtn() {
  // ACFプラグインが無効化されている（get_field等が使えない）場合は何もしない
  if (!function_exists('get_field')) {
    return;
  }

  $FIELD_KEY = 'good';
  global $post;
  $postID = $post->ID;
  $count = get_field($FIELD_KEY, $postID);

  if (!$count) {
    // いいね数が取得できない場合は、初期値として0をセット
    update_field($FIELD_KEY, 0, $postID);
    $count = get_field($FIELD_KEY, $postID);
  }

  $btnClass = 'goodBtn';  // いいねボタンのクラス
  $btnCaption = 'Good';   // いいねボタンのキャプション
  if (checkHasCookie($postID)) {
    // いいね済みの場合
    $btnClass = $btnClass . ' -clicked';
  }
  $html = '<div class="goodBtnContainer"><button type="button" id="goodBtn" class="'.$btnClass.'" data-id="'.$postID.'">'.$btnCaption.'</button><p class="goodCnt">いいね <span id="goodCntNum">'.$count.'</span>件</p></div>';

  return $html;
}
add_shortcode('goodBtnArea', 'getGoodBtn'); // [goodBtnArea]で呼び出せる

//----------------------------------------------
// いいね数カウント処理（いいねボタン押下時に呼び出される）
//----------------------------------------------
function count_up() {
  // postIDないときはなにもしない
  if (!isset($_POST['postID'])) {
    return;
  }
  // get_field等が使えない場合は何もしない
  if(!function_exists('get_field')) {
    return;
  }

  $FIELD_KEY = 'good';
  $postID = $_POST['postID']; //投稿ID
  $count = (int)get_field($FIELD_KEY, $postID); // いいね数

  if (checkHasCookie($postID)) {
    // 【いいね済みの場合】
    if($count > 0){
      update_field($FIELD_KEY, $count - 1, $postID);
    }
    setcookie('good['.$postID.']', false, time() - 30, '/'); // Cookieから該当の投稿情報を削除
  }
  else {
    // 【未いいねの場合】
    update_field($FIELD_KEY, $count + 1, $postID);
    setcookie('good['.$postID.']', $postID, time() + 60 * 60 * 24 * 30, '/'); //いいねした投稿情報をCookieに保存
  }

  // 新しいカウント数を返却する
  echo get_field($FIELD_KEY, $postID);
  die();  // die()を呼び出さないと、返り値の値の最後に「0」が付く
}
add_action( 'wp_ajax_count_up', 'count_up' );// ログインしているユーザー向け関数
add_action( 'wp_ajax_nopriv_count_up', 'count_up' );// 非ログインユーザー用関数

?>
