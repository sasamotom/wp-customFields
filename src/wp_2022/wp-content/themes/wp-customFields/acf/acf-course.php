<?php
//----------------------------------------------
// カスタム投稿を定義
//----------------------------------------------
function create_post_type_course() {
  $label = '講座';
  $slug = 'course';
  // $tax = 'cat_course';
  // $taxName = '（タクソノミー名）';
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
    'has_archive' => true,
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
  //     'show_admin_column' => true
  //   )
  // );
  // register_taxonomy_for_object_type($tax, $slug);
  // register_taxonomy_for_object_type('post_tag', $slug); // 「投稿」のタグ
  // register_taxonomy_for_object_type('category', $slug); // 「投稿」のカテゴリー
}
add_action( 'init', 'create_post_type_course' );

//----------------------------------------------
// アーカイブページにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_tag_archive_course( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_tag()) {
    $wp_query->set( 'post_type', array('post','course'));
  }
}
add_action( 'pre_get_posts', 'add_post_tag_archive_course');

//----------------------------------------------
// カテゴリーアーカイブにて、post_typeに今回のカスタム投稿タイプを追加する
//----------------------------------------------
function add_post_category_archive_course( $wp_query ) {
  if ($wp_query->is_main_query() && $wp_query->is_category()) {
  $wp_query->set( 'post_type', array('post','course'));
  }
}
add_action( 'pre_get_posts', 'add_post_category_archive_course');

//----------------------------------------------
// カスタムタクソノミーを定義
//----------------------------------------------
// function add_custom_taxonomies_term_filter_course() {
//   global $post_type;
//   if ( $post_type == 'course' ) {
//     $taxonomy = 'cat_course';
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
// add_action( 'restrict_manage_posts', 'add_custom_taxonomies_term_filter_course' );

//----------------------------------------------
// ショートコード
//----------------------------------------------
// コースリストの取得
function getCourseList($atts) {
  extract(shortcode_atts(array(
    // "num" => '3',         // 最新記事リストの取得数
    "cat" => 'course',       // 表示する記事のpost_type
    "tag" => '',
  ), $atts));
  global $post;
  $oldpost = $post;
  $myposts = get_posts('order=DESC&orderby=menu_order&post_type='.$cat.'&tag='.$tag);
  if (!$myposts) {
    return false;
  }
  else {
    $retHtml='<ul class="courseList">';
    foreach($myposts as $post) :
      $linkPath = esc_url(get_permalink());
      $courseName = esc_html(get_the_title("","",false));
      $imgPath = esc_url(get_field('img01'));
      if (empty($imgPath)) {
        $imgPath = esc_url(get_field('course_img01'));
      }
      setup_postdata($post);
      $retHtml.='<li>';
      $retHtml.='<a href="'.$linkPath.'"><dl class="courseDtl">';
      if (!empty($imgPath)) {
        $retHtml.='<dd class="courseDtl_pic"><img src="'.$imgPath.'" alt="'.$courseName.'" '.get_image_size($imgPath).' loading="lazy" /></dd>';
      }
      else {
        $retHtml.='<dd class="courseDtl_pic"><img src="'.esc_url(content_url()).'/uploads/default.jpg" alt="no image" width="800" height="500" loading="lazy" /></dd>';
      }
      $retHtml.='<dt class="courseDtl_name">'.$courseName.'</dt>';
      $retHtml.='</dl></a>';
      $retHtml.='</li>';
    endforeach;
    $retHtml.='</ul>';
    $post = $oldpost;
    wp_reset_postdata();
    return $retHtml;
  }
}
add_shortcode("courseList", "getCourseList"); // [courseList]で呼び出せる
?>
