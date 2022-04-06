<?php get_header(); ?>

<?php
  $cat = get_queried_object();
  $cat_name = esc_html($cat -> name);
  $cat_slug = esc_html($cat -> slug);
  $title = esc_html(get_the_title('', '', false));
  $content = get_the_content('', '', false);
  $category = get_the_category();
  $cat_id   = $category[0]->cat_ID;
  $cat_name = esc_html($category[0]->cat_name);
?>

<main id="main">
  <div class="pageTtlContainer">
    <div class="container">
      <h1 class="pageTtl"><?php echo $title ?></h1>
    </div>
  </div>
  <div class="breadcrumbContainer">
    <div class="container">
      <?php breadcrumb(); ?>
    </div>
  </div>
  <section class="sec-blog">
    <div class="container">
      <article class="blogContainer">
<?php
if ( have_posts() ) :
  while ( have_posts() ) :
    the_post();
    the_content();
  endwhile;
else :
?>
        <p>只今、記事は準備中です</p>
<?php
endif;
?>
<?php
  $prev_post = get_previous_post(false);
  $next_post = get_next_post(false);
?>

      </article>
      <div class="btnContainer">
        <div class="btn -back">
<?php if (!empty( $prev_post )): ?>
          <a href="<?php echo esc_url(get_permalink( $prev_post -> ID )); ?>">前の記事へ</a>
<?php else : ?>
          <span>前の記事へ</span>
<?php endif; ?>
        </div>
        <div class="returnList">
          <a href="/blog/">一覧に戻る</a>
        </div>
        <div class="btn">
<?php if (!empty( $next_post )): ?>
          <a href="<?php echo esc_url(get_permalink( $next_post -> ID )); ?>">次の記事へ</a>
<?php else : ?>
          <span>次の記事へ</span>
<?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
