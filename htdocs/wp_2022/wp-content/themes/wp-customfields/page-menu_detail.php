<?php get_header(); ?>

<?php
// // SQLを自分で発行して、理想の並び順で一括でデータを取得するやり方-----ここから↓
// global $wpdb;
// $sql = "
//   SELECT
//     wp_posts.*, wp_term_relationships.term_taxonomy_id, wp_terms.term_order, wp_terms.name as termname
//   FROM wp_posts
//     LEFT JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id)
//     LEFT JOIN wp_terms ON (wp_term_relationships.term_taxonomy_id = wp_terms.term_id)
//   WHERE
//     wp_posts.post_type = 'menu'
//     AND
//     ((wp_posts.post_status = 'publish'))
//   ORDER BY
//     wp_terms.term_order ASC,
//     wp_posts.menu_order ASC
// ";
// $results = $wpdb->get_results($sql);
// foreach ($results as $post):
//   setup_postdata($post);
//   // ここでデータを表示する（とりあえず、タイトルと詳細テキスト）
//   echo '<p>'.$post->termname.'</p>';    // タクソノミー名（LIMITED DONUTS, FUWAFUWA DONUTS, MOIST DONUTSのどれか）
//   echo '<p>'.esc_html(the_title()).'</p>';
//   echo '<p>'.get_field('text').'</p>';
// endforeach;
// // SQLを自分で発行して、理想の並び順で一括でデータを取得するやり方-----ここまで↑
?>

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
          <ul class="donutsList -detail">
<?php
      while ( $the_query->have_posts() ) : $the_query->the_post();
?>
            <li id="<?php echo $post->ID; ?>">
              <dl class="donutsInfo">
                <dd class="donutsInfo_pic">
                <?php $imgPath = esc_url(get_field('image'));
                if ($imgPath) : ?>
                  <img src="<?php echo $imgPath ?>" alt="<?php echo $name ?>" <?php echo get_image_size($imgPath) ?> loading="lazy" />
                <?php else : ?>
                  <img src="<?php echo site_url(); ?>/wp-content/uploads/default.jpg" alt="no image" width="800" height="500" loading="lazy" />
                <?php endif; ?>
                </dd>
                <div class="txts">
                  <dt class="donutsInfo_name"><?php esc_html(the_title()); ?></dt>
                  <?php if (!empty(get_field('text'))) : ?>
                    <dd class="donutsInfo_txt"><?php echo get_field('text'); ?></dd>
                  <?php endif; ?>
                  <?php if (!empty(esc_html(get_field('price')))) : ?>
                  <dd class="donutsInfo_price">¥<?php echo esc_html(get_field('price')); ?><span class="-small">（税込）</span></dd>
                  <?php endif; ?>
                </div>
              </dl>
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
