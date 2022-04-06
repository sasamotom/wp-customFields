<?php get_header(); ?>
<?php
  $cat = get_queried_object();
  $cat_name = $cat -> name;
  $cat_slug = $cat -> slug;
?>
<?php remove_filter('the_content', 'wpautop'); ?>
<!-- <h1 class="underTitle h1_seminar"><?php echo esc_html($cat_name); ?></h1> -->

<main id="main">
  <div class="pageTtlContainer">
    <div class="container">
      <h1 class="pageTtl">BLOG</h1>
    </div>
  </div>
  <div class="breadcrumbContainer">
    <div class="container">
      <?php breadcrumb(); ?>
    </div>
  </div>

  <section class="sec-blogList">
    <div class="container">
      <h2 class="secTtl">最新記事</h2>
      <ul class="articleList">
<?php
$paged = (int) get_query_var('paged');  // 今いるページ
$args = array(
  'posts_per_page' => 5, // １ページあたりの記事数
  'paged' => $paged,
  'orderby' => 'date',
  'order' => 'desc',
  'post_type' => 'post',
  'post_status' => 'publish'
);
$the_query = new WP_Query($args);
?>
<?php if ( $the_query->have_posts() ) : ?>
  <?php while ( $the_query->have_posts() ) :
    $the_query->the_post();
    $category = get_the_category();
    $cat_name2 = $category[0]->cat_name;
  ?>
        <li>
          <a href="<?php echo esc_url(get_permalink()); ?>">
            <dl class="articleDtl">
              <dd class="articleDtl_pic">
              <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail(); ?>
              <?php elseif (first_image()): ?>
                <img src="<?php echo esc_url(first_image()) ?>" alt="<?php esc_html(the_title()); ?>" <?php echo get_image_size(first_image()); ?> loading="lazy" />
              <?php else: ?>
                <img src="<?php echo site_url(); ?>/wp-content/uploads/default.jpg" alt="no image" width="800" height="500" loading="lazy" />
              <?php endif; ?>
              </dd>
              <div class="col2">
                <dd class="articleDtl_date"><time datatime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y/n/j'); ?></time></dd>
                <dt class="articleDtl_ttl"><h3><?php esc_html(the_title()); ?></h3></dt>
                <dd class="articleDtl_txt"><?php esc_html(the_excerpt()); ?></dd>
              </div>
            </dl>
          </a>
        </li>
  <?php endwhile;?>
<?php elseif(! have_posts() ) :?>
        <li>只今、記事は準備中です</li>
<?php endif; ?>
      </ul>
    <?php pagination($the_query->max_num_pages, 2) ?>
<?php wp_reset_postdata(); ?>
    </div>
  </section>
</main>
<?php get_footer(); ?>
