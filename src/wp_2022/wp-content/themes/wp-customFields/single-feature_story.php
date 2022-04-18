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
<!--画像にテキストを挿入するスクリプト -->
<script>
  function drawText(canvasId, imgPath, text) {
    const image = new Image();
    image.src = imgPath;
    image.onload = (function () {
      // 画像ロードが完了してからキャンバスの準備をする
      const canvas = document.getElementById(canvasId);
      const ctx = canvas.getContext('2d');
      // キャンバスのサイズを画像サイズに合わせる
      // 文字のサイズにばらつきが出るのが嫌なので、横幅固定とする
      canvas.width = 600;
      canvas.height = 600 * image.height / image.width;
      // キャンバスに画像を描画（開始位置0,0）
      ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
      // 文字のスタイルを指定
      ctx.font = '18px serif';
      ctx.fillStyle = '#555555';
      // 文字の配置を指定（左上基準にしたければtop/left、文字の中心にしたければcenter）
      ctx.textBaseline = 'bottom';
      ctx.textAlign = 'right';
      // 座標を指定して文字を描く（右下）
      const x = canvas.width  - 20;
      const y = canvas.height - 20;
      ctx.fillText(text, x, y);
      console.log(canvasId);
    });
  }
</script>

<main id="main" class="site2">
  <div class="pageTtlContainer feature_story">
    <div class="container">
      <h1 class="site2PageTtl"><?php echo $post_name; ?></h1>
    </div>
  </div>
  <section class="sec-featureDtl">
    <div class="container">
<?php
  if ( have_posts() ) :
    while ( have_posts() ) : the_post();
?>
      <p><time datatime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y/n/j'); ?></time></p>
      <h2 class="secTtl"><?php echo esc_html(the_title()); ?></h2>
      <?php if (!empty(get_field('text'))) : ?>
      <p class="featureTxt"><?php echo get_field('text'); ?></p>
      <?php endif; ?>
<?php
      $rows = get_field('iteminfo');
      if ($rows) :
?>
      <ul class="featureDtl">
<?php
        foreach($rows as $index => $row) :
          $name = esc_html($row['itemname']);
          $imgPath = esc_url($row['itemimage']);
          $text = $row['itemtext'];
          $detail = esc_html($row['itemdetail']);
          $link = esc_url($row['itemlink']);
?>
        <li>
          <dl class="featureItem">

          <?php if (!empty($imgPath)) : ?>
            <dd class="featureItem_pic"><canvas id ="can<?php echo $index; ?>"></canvas></dd>
            <script>drawText('can<?php echo $index; ?>', '<?php echo $imgPath; ?>', '<?php if (!empty($name)) { echo $name.' / '; }; ?><?php echo esc_html(the_title()); ?>');</script>
          <?php endif; ?>
            <div class="featureItem_txts">
          <?php if (!empty($name)) : ?>
              <dt class="featureItem_name"><h3 class="featureItem_ttl"><?php echo $name; ?></h3></dt>
          <?php endif; ?>
          <?php if (!empty($text)) : ?>
              <dd class="featureItem_txt"><?php echo $text; ?></dd>
          <?php endif; ?>
          <?php if (!empty($detail)) : ?>
              <dd class="featureItem_tbl">
                <table  le class="featureTbl">
                  <tbody>
            <?php
              $lines = explode("\n", $detail);  // 各行毎にデータを分割
              $lineLen = count($lines);         // 何行のデータがあるか
              foreach ($lines as $line) :
                $datas = explode("^", $line);
                if (count($datas) === 2) :
            ?>
                    <tr>
                      <th><?php echo $datas[0]; ?></th>
                      <td><?php echo $datas[1]; ?></td>
                    </tr>
            <?php
                endif;
              endforeach;
            ?>
                  </tbody>
                </table>
              </dd>
          <?php endif; ?>
          <?php if (!empty($link)) : ?>
              <dd class="featureItem_link btn">
                <a href="<?php echo $link; ?>">商品ページへ</a>
              </dd>
          <?php endif; ?>
            </div>
          </dl>
        </li>
<?php
        endforeach;
?>
      </ul>
<?php
      endif;
?>
    </div>
  </section>
    <?php if (!empty(esc_html(get_field('tag')))) : ?>
  <?php echo do_shortcode('[newItemList tag="'.esc_html(get_field('tag')).'" ttl="#'.esc_html(get_field('tag')).'"]'); ?>
    <?php endif; ?>
<?php
    endwhile;
    wp_reset_postdata();
?>
<?php
  else :
?>
      <p>Feature storyはありません。</p>
    </div>
  </section>
<?php
  endif;
?>
  <div class="sec-btn">
    <div class="container">
      <div class="btn">
        <a href="/site2/feature_story/">Feature story一覧</a>
      </div>
    </div>
  </div>


</main>


<?php get_footer(); ?>
