<?php get_header(); ?>

<main id="main" class="site3">
  <div class="pageTtlContainer">
    <div class="container">
      <h1 class="site3PageTtl">MENU</h1>
    </div>
  </div>

  <div class="sec-menu">
    <div class="container">
      <ul class="categoryLinks">
<?php
  $args = array(
    'orderby'       => 'menu_order',
    'order'         => 'DESC',
  );
  $terms = get_terms('cat_menu', $args);    // タクソノミーの指定
  foreach ($terms as $term) {
    echo '<li><a href="#anc-'.$term->term_id.'"><span>'.$term->name.'</span></a></li>';
  }
?>
      </ul>
      <ul class="donutsCategory">
<?php
  foreach ($terms as $term) :
    // $paged = (int) get_query_var('paged');
    $args = array(
      'posts_per_page' => -1, // 全件取得
      // 'paged' => $paged,
      'orderby' => 'menu_order',
      'order' => 'asc',
      'post_type' => 'menu',
      'post_status' => 'publish',
      'tax_query' => array(
        'relation' => 'OR',
        array(
          'taxonomy' => 'cat_menu',
          'field' => 'slug',
          'terms' => $term->slug,
        ),
      ),
    );
    $the_query = new WP_Query($args);
    if ( $the_query->have_posts() ) :
?>
        <li id ="anc-<?php echo $term->term_id; ?>">
          <h2 class="secTtl"><?php echo $term->name; ?></h2>
          <p class="cateTxt"><?php echo $term->description; ?></p>
          <ul class="donutsList">
<?php
      while ( $the_query->have_posts() ) : $the_query->the_post();
?>
            <li>
              <a href="/site3/menu_detail/#<?php echo $post->ID; ?>">
                <dl class="donutsInfo">
                  <dd class="donutsInfo_pic">
                  <?php $imgPath = esc_url(get_field('image'));
                  if ($imgPath) : ?>
                    <img src="<?php echo $imgPath ?>" alt="<?php echo $name ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" />
                  <?php else : ?>
                    <img src="<?php echo site_url(); ?>/wp-content/uploads/default.jpg" alt="no image" width="800" height="500" loading="lazy" />
                  <?php endif; ?>
                  </dd>
                  <dt class="donutsInfo_name"><?php esc_html(the_title()); ?></dt>
                  <?php if (!empty(esc_html(get_field('price')))) : ?>
                  <dd class="donutsInfo_price">¥<?php echo esc_html(get_field('price')); ?><span class="-small">（税込）</span></dd>
                  <?php endif; ?>
                </dl>
              </a>
            </li>
<?php
      endwhile;
?>
          </ul>
        </li>
<?php
          wp_reset_postdata();
    endif;
  endforeach;
?>
      </ul>
    </div>
  </div>
</main>

<?php get_footer(); ?>
