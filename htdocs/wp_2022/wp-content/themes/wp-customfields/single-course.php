<?php get_header(); ?>

<?php
  $cat = get_queried_object();
  $cat_name = esc_html($cat -> name);
  $cat_slug = esc_html($cat -> slug);
  $title = esc_html(get_the_title('', '', false));
  $content = get_the_content('', '', false);
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
  <section class="sec-courseDetail">
    <div class="container">
<?php
if ( have_posts() ) :
  while ( have_posts() ) : the_post();
?>
      <h2 class="secTtl"><?php echo $title ?></h2>
      <p class="courseTxt"><?php echo get_field('description') ?></p>
      <ul class="coursePicList">
    <?php $imgPath = esc_url(get_field('img01'));
    if (!empty ($imgPath)) : ?>
        <li><img src="<?php echo $imgPath ?>" alt="<?php echo $title ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" /></li>
    <?php endif; ?>
    <?php $imgPath = esc_url(get_field('img02'));
    if (!empty ($imgPath)) : ?>
        <li><img src="<?php echo $imgPath ?>" alt="<?php echo $title ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" /></li>
    <?php endif; ?>
    <?php $imgPath = esc_url(get_field('img03'));
    if (!empty ($imgPath)) : ?>
        <li><img src="<?php echo $imgPath ?>" alt="<?php echo $title ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" /></li>
    <?php endif; ?>
      </ul>
      <ul class="courseInfo">
    <?php if (!empty (esc_html(get_field('course_name')))) : ?>
        <li>
          <h3 class="courseInfo_ttl"><?php echo esc_html(get_field('course_name')) ?></h3>
      <?php if (!empty (get_field('course_description'))) : ?>
          <p><?php echo get_field('course_description') ?></p>
      <?php endif; ?>
          <p class="courseInfo_samplePicTtl">作品見本</p>
          <ul class="courseInfo_pic">
      <?php $imgPath = esc_url(get_field('course_img01'));
      if (!empty ($imgPath)) : ?>
            <li><img src="<?php echo $imgPath ?>" alt="<?php echo $title ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" /></li>
      <?php endif; ?>
      <?php $imgPath = esc_url(get_field('course_img02'));
      if (!empty ($imgPath)) : ?>
            <li><img src="<?php echo $imgPath ?>" alt="<?php echo $title ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" /></li>
      <?php endif; ?>
      <?php $imgPath = esc_url(get_field('course_img03'));
      if (!empty ($imgPath)) : ?>
            <li><img src="<?php echo $imgPath ?>" alt="<?php echo $title ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" /></li>
      <?php endif; ?>
          </ul>
          <?php echo get_field('course_table') ?>
          <p class="btn"><a href="/apply/?<?php echo $title ?>+<?php esc_html(get_field('course_name')) ?>">講座申込はこちら</a></p>
        </li>
    <?php endif; ?>
      </ul>
<?php
  endwhile;
else :
?>
      <p>講座情報は準備中です。</p>
<?php
endif;
?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
