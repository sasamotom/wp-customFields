<!-- Newsアーカイブ -->
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
  <div class="pageTtlContainer news">
    <div class="container">
      <h1 class="site2PageTtl"><?php echo $post_name; ?><?php if (is_tax()) { echo ' - '.$cat_name ; }?></h1>
    </div>
  </div>

  <div class="sec-newsList">
    <div class="container">
      <ul class="cateList">
<?php if (is_tax()) : ?>
        <li><a href="/site2/news/">全て</a></li>
<?php else : ?>
        <li><span>全て</span></li>
<?php endif; ?>
<?php
  $args = array(
    'orderby'       => 'menu_order',
    'order'         => 'DESC',
  );
  $terms = get_terms('cat_news', $args);    // タクソノミーの指定
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
    $type = get_query_var('cat_news');
    $args = array(
      'posts_per_page' => 10,
      'paged' => $paged,
      'orderby' => 'date',
      'order' => 'desc',
      'post_type' => 'news',
      'post_status' => 'publish',
      'tax_query' => array(
        'relation' => 'OR',
        array(
          'taxonomy' => 'cat_news',
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
    $terms = get_terms('cat_news', $args);    // タクソノミーの指定
    foreach ($terms as $term) {
      echo '<li><a href="'.get_term_link($term).'">'.$term->name.'</a></li>';
    }
    $paged = (int) get_query_var('paged');
    $args = array(
      'posts_per_page' => 10,
      'paged' => $paged,
      'orderby' => 'date',
      'order' => 'desc',
      'post_type' => 'news',
      'post_status' => 'publish'
    );
  }
?>
      </ul>
      <ul class="newsList">
<?php
  $the_query = new WP_Query($args);
  if ( $the_query->have_posts() ) :
    while ( $the_query->have_posts() ) : $the_query->the_post();
?>
        <li>
          <a href="<?php esc_url(the_permalink()); ?>">
            <dl class="newsList_item">
              <dd class="newsList_date"><time datatime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y/n/j'); ?></dd>
              <?php
              $tax_slug = 'cat_news';       // タクソノミースラッグ指定
              $cat_terms = wp_get_object_terms($post->ID, $tax_slug); // タームの情報を取得
              if(!empty($cat_terms)) {
                if(!is_wp_error($cat_terms)){ // 変数が WordPress Error でなければ true
                  foreach($cat_terms as $cat_term) {
                      echo '<dd class="newsList_tag '.$cat_term->slug.'">'.$cat_term->name.'</dd>';
                  }
                }
              }
              ?>
              <dt class="newsList_txt"><?php esc_html(the_title()); ?></dt>
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
    <p>Newsはありません。</p>
<?php
  endif;
?>
<?php pagination($the_query->max_num_pages, 2) ?>
    </div>
  </div>
</main>
