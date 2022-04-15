<?php get_header(); ?>

<?php
  global $post;
  $post_type = $post->post_type;
  $post_name = (get_post_type_object($post_type))->label;
  $cat = get_queried_object();
  $cat_name = esc_html($cat -> name);
  $cat_slug = esc_html($cat -> slug);
  $title = esc_html(get_the_title('', '', false));
  $content = get_the_content('', '', false);
?>

<main id="main" class="site2">
  <div class="pageTtlContainer item">
    <div class="container">
      <h1 class="site2PageTtl"><?php echo $post_name ?></h1>
    </div>
  </div>
  <div class="sec-itemDetail">
    <div class="container">
<?php
if ( have_posts() ) :
  while ( have_posts() ) : the_post();
?>
      <dl class="itemData">
<?php
    $images = get_field('images');
    if ($images) :
 ?>
        <dd class="itemData_pic">
          <div class="swiper mySwiperMain">
            <div class="swiper-wrapper">
      <?php foreach ($images as $img) : ?>
              <div class="swiper-slide"><img src="<?php echo esc_url($img);?>" alt="<?php echo $title ?>" <?php echo esc_url($img); ?> loading="lazy"></div>
      <?php endforeach; ?>
            </div>
          </div>
          <div class="thumbContainer">
            <div class="swiper mySwiperThumb">
              <div class="swiper-wrapper">
      <?php foreach ($images as $img) : ?>
                <div class="swiper-slide"><img src="<?php echo esc_url($img);?>" alt="<?php echo $title ?>" <?php echo esc_url($img); ?> loading="lazy"></div>
      <?php endforeach; ?>
              </div>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
          </div>
        </dd>
<?php
    endif;
?>
        <div class="itemData_info">
          <dt><h2 class="itemData_name"><?php echo $title ?></h2></dt>
          <dd class="itemData_cate">
            <ul class="cateList">
              <?php
                $tax_slug = 'cat_item';         // タクソノミースラッグ指定
                $cat_terms = wp_get_object_terms($post->ID, $tax_slug); // タームの情報を取得
                if (!empty($cat_terms)) { // 変数が空でなければ true
                  if (!is_wp_error($cat_terms)) {
                    foreach ($cat_terms as $cat_term) {
                      echo '<li>'.$cat_term->name.'</li>';
                    }
                  }
                }
              ?>
            </ul>
          </dd>
          <dd class="itemData_txt"><?php echo get_field('description') ?></dd>
          <dd class="itemData_table">
            <dl class="itemTbl">
              <?php $group_field = get_field('detail'); ?>
              <dt>サイズ</dt>
              <dd><?php echo esc_html($group_field['size']); ?></dd>
              <dt>発売日</dt>
              <dd><?php echo esc_html($group_field['launch']); ?></dd>
              <dt>価格</dt>
              <dd><?php echo esc_html($group_field['price']); ?></dd>
            </dl>
          </dd>
          <dd class="itemData_tag">


<?php
    $posttags = get_the_tags();
    if ($posttags) :
?>
            <ul class="tagList">
              <?php
      foreach($posttags as $tag) {
        echo '<li>' . $tag->name . '</li>';
      }
?>
            </ul>
<?php
    endif;
?>
          </dd>
        </div>
      </dl>
      <div class="goodBtnContainer">
        <button class="goodBtn">Good</button>
        <p class="goodCnt">いいね <span><?php echo get_field('good'); ?></span>件</p>
      </div>

      <ul class="itemStory">
<?php
    $rows = get_field('extra');
    if ($rows) :
      foreach($rows as $row) :
?>
        <li>
          <div class="txts">
        <?php if ($row['subtitle']) : ?>
            <h3 class="txts_ttl"><?php echo esc_html($row['subtitle']); ?></h3>
        <?php endif; ?>
        <?php if ($row['text']) : ?>
            <p class="txts_txt"><?php echo $row['text']; ?></p>
        <?php endif; ?>
          </div>
        <?php if ($row['image']) : ?>
          <div class="pic"><img src="<?php echo esc_url(esc_html($row['image'])); ?>" alt="" <?php echo get_image_size(esc_url(esc_html($row['image']))); ?> loading="lazy"></div>
        <?php endif; ?>
        </li>
<?php
      endforeach;
    endif;
?>
      </ul>

<?php
  endwhile;
else :
?>
      <p>準備中です。</p>
<?php
endif;
?>
      <div class="btn">
        <a href="/site2/item/">Item一覧</a>
      </div>
    </div>
  </div>
</main>

<?php get_footer(); ?>
