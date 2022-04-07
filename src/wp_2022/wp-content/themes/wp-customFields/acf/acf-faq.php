<?php
//----------------------------------------------
// カスタム投稿を定義
//----------------------------------------------
function create_post_type_faq() {
  $label = 'よくある質問';
  $slug = 'faq';
  $tax = 'cat_faq';
  $taxName = '質問カテゴリ';
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
    'has_archive' => true,
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
      'show_admin_column' => true
    )
  );
  register_taxonomy_for_object_type($tax, $slug);
  // register_taxonomy_for_object_type('post_tag', $slug); // 「投稿」のタグ
  // register_taxonomy_for_object_type('category', $slug); // 「投稿」のカテゴリー
}
add_action( 'init', 'create_post_type_faq' );

//----------------------------------------------
// アーカイブページにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_tag_archive_faq( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_tag()) {
    $wp_query->set( 'post_type', array('post','faq'));
  }
}
add_action( 'pre_get_posts', 'add_post_tag_archive_faq');

//----------------------------------------------
// カテゴリーアーカイブにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_category_archive_faq( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_category()) {
  $wp_query->set( 'post_type', array('post','faq'));
  }
}
add_action( 'pre_get_posts', 'add_post_category_archive_faq');

//----------------------------------------------
// カスタムタクソノミーを定義
//----------------------------------------------
function add_custom_taxonomies_term_filter_faq() {
  global $post_type;
  if ( $post_type == 'faq' ) {
    $taxonomy = 'cat_faq';
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
add_action( 'restrict_manage_posts', 'add_custom_taxonomies_term_filter_faq' );

//----------------------------------------------
// ショートコード
//----------------------------------------------
// TOPページ用よくある質問を取得（質問カテゴリが「TOPページに表示（slug==showtop）」のもの←これはWP管理画面から登録）
function getFaqForTop($atts) {
  extract(shortcode_atts(array(
    // "num" => '3',         // 最新記事リストの取得数
    "cat" => 'faq',       // 表示する記事のpost_type
    "tag" => '',
    "cat_faq" => 'showtop'
  ), $atts));
  global $post;
  $oldpost = $post;
  // $myposts = get_posts('numberposts='.$num.'&order=asc&orderby=menu_order&post_type='.$cat.'&tag='.$tag);
  $myposts = get_posts('order=DESC&orderby=menu_order&post_type='.$cat.'&tag='.$tag .'&cat_faq='.$cat_faq);
  if (!$myposts) {
    return false;
  }
  else {
    $retHtml='<dl class="faqList">';
    foreach($myposts as $post) :
      $text_q = esc_html(get_field('text_q'));
      $text_a = get_field('text_a');
      // $post_id = get_the_ID();
      setup_postdata($post);
      $retHtml.='<dt class="faqList_q"><span>'.$text_q.'</span></dt>';
      $retHtml.='<dd class="faqList_a"><span>'.$text_a.'</span></dd>';
    endforeach;
    $retHtml.='</dl>';
    $post = $oldpost;
    wp_reset_postdata();
    return $retHtml;
  }
}
add_shortcode("faqForTop", "getFaqForTop"); // [faqForTop]で呼び出せる
?>
