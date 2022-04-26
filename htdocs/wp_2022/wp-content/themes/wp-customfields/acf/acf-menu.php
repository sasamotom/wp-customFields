<?php
//----------------------------------------------
// カスタム投稿を定義
//----------------------------------------------
function create_post_type_menu() {
  $label = 'MENU';
  $slug = 'menu';
  $tax = 'cat_menu';
  $taxName = 'MENUカテゴリ';
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
      // 'thumbnail',
      'custom-fields',
      // 'excerpt',
      // 'author',
      'revisions',
      //'page-attributes',
    ],
    'rewrite' => ["slug" => "site3/menu", "with_front" => false],
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
      'rewrite' => array('slug' => 'site3/menu/category', 'with_front' => false)
    )
  );
  register_taxonomy_for_object_type($tax, $slug);
  // register_taxonomy_for_object_type('post_tag', $slug); // 「投稿」のタグ
  // register_taxonomy_for_object_type('category', $slug); // 「投稿」のカテゴリー
}
add_action( 'init', 'create_post_type_menu' );

//----------------------------------------------
// アーカイブページにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_tag_archive_menu( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_tag()) {
    $wp_query->set( 'post_type', array('post','menu'));
  }
}
add_action( 'pre_get_posts', 'add_post_tag_archive_menu');

//----------------------------------------------
// カテゴリーアーカイブにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_category_archive_menu( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_category()) {
  $wp_query->set( 'post_type', array('post','menu'));
  }
}
add_action( 'pre_get_posts', 'add_post_category_archive_menu');

//----------------------------------------------
// カスタムタクソノミーを定義
//----------------------------------------------
function add_custom_taxonomies_term_filter_menu() {
  global $post_type;
  if ( $post_type == 'menu' ) {
    $taxonomy = 'cat_menu';
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
add_action( 'restrict_manage_posts', 'add_custom_taxonomies_term_filter_menu' );

//----------------------------------------------
// URLを変更したタクソノミーアーカイブページへアクセスできるようにする
//----------------------------------------------
function url_rewrite_rules_menu() {
  add_rewrite_rule('site3/menu/category/([^/]+)/page/([0-9]+)/?$', 'index.php?cat_menu=$matches[1]&paged=$matches[2]', 'top');
  add_rewrite_rule('site3/menu/category/([^/]+)/?$', 'index.php?cate_menu=$matches[1]', 'top');
}
add_action( 'init', 'url_rewrite_rules_menu' );

//----------------------------------------------
// カスタムタクソノミーのリンク調整
//----------------------------------------------
function rewrite_term_links_menu($termlink, $term, $taxonomy) {
  return ($taxonomy === 'cat_menu' ? home_url('/site3/menu/category/'. $term->slug) : $termlink);
}
add_filter( 'term_link', 'rewrite_term_links_menu', 10, 3 );

//----------------------------------------------
// カスタムフィールド定義（ACFのGenerate PHPをベースに作成）
//----------------------------------------------
if( function_exists('acf_add_local_field_group') ):
  acf_add_local_field_group(array(
    'key' => 'MENU_content',
    'title' => 'MENU',
    'fields' => array(
      array(
        'key' => 'image',
        'label' => '画像',
        'name' => 'image',
        'type' => 'image',
        'instructions' => '商品画像を設定します。',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'return_format' => 'url',
        'preview_size' => 'medium',
        'library' => 'all',
        'min_width' => '',
        'min_height' => '',
        'min_size' => '',
        'max_width' => '',
        'max_height' => '',
        'max_size' => '',
        'mime_types' => 'jpg,png,gif,svg',
      ),
      array(
        'key' => 'text',
        'label' => '説明文',
        'name' => 'text',
        'type' => 'textarea',
        'instructions' => '説明文を入力します。',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'placeholder' => '',
        'maxlength' => '',
        'rows' => 4,
        'new_lines' => 'br',
      ),
      array(
        'key' => 'price',
        'label' => '値段（税込）',
        'name' => 'price',
        'type' => 'number',
        'instructions' => '値段を入力します。',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '円（税込）',
        'min' => '',
        'max' => '',
        'step' => '',
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'menu',
        ),
      ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
  ));
endif;

if( function_exists('acf_add_local_field_group') ):
  acf_add_local_field_group(array(
    'key' => 'newdonuts_content',
    'title' => 'NEW DONUTS',
    'fields' => array(
      array(
        'key' => 'newdonuts',
        'label' => 'NEW DONUTSに表示するドーナツ',
        'name' => 'newdonuts',
        'type' => 'post_object',
        'instructions' => 'TOPページに表示する新商品を１つ選択します。',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
          'width' => '',
          'class' => '',
          'id' => '',
        ),
        'post_type' => array(
          0 => 'menu',
        ),
        'taxonomy' => '',
        'allow_null' => 0,
        'multiple' => 0,
        'return_format' => 'id',
        'ui' => 1,
      ),
    ),
    'location' => array(
      array(
        array(
          'param' => 'page',
          'operator' => '==',
          'value' => '329',
        ),
      ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
  ));
endif;

//----------------------------------------------
// ショートコード
//----------------------------------------------
// TOPページ用ニュース一覧（新しいものから３つ）
function getNewDonutsForTop($atts) {
  // get_field等が使えない場合は何もしない
  if(!function_exists('get_field')) {
    return;
  }
  $donuts_id = get_field('newdonuts');
  if (!$donuts_id) {
    return false;
  }

  global $post;
  $oldpost = $post;
  $mypost = get_post($donuts_id);
  if (!$mypost) {
    return false;
  }
  $post = $mypost;
  $retHtml='<section class="topNew"><div class="container"><div class="twoCols">';
  $imgPath = esc_url(get_field('image'));
  $name  = esc_html(get_the_title("","",false));
  $text  = get_field('text');
  $price  = esc_html(get_field('price'));
  setup_postdata($post);

  $retHtml.='<div class="col"><h2 class="secTtl">NEW DONUTS</h2>';
  if ($imgPath) {
    $retHtml.='<div class="pic"><img src="'. $imgPath.'" alt="'.$name.'" '.get_image_size($imgPath).' loading="lazy" /></div>';
  }
  $retHtml.='</div>';
  $retHtml.='<div class="col"><dl class="donutsInfo">';
  $retHtml.='<dt class="donutsInfo_name">'. $name.'</dt>';
  $retHtml.='<dd class="donutsInfo_txt">'.$text.'</dd>';
  $retHtml.='<dd class="donutsInfo_price">¥'.$price.'<span class="-small">（税込）</span></dd>';
  $retHtml.='</dl></div>';

  $retHtml.='</div></div></section>';
  $post = $oldpost;
  wp_reset_postdata();
  return $retHtml;
}
add_shortcode("newDonutsForTop", "getNewDonutsForTop"); // [newDonutsForTop]で呼び出せる

?>
