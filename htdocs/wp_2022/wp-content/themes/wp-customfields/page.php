<?php get_header(); ?>

<?php
  global $post;
  $slug = $post->post_name;
  $parent_id = get_page_parent( $post->post_parent );
  $parent_slug = get_post($parent_id)->post_name;
?>

<main id="main" class="<?php echo $parent_slug ?>">
<?php if ($slug === 'site2') : ?>
  <!-- ページタイトルなし -->
<?php elseif ($parent_slug === 'site2') : ?>
  <div class="pageTtlContainer <?php echo $slug ?>">
    <div class="container">
      <h1 class="site2PageTtl"><?php the_title(); ?></h1>
    </div>
  </div>
<?php else : ?>
  <div class="pageTtlContainer <?php echo $slug ?>">
    <div class="container">
      <h1 class="pageTtl"><?php the_title(); ?></h1>
    </div>
  </div>
  <div class="breadcrumbContainer">
    <div class="container">
      <?php breadcrumb(); ?>
    </div>
  </div>
<?php endif; ?>
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
