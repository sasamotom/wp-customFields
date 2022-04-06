<?php get_header(); ?>

<?php
if ( !is_home() && !is_front_page() && have_posts() ) :
  while ( have_posts() ) : the_post();
?>
  <h1><?php the_title(); ?></h1>
<?php
    endwhile;
endif;
?>
  <main>
    <div class="container">

<?php
if ( have_posts() ) :
  while ( have_posts() ) : the_post();
?>
  <?php the_content(); ?>
<?php
  endwhile;
endif;
?>
    </div>
  </main>

<?php get_footer(); ?>
