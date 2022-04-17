<?php get_header(); ?>

<?php
  global $post;
  $slug = $post->post_name;
  $cat = get_queried_object();
  $cat_name = $cat -> label;
?>

<main id="main">
  <div class="pageTtlContainer faq">
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
  <div class="sec-faq">
    <div class="container">
<?php
  // $paged = (int) get_query_var('paged');
  $args = array(
    'posts_per_page' => 20,
    'paged' => $paged,
    'orderby' => 'menu_order',
    'order' => 'asc',
    'post_type' => 'faq',
    'post_status' => 'publish'
  );
  $the_query = new WP_Query($args);
  if ( $the_query->have_posts() ) : ?>
    <dl class="faqList">
<?php
    while ( $the_query->have_posts() ) : $the_query->the_post();
      $text_q = esc_html(get_field('text_q'));
      $text_a = get_field('text_a');
?>
        <dt dt class="faqList_q"><span><?php echo $text_q ?></span></dt>
        <dd class="faqList_a"><span><?php echo $text_a ?></span></dd>
<?php
    endwhile;
    wp_reset_postdata();
?>
    </dl>
<?php
  else :
?>
    <p>只今、記事は準備中です</p>
<?php
  endif;
?>
<?php pagination($the_query->max_num_pages, 2) ?>
    </div>
  </div>
</main>

<?php get_footer(); ?>
