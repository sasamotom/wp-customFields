<?php
// *********************************************
// ACFを使わずにカスタムフィールドを定義する！！（自由に繰り返せるver）
// 使用場所： /site3/coupon/  （←固定ページ IDは341）
// 項目： クーポン画像、有効期限開始日、有効期限終了日
//      （上記3項目を1グループとして、好きなだけ繰り返せるようにする）
// 参考URL
//  https://cly7796.net/blog/cms/add-custom-fields-with-functions-php/
//  https://fit-jp.com/customfield/
//  https://wordpress-template-media.com/customfield-mediaupload/#index_id1
//  https://gist.github.com/mimosafa/5360998
//
// - こうなったよMEMO -
//  カスタムフィールドのデータを配列で保存することで、実現した。
//  $coupon_list[0][$i]が画像URL
//  $coupon_list[1][$i]が有効期限開始日
//  $coupon_list[2][$i]が有効期限終了日
//  この0,1,2というのをEnumで定義したかったが、phpでEnumを使えるようになったのは
//  ごく最近（ver8.1）のことらしく、このDocker環境では使えない。
//  C言語をベースとしているはずなのに、Enum使えないとかどうなってるんだ。
//  オブジェクト指向に寄せた結果か。。。ガッカリですわ。
//  ver8.1以降のphpが使える場合は、絶対Enumにする。定数直書きとかないわ。(2022/4/30 Megumi.S)
// *********************************************

// --------------------------------------------------------
// カスタムフィールドの追加
// --------------------------------------------------------
function add_custom_field_coupon() {
  $post_id = '';
  if(isset($_GET['post']) || isset($_POST['post_ID'])) {
    $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
  }
  global $wpdb;
  $id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = 'coupon'");
  $page_id = esc_html($id);  // スラッグが「coupon」の固定ページの ID

  // - MEMO -
  //  add_meta_box関数ではカスタムフィールドを「特定の固定ページに表示する」ということができないため、
  //  add_meta_box関数を呼び出すか呼び出さないかで実現させる
  if ($post_id == $page_id) {
    add_meta_box('coupon_info', 'クーポン情報', 'insert_coupon_fields', 'page', 'normal');
  }
}
add_action( 'admin_menu', 'add_custom_field_coupon' );

// --------------------------------------------------------
// カスタムフィールドのHTMLを追加する時の処理
// --------------------------------------------------------
function insert_coupon_fields() {
  global $post;
  // nonceの追加
  wp_nonce_field('action-coupon_info', 'nonce-coupon_info');

  $coupon_list = get_post_meta( $post->ID, 'coupon_list', true );
?>
  <table class="form-table">
    <thead>
      <tr valign="top">
        <th scope="col"><label>クーポン画像</label></th>
        <th scope="col"><label>クーポン有効期限（開始日〜終了日）</label></th>
        <th scope="col"><label>control</label></th>
      <tr>
    </thead>
    <tbody id="area-tbody">
<?php
  if (!empty($coupon_list)) {
    for ($i = 0 ; $i < count($coupon_list[0]); $i++) {
?>
      <tr valign="top" class="area-tr">
        <td>
          <div class="media">
            <img src="<?php echo esc_url($coupon_list[0][$i]); ?>" alt="">
          </div>
          <input style="width:0;height:0;padding:0;margin:0;visibility:hidden" name="coupon_image[]" type="text" value="<?php echo esc_url($coupon_list[0][$i]); ?>" />
          <input style="width:80px" type="button" name="media" value="画像選択" />
          <input style="width:80px" type="button" name="media-clear" value="画像削除" />
        </td>
        <td><input type="date" name="coupon_start[]" value="<?php echo esc_html($coupon_list[1][$i]); ?>" class="regular-text" /><br>　〜<br><input type="date" name="coupon_end[]" value="<?php echo esc_html($coupon_list[2][$i]); ?>" class="regular-text" /></td>
        <td>
          <input type="button" class="button delete-tr" id="" value="削除" />
          <input type="button" class="button move-up-tr" id="" value="↑" />
          <input type="button" class="button move-down-tr" id="" value="↓" />
        </td>
      </tr>
<?php
    }
  }
  else {
?>
      <tr valign="top" class="area-tr">
        <td>
          <div class="media"></div>
          <input style="width:0;height:0;padding:0;margin:0;visibility:hidden" name="coupon_image[]" type="text" value="" />
          <input style="width:80px" type="button" name="media" value="画像選択" />
          <input style="width:80px" type="button" name="media-clear" value="画像削除" />
        </td>
        <td><input type="date" name="coupon_start[]" value="" class="regular-text" /><br>　〜<br><input type="date" name="coupon_end[]" value="" class="regular-text" /></td>
        <td>
          <input type="button" class="button delete-tr" id="" value="削除" />
          <input type="button" class="button move-up-tr" id="" value="↑" />
          <input type="button" class="button move-down-tr" id="" value="↓" />
        </td>
      </tr>
<?php
  }
?>
      <tr valign="top">
        <td colspan="3">
          <input type="button" class="button" id="add-tr" value="行を追加" />
          <span class="description">クーポンが複数ある場合は行を追加してください</span>
        </td>
      </tr>
    </tbody>
  </table>
  <script>
    (function($){
      // 行を追加ボタン押下処理
      $('#area-tbody').on('click', '#add-tr', function(){
        var areaTr = '\
        <tr valign="top" class="area-tr">\
          <td>\
              <div class="media"></div>\
              <input style="width:0;height:0;padding:0;margin:0;visibility:hidden" name="coupon_image[]" type="text" value="" />\
              <input style="width:80px" type="button" name="media" value="画像選択" />\
              <input style="width:80px" type="button" name="media-clear" value="画像削除" />\
          </td>\
          <td><input type="date" name="coupon_start[]" value="" class="regular-text" /><br>　〜<br><input type="date" name="coupon_end[]" value="" class="regular-text" /></td>\
          <td>\
            <input type="button" class="button delete-tr" id="" value="削除" />\
            <input type="button" class="button move-up-tr" id="" value="↑" />\
            <input type="button" class="button move-down-tr" id="" value="↓" />\
          </td>\
        </tr>';
        $(this).closest('tr').before(areaTr);
      });
      // 削除ボタン押下処理
      $('#area-tbody').on('click', '.delete-tr', function(){
        $(this).closest('tr').remove();
      });
      // ↑ボタン押下処理
      $('#area-tbody').on('click', '.move-up-tr',function(){
        var moveTr = $(this).parent().parent();
        if($(moveTr).prev('tr')){
          $(moveTr).insertBefore($(moveTr).prev('tr'));
        }
      });
      // ↓ボタン押下処理
      $('#area-tbody').on('click', '.move-down-tr',function(){
        var moveTr = $(this).parent().parent();
        if($(moveTr).next('tr') && $(moveTr).next('tr').hasClass('area-tr')){
          $(moveTr).insertAfter($(moveTr).next('tr'));
        }
      });
      // 画像選択ボタン押下処理
      $('body').on('click', 'input:button[name=media]' , function(e) {
        e.preventDefault();
        var custom_uploader;
        custom_uploader = wp.media({
          title: "画像を選択", //タイトルのテキストラベル
          button: {
            text: "画像を設定" //ボタンのテキストラベル
          },
          library: {
            type: "image" //imageにしておく。
          },
          multiple: false //選択できる画像を1つだけにする。
        });
        var imgUrlBox = $(this).siblings('input:text[name="coupon_image[]"]');
        var picBox = $(this).siblings(".media");
        custom_uploader.on("select", function() {
          var images = custom_uploader.state().get("selection");
          /* file の中に選択された画像の各種情報が入っている */
          images.each(function(file){
            picBox.empty(); // mediaの中身をクリア
            imgUrlBox.val(""); // テキストフォームをクリア
            picBox.append('<img src="'+file.attributes.url+'" />'); // プレビュー用にメディアアップローダーで選択した画像を表示させる
            imgUrlBox.val(file.attributes.url); // テキストフォームに選択したURLを追加
          });
        });
        custom_uploader.open();
      });
      //クリアボタン押下処理
      $('body').on('click', 'input:button[name=media-clear]' , function() {
        $(this).siblings('input:text[name="coupon_image[]"]').val(""); // テキストフォームをクリア
        $(this).siblings(".media").empty(); // mediaの中身をクリア
      });
    })(jQuery);
  </script>
<?php
}

// --------------------------------------------------------
// カスタムフィールドの保存
// --------------------------------------------------------
function save_custom_field_coupon($post_id) {
  if (isset($_POST['nonce-coupon_info'] ) && $_POST['nonce-coupon_info']) {
    if (check_admin_referer('action-coupon_info', 'nonce-coupon_info')) {
      $coupon_image_array = $_POST['coupon_image'];
      $coupon_start_array = $_POST['coupon_start'];
      $coupon_end_array = $_POST['coupon_end'];
      $coupon_list = [3];
      if (is_array($coupon_image_array) && is_array($coupon_start_array) && is_array($coupon_end_array)) {
        $coupon_list[0] = $coupon_image_array;
        $coupon_list[1] = $coupon_start_array;
        $coupon_list[2] = $coupon_end_array;
      }
      if(!empty($_POST['coupon_image'])) {
        update_post_meta($post_id, 'coupon_list', $coupon_list);
      }
      else {
        delete_post_meta($post_id, 'coupon_list');
      }
    }
  }
}
add_action( 'save_post', 'save_custom_field_coupon' );

?>
