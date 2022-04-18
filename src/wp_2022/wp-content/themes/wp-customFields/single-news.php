<?php get_header(); ?>

<?php
  global $post;
  $post_type = $post->post_type;
  $post_name = (get_post_type_object($post_type))->label;
  $cat = get_queried_object();
  $cat_name = esc_html($cat -> name);
  $cat_slug = esc_html($cat -> slug);
  $title = esc_html(get_the_title('', '', false));
?>



<main id="main" class="site2">
  <div class="pageTtlContainer news">
    <div class="container">
      <h1 class="site2PageTtl"><?php echo $title ?></h1>
    </div>
  </div>
  <div class="sec-newsDetail">
    <div class="container">
<?php
if ( have_posts() ) :
  while ( have_posts() ) : the_post();
?>
      <ul>
        <li><time datatime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y/n/j'); ?></time></li>
<?php
    $tax_slug = 'cat_news';         // タクソノミースラッグ指定
    $cat_terms = wp_get_object_terms($post->ID, $tax_slug); // タームの情報を取得
    if (!empty($cat_terms)) { // 変数が空でなければ true
      if (!is_wp_error($cat_terms)) {
        foreach ($cat_terms as $cat_term) {
          echo '<li class="newsList_tag '.$cat_term->slug.'">'.$cat_term->name.'</li>';
        }
      }
    }
?>
      </ul>
    <?php if (!empty(get_field('text'))) : ?>
      <div class="editContainer"><?php echo get_field('text') ?></div>
    <?php endif; ?>

<?php
    $rows = get_field('movie');
    if ($rows) :
?>
      <section class="sec-movie">
        <h2 class="secTtl">Movie</h2>
        <ul class="movieList">
<?php
      foreach($rows as $row) :
?>
        <?php if ($row['url']) : ?>
          <li>
            <div class="thumb" data-micromodal-trigger="modal-1">
              <figure>
                <img src="https://img.youtube.com/vi/<?php echo esc_html($row['url']); ?>/hqdefault.jpg" alt="">
                <?php if ($row['title']) : ?>
                <figcaption><?php echo esc_html($row['title']); ?></figcaption>
                <?php endif; ?>
              </figure>
            </div>
            <div class="c-modal movieModal" id="modal-1" aria-hidden="true">
              <div class="c-modal_overlay" tabindex="-1" data-micromodal-close>
                <div class="c-modal_container" role="dialog" aria-modal="true" aria-labelledby="modal">
                  <div class="c-modal_content">
                    <div class="movieContainer">
                      <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo esc_html($row['url']); ?>?enablejsapi=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                  </div>
                  <div class="c-modal_close" aria-label="Close modal" data-micromodal-close></div>
                </div>
              </div>
            </div>
          </li>
        <?php endif; ?>
<?php
      endforeach;
?>
        </ul>
      </section>
<?php
    endif;
?>
<?php
      $prev_post =  get_next_post(false);
      $next_post = get_previous_post(false);
?>
      <div class="preNextBtnContainer">
        <div class="btn -back">
      <?php if (!empty( $prev_post )): ?>
          <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>">前の記事へ<br><?php echo esc_html(get_the_title($prev_post->ID)); ?></a>
      <?php else : ?>
          <span>前の記事へ</span>
      <?php endif; ?>
        </div>
        <div class="btn">
      <?php if (!empty( $next_post )): ?>
          <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>">次の記事へ<br><?php echo esc_html(get_the_title($next_post->ID)); ?></a>
      <?php else : ?>
          <span>次の記事へ</span>
      <?php endif; ?>
        </div>
      </div>
      <div class="btn">
        <a href="/site2/news/">News一覧</a>
      </div>
    </div>
  </div>


<?php
    $items = get_field('items');
    if ($items) :
?>
  <section class="sec-Items">
    <div class="container">
      <h2 class="secTtl">関連アイテム</h2>
      <ul class="itemList">

<?php
      foreach($items as $item) :
?>
        <li><?php echo do_shortcode('[InnerLiOfItem id="'.$item. '"]'); ?></li>
<?php
      endforeach;
?>
      </ul>
    </div>
  </section>
<?php
    endif;
?>

<?php
  endwhile;
else :
?>
      <p>準備中です。</p>
    </div>
  </div>
<?php
endif;
?>
</main>

<?php get_footer(); ?>
