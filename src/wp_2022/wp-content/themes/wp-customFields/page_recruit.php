<?php
/**
 * Template Name: site3-RECRUIT
 * Description: 採用ページ用テンプレート
 */
?>

<?php get_header(); ?>

<?php
  global $post;
  $slug = $post->post_name;
  $parent_id = get_page_parent( $post->post_parent );
  $parent_slug = get_post($parent_id)->post_name;
?>

<main id="main" class="<?php echo $parent_slug ?>">
  <div class="pageTtlContainer">
    <div class="container">
      <h1 class="site3PageTtl">RECRUIT</h1>
    </div>
  </div>
  <div class="container">
    <div class="layout_2cols">
      <nav class="col1 recruitNav">
      <?php wp_nav_menu( array (
        'theme_location' => 'site3recruit',                     // 登録したメニューのキーを入れる
        'items_wrap' => '<ul class="recruitNavList">%3$s</ul>', // ulを指定
        'container' => false,                                   // "div"や"nav"を指定するとラッパー生成できるけどclassをつけたい場合はこのPHP自体をnavで括る方が早い
        'echo' => true,                                         // falseで変数として出力（加工ができる）
      )); ?>
      </nav>
      <div class="col2">
<?php
  if (have_posts()) :
    while(have_posts()):
      the_post();
      the_content();
    endwhile;
  endif;
?>
      </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>
