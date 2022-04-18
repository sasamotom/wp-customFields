<?php get_header(); ?>

<!-- Feature storyアーカイブ -->
<?php
  global $post;
  $post_type = $post->post_type;
  $post_name = (get_post_type_object($post_type))->label;
?>

<main id="main" class="site2">
  <div class="pageTtlContainer feature_story">
    <div class="container">
      <h1 class="site2PageTtl"><?php echo $post_name; ?></h1>
    </div>
  </div>

  <div class="sec-featureList">
    <div class="container">
      <ul class="featureList">
<?php
  $paged = (int) get_query_var('paged');
  $args = array(
    'posts_per_page' => 5,
    'paged' => $paged,
    'orderby' => 'date',
    'order' => 'desc',
    'post_type' => 'feature_story',
    'post_status' => 'publish'
  );
  $the_query = new WP_Query($args);
  if ( $the_query->have_posts() ) :
    while ( $the_query->have_posts() ) : $the_query->the_post();
?>
        <li>
          <a href="<?php esc_url(the_permalink()); ?>">
            <dl class="featureDtl">
              <dd class="featureDtl_pic">
                <!-- <img src="/wp_2022/wp-content/uploads/2_img10.jpg" alt=""> -->
                <!-- 画像優先度：アイキャッチ→画像の１つめ→default -->
      <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail(); ?>
      <?php else :
        $imgPath = '';
        $rows = get_field('iteminfo');
        if ($rows) {
          $imgPath = esc_url(($rows[0])['itemimage']);
        }
        if ($imgPath) :
      ?>
                <img src="<?php echo $imgPath ?>" alt="<?php echo $name ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" />
        <?php else : ?>
                <img src="<?php echo site_url(); ?>/wp-content/uploads/default.jpg" alt="no image" width="800" height="500" loading="lazy" />
        <?php endif; ?>
      <?php endif; ?>
              </dd>
              <div class="featureDtl_txts">
                <dt class="featureDtl_date"><time datatime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y/n/j'); ?></time></dt>
                <dt class="featureDtl_name"><?php esc_html(the_title()); ?></dt>
              </div>
            </dl>
          </a>
        </li>
<?php
    endwhile;
    wp_reset_postdata();
?>
    </ul>
<?php
  else :
?>
    <p>Feature storyはありません。</p>
<?php
  endif;
?>
<?php pagination($the_query->max_num_pages, 2) ?>
    </div>
  </div>
</main>


<?php get_footer(); ?>
