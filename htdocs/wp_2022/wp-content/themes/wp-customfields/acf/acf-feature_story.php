<?php
//----------------------------------------------
// カスタム投稿を定義
//----------------------------------------------
function create_post_type_feature() {
  $label = 'Feature story';
  $slug = 'feature_story';
  // $tax = 'cat_news';
  // $taxName = 'Newsカテゴリ';
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
    'rewrite' => ["slug" => "site2/feature_story", "with_front" => false],
  ];
  register_post_type($slug, $args);

  // カスタム投稿にカスタムタクソノミーを関連づける
  // register_taxonomy(
  //   $tax,                 // タクソノミーの名前
  //   $slug,                // このタクソノミーをこのカスタム投稿に設定する
  //   array(
  //     'labels' => array(
  //       'name' => $taxName  // 管理画面で表示する分類名
  //     ),
  //     'hierarchical' => true,           // true  階層を持たせられる
  //     'show_admin_column' => true,
  //     'rewrite' => array('slug' => 'site2/news/category', 'with_front' => false)
  //   )
  // );
  // register_taxonomy_for_object_type($tax, $slug);
  // register_taxonomy_for_object_type('post_tag', $slug); // 「投稿」のタグ
  // register_taxonomy_for_object_type('category', $slug); // 「投稿」のカテゴリー
}
add_action( 'init', 'create_post_type_feature' );

//----------------------------------------------
// アーカイブページにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_tag_archive_feature( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_tag()) {
    $wp_query->set( 'post_type', array('post','feature_story'));
  }
}
add_action( 'pre_get_posts', 'add_post_tag_archive_feature');

//----------------------------------------------
// カテゴリーアーカイブにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_category_archive_feature( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_category()) {
  $wp_query->set( 'post_type', array('post','news'));
  }
}
add_action( 'pre_get_posts', 'add_post_category_archive_feature');

//----------------------------------------------
// カスタムタクソノミーを定義
//----------------------------------------------
// function add_custom_taxonomies_term_filter_news() {
//   global $post_type;
//   if ( $post_type == 'news' ) {
//     $taxonomy = 'cat_news';
//     wp_dropdown_categories( array(
//       'show_option_all' => 'すべてのカテゴリー',
//       'orderby'         => 'name',
//       'selected'        => get_query_var( $taxonomy ),
//       'hide_empty'      => 0,
//       'name'            => $taxonomy,
//       'taxonomy'        => $taxonomy,
//       'value_field'     => 'slug',
//     ) );
//   }
// }
// add_action( 'restrict_manage_posts', 'add_custom_taxonomies_term_filter_news' );

//----------------------------------------------
// URLを変更したタクソノミーアーカイブページへアクセスできるようにする
//----------------------------------------------
// function url_rewrite_rules_news() {
//   add_rewrite_rule('site2//category/([^/]+)/page/([0-9]+)/?$', 'index.php?cat_news=$matches[1]&paged=$matches[2]', 'top');
//   add_rewrite_rule('site2/news/category/([^/]+)/?$', 'index.php?cate_news=$matches[1]', 'top');
// }
// add_action( 'init', 'url_rewrite_rules_news' );

//----------------------------------------------
// カスタムタクソノミーのリンク調整
//----------------------------------------------
// function rewrite_term_links_news($termlink, $term, $taxonomy) {
//   return ($taxonomy === 'news_news' ? home_url('/site2/news/category/'. $term->slug) : $termlink);
// }
// add_filter( 'term_link', 'rewrite_term_links_news', 10, 3 );

//----------------------------------------------
// ショートコード
//----------------------------------------------
// TOPページ用ニュース一覧（新しいものから３つ）
// function getNewsListForTop($atts) {
//   extract(shortcode_atts(array(
//     "num" => '3',         // 取得記事数
//     "cat" => 'news',       // 表示する記事のpost_type
//     "tag" => '',
//   ), $atts));
//   global $post;
//   $oldpost = $post;
//   $myposts = get_posts('numberposts='.$num.'&order=desc&orderby=date&post_type='.$cat.'&tag='.$tag);
//   if (!$myposts) {
//     return false;
//   }
//   else {
//     $retHtml='      <ul class="newsList">';
//     foreach($myposts as $post) :
//       $txt  = esc_html(get_the_title("","",false));
//       setup_postdata($post);
//       $retHtml.='<li><a href="'.esc_url(get_permalink()).'"><dl class="newsList_item">';
//       $retHtml.='<dd class="newsList_date"><time datatime="'.get_the_date('Y-m-d', $post->ID).'">'.get_the_date('Y/n/j', $post->ID).'</time></dd>';
//       $tax_slug = 'cat_news';         // タクソノミースラッグ指定
//       $cat_terms = wp_get_object_terms($post->ID, $tax_slug); // タームの情報を取得
//       if (!empty($cat_terms)) { // 変数が空でなければ true
//         if (!is_wp_error($cat_terms)) {
//           foreach ($cat_terms as $cat_term) {
//             $retHtml.='<dd class="newsList_tag '.$cat_term->slug.'">'.$cat_term->name.'</dd>';
//           }
//         }
//       }
//       $retHtml.='<dt class="newsList_txt">'.$txt.'</dt>';
//       $retHtml.='</dl></a></li>';
//     endforeach;
//     $retHtml.='</ul>';
//     $post = $oldpost;
//     wp_reset_postdata();
//     return $retHtml;
//   }
// }
// add_shortcode("newsListForTop", "getNewsListForTop"); // [newsListForTop]で呼び出せる

?>
