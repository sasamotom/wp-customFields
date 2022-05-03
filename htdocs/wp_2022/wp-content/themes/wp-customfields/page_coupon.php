<?php
/**
 * Template Name: site3-COUPON
 * Description: クーポンページ用テンプレート
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
      <h1 class="site3PageTtl">COUPON</h1>
    </div>
  </div>
  <section class="sec-coupon">
    <div class="container">
      <p>このページをご購入時にお見せいただければ、クーポンが適用できます！<br>※クーポンの併用はできません。</p>
<?php
  if (have_posts()) :
    while(have_posts()):
      the_post();
      the_content();
    endwhile;
  endif;
?>
<?php
  // クーポン情報を表示
  $coupon_list = get_post_meta( $post->ID, 'coupon_list', true );
  if (!empty($coupon_list)) :
?>
      <ul class="couponList">
<?php
    $today = new DateTime(date("Y-m-d"));
    for ($i = 0 ; $i < count($coupon_list[0]); $i++) :
      $startFlag = true;    // 開始しているかどうか（true: 開始している, false: 開始していない）
      $endFlag = false;     // 終了しているかどうか（true: 終了している, false: 終了していない）
      $imgPath = esc_url($coupon_list[0][$i]);
      $startDate = esc_html($coupon_list[1][$i]);
      if ($startDate) {
        $startDate = new DateTime($startDate);
        if ($today < $startDate) {
          $startFlag = false;
        }
      }
      $endDate = esc_html($coupon_list[2][$i]);
      if ($endDate) {
        $endDate = new DateTime($endDate);
        if ($endDate < $today) {
          $endFlag = true;
        }
      }
      if ($imgPath && $endFlag === false) :
?>
        <li <?php if ($startFlag === false) { echo 'class="notStart"'; } ?>>
          <img src="<?php echo $imgPath; ?>" alt="クーポン画像" <?php echo get_image_size($imgPath) ?> loading="lazy">
        </li>
<?php
      endif;
    endfor;
?>
      </ul>
<?php
  endif;
?>
<?php
  // Instagramを表示
  // get_template_part( 'template/content', 'instagram' );
?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
