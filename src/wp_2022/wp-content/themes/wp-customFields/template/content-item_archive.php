<!-- Itemアーカイブ -->
<?php
  global $post;
  $post_type = $post->post_type;
  $post_name = (get_post_type_object($post_type))->label;
  if (is_tax()) {
    $cat = get_queried_object();
    $cat_name = $cat -> name;
  }
?>

<main id="main" class="site2">
  <div class="pageTtlContainer item">
    <div class="container">
      <h1 class="site2PageTtl"><?php echo $post_name; ?><?php if (is_tax()) { echo ' - '.$cat_name ; }?></h1>
    </div>
  </div>

  <div class="sec-newsList">
    <div class="container">
      <ul class="cateList">
<?php if (is_tax()) : ?>
        <li><a href="/site2/item/">全て</a></li>
<?php else : ?>
        <li><span>全て</span></li>
<?php endif; ?>
<?php
  $terms = get_terms('cat_item', $args);    // タクソノミーの指定
  if (is_tax()) {
    $ex_term = get_queried_object();          // 現在選択中のタクソノミー
    foreach ($terms as $term) {
      if($ex_term->term_id === $term->term_id){
        echo '<li><span>'.$term->name.'</span></li>';
      }
      else {
        echo '<li><a href="'.get_term_link($term).'">'.$term->name.'</a></li>';
      }
    }
    $type = get_query_var('cat_item');
    $args = array(
      'posts_per_page' => 16,
      'paged' => $paged,
      'orderby' => 'date',
      'order' => 'desc',
      'post_type' => 'item',
      'post_status' => 'publish',
      'tax_query' => array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'cat_item',
            'field' => 'slug',
            'terms' => $type,
        ),
      ),
    );
  }
  else {
    $args = array(
      'orderby'       => 'menu_order',
      'order'         => 'ASC',
    );
    $terms = get_terms('cat_item', $args);    // タクソノミーの指定
    foreach ($terms as $term) {
      echo '<li><a href="'.get_term_link($term).'">'.$term->name.'</a></li>';
    }
    $paged = (int) get_query_var('paged');
    $args = array(
      'posts_per_page' => 16,
      'paged' => $paged,
      'orderby' => 'date',
      'order' => 'desc',
      'post_type' => 'item',
      'post_status' => 'publish'
    );
  }
?>
      </ul>
      <ul class="itemList">
<?php
  $the_query = new WP_Query($args);
  if ( $the_query->have_posts() ) :
    while ( $the_query->have_posts() ) : $the_query->the_post();
?>
        <li>
          <a href="<?php esc_url(the_permalink()); ?>">
            <dl class="itemDtl">
              <dd class="itemDtl_pic">
                <!-- 画像優先度：アイキャッチ→画像（登録必須）の１つめ -->
      <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail(); ?>
      <?php else :
        $images = get_field('images');
        $imgPath = $images[0];
        ?>
                <img src="<?php echo $imgPath ?>" alt="<?php echo $name ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" />
      <?php endif; ?>
              </dd>
              <dt class="itemDtl_name"><?php esc_html(the_title()); ?></dt>
              <dd class="itemDtl_price"><?php esc_html(get_field('price')); ?></dd>
            </dl>
          </a>
        </li>
<?php
    endwhile;
    wp_reset_postdata();
?>
    </ul>
<?php
  else :
?>
    <p>Itemはありません。</p>
<?php
  endif;
?>
<?php pagination($the_query->max_num_pages, 2) ?>
    </div>
  </div>
</main>


