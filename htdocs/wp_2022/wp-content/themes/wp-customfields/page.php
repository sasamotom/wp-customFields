<?php get_header(); ?>

<?php
  global $post;
  $slug = $post->post_name;
?>

<main id="main">
  <div class="pageTtlContainer">
    <div class="container">
      <h1 class="pageTtl"><?php the_title(); ?></h1>
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
</main>

<?php get_footer(); ?>
