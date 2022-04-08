<?php get_header(); ?>

<?php
  global $post;
  $slug = $post->post_name;
  $cat = get_queried_object();
  $cat_name = $cat -> label;
?>

<main id="main">
  <div class="pageTtlContainer">
    <div class="container">
      <h1 class="pageTtl"><?php echo $cat_name; ?></h1>
    </div>
  </div>
  <div class="breadcrumbContainer">
    <div class="container">
      <?php breadcrumb(); ?>
    </div>
  </div>
<?php
  if (have_posts()) :
    while(have_posts()):
      the_post();
      the_content();
    endwhile;
  endif;
?>
  <section class="sec-courseList">
    <div class="container">
      <h2 class="secTtl">開催中の講座</h2>
<?php
  // $paged = (int) get_query_var('paged');
  $args = array(
    'posts_per_page' => 10,
    'paged' => $paged,
    'orderby' => 'menu_order',
    'order' => 'asc',
    'post_type' => 'course',
    'post_status' => 'publish'
  );
  $the_query = new WP_Query($args);
  if ( $the_query->have_posts() ) : ?>
    <ul class="courseList">
<?php
    while ( $the_query->have_posts() ) : $the_query->the_post();
      $linkPath = esc_url(get_permalink());
      $name = esc_html(get_the_title("","",false));
      $description = get_field('description');
      $imgPath = esc_url(get_field('img01'));
      if (empty($imgPath)) {
        $imgPath = esc_url(get_field('course_img01'));
      }
?>
      <li>
        <dl class="courseDtl">
      <?php if (!empty($imgPath)) : ?>
          <dd class="courseDtl_pic"><img src="<?php echo $imgPath ?>" alt="<?php echo $name ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" /></dd>
      <?php else : ?>
          <dd class="courseDtl_pic"><img src="<?php echo site_url(); ?>/wp-content/uploads/default.jpg" alt="no image" width="800" height="500" loading="lazy" /></dd>
      <?php endif; ?>
          <div class="courseDtl_col2">
            <dt class="courseDtl_name"><?php echo $name ?></dt>
            <dd class="courseDtl_txt"><?php echo $description ?></dd>
            <dd class="courseDtl_btn btn"><a href="<?php echo $linkPath ?>">MORE</a></dd>
          </div>
        </dl>
      </li>
<?php
    endwhile;
    wp_reset_postdata();
?>
    </ul>
<?php
  else :
?>
    <p>只今、講座は準備中です</p>
<?php
  endif;
?>
<?php pagination($the_query->max_num_pages, 2) ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
