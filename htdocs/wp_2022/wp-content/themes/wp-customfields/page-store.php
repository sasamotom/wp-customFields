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
?>
  <section class="sec-store">
    <div class="container">
      <ul class="storeList">
<?php
      $rows = get_field('store');
      if ($rows) :
        foreach($rows as $row) :
?>
        <li>
          <?php if (!empty(esc_html($row['name']))) : ?>
          <h2 class="secTtl"><?php echo esc_html($row['name']); ?></h2>
          <?php endif; ?>
          <div class="stotreInfoContainer">
            <div class="pic">
          <?php if (!empty(esc_url($row['image']))) : ?>
            <img src="<?php echo esc_url($row['image']) ?>" alt="<?php echo esc_html($row['name']) ?>" <?php echo get_image_size(esc_url($row['image'])) ?> loading="lazy" />
          <?php else : ?>
              <img src="<?php echo site_url(); ?>/wp-content/uploads/default.jpg" alt="no image" width="800" height="500" loading="lazy" />
          <?php endif; ?>
            </div>
            <dl class="storeTbl">
          <?php if (!empty($row['address'])) : ?>
              <dt>Address</dt>
              <dd><?php echo $row['address']; ?></dd>
          <?php endif; ?>
          <?php if (!empty($row['tel'])) : ?>
              <dt>Tel</dt>
              <dd><?php echo esc_html($row['tel']); ?></dd>
          <?php endif; ?>
          <?php if (!empty($row['openingtime'])) : ?>
              <dt>営業時間</dt>
              <dd><?php echo esc_html($row['openingtime']); ?></dd>
          <?php endif; ?>
          <?php if (!empty($row['closedday'])) : ?>
              <dt>定休日</dt>
              <dd><?php echo esc_html($row['closedday']); ?></dd>
          <?php endif; ?>
            </dl>
          </div>
        </li>
<?php
        endforeach;
      endif;
?>
      </ul>
    </div>
  </section>
<?php
    endwhile;
  endif;
?>
</main>

<?php get_footer(); ?>
