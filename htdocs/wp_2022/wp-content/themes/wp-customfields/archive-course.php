<?php get_header(); ?>

<?php
  global $post;
  $slug = $post->post_name;
  $cat = get_queried_object();
  $cat_name = $cat -> label;
?>

<main id="main">
  <div class="pageTtlContainer course">
    <div class="container">
      <h1 class="pageTtl"><?php echo $cat_name; ?></h1>
    </div>
  </div>
  <div class="breadcrumbContainer">
    <div class="container">
      <?php breadcrumb(); ?>
    </div>
  </div>
  <section class="sec-aboutPastel">
    <div class="container">
      <h2 class="secTtl">講座について</h2>
      <div class="textContainer">
        <div class="txts">
          <h3 class="txts_ttl">複数講座から選べる</h3>
          <p>初めての方でも簡単に楽しめる講座やインストラクター養成講座など、４講座ご用意しております。<br>一部講座は、WEBでも受講可能です。<br>初めての方には、お好きな絵を選んでいただくパステルアート講座がおすすめです。</p>
        </div>
        <div class="pic">
          <img src="/wp_2022/wp-content/uploads/img024.jpg" alt="">
        </div>
      </div>
    </div>
  </section>
<?php
  if (have_posts()) :
    while(have_posts()):
      the_post();
      the_content();
    endwhile;
  endif;
?>
  <section class="sec-courseList" id="list">
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
?>
      <li>
        <dl class="courseDtl">
      <?php if (has_post_thumbnail()) : ?>
          <dd class="courseDtl_pic"><?php the_post_thumbnail(); ?></dd>
      <?php else :
        $imgPath = esc_url(get_field('img01'));
        if (empty($imgPath)) {
          $imgPath = esc_url(get_field('course_img01'));
        }
      ?>
        <?php if (!empty($imgPath)) : ?>
            <dd class="courseDtl_pic"><img src="<?php echo $imgPath ?>" alt="<?php echo $name ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" /></dd>
        <?php else : ?>
            <dd class="courseDtl_pic"><img src="<?php echo site_url(); ?>/wp-content/uploads/default.jpg" alt="no image" width="800" height="500" loading="lazy" /></dd>
        <?php endif; ?>
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
