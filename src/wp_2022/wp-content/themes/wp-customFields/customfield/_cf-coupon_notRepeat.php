<?php
// このファイルは今回使用しないが、繰り返さないカスタムフィールドの作り方の例としてとっておく。

// *********************************************
// ACFを使わずにカスタムフィールドを定義する！！（繰り返さないver）
// 使用場所： /site3/coupon/  （←固定ページ IDは341）
// 項目： クーポン画像、有効期限開始日、有効期限終了日
//      （上記3項目を1グループとして、好きなだけ繰り返せるようにする）
// 参考URL
//  https://cly7796.net/blog/cms/add-custom-fields-with-functions-php/
//  https://fit-jp.com/customfield/
//  https://wordpress-template-media.com/customfield-mediaupload/#index_id1
// *********************************************

// カスタムフィールドの追加
//////////////////////////////////////////////////////////////
// add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
//  管理画面にmeta boxを追加する。
//    $id: 投稿画面の入力部分に付与されるID。必須。
//    $title: 投稿画面の入力部分に表示されるタイトル。必須。
//    $callback: 投稿画面の入力部分にHTMLを出力する関数。必須。
//    $screen: カスタムフィールドを追加する投稿タイプ。
//    $context: カスタムフィールド追加する場所。(normal,advanced,sideのいずれか)
//    $priority: 表示される優先度。
//    $callback_args: コールバックに渡す引数。
//////////////////////////////////////////////////////////////
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

// カスタムフィールドのHTMLを追加する時の処理
function insert_coupon_fields() {
  global $post;
  // nonceの追加
  wp_nonce_field('action-coupon_info', 'nonce-coupon_info');

  //　管理画面に表示される入力エリア作成。「get_post_meta()」は現在入力されている値を表示するための記述
  $image = get_post_meta($post->ID, 'coupon_image', true);
  echo 'クーポン画像　<form method="post" action="admin.php?page=site_settings">
      <div id="media" style="max-width:500px;">
          <img src="' . $image . '" alt="">
      </div>
      <input style="width:0;height:0;padding:0;margin:0;visibility:hidden" name="coupon_image" type="text" value="'. $image .'"/>
      <input style="width:80px" type="button" name="media" value="画像選択" />
      <input style="width:80px" type="button" name="media-clear" value="削除" />
    </form><br>
    <script>
      (function ($) {
        var custom_uploader;
        //メディアアップローダーボタン
        $("input:button[name=media]").click(function(e) {
          e.preventDefault();
          if (custom_uploader) {
            custom_uploader.open();
            return;
          }
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
          custom_uploader.on("select", function() {
            var images = custom_uploader.state().get("selection");
            /* file の中に選択された画像の各種情報が入っている */
            images.each(function(file){
              $("input:text[name=coupon_image]").val(""); //テキストフォームをクリア
              $("#media").empty(); //id mediaタグの中身をクリア
              $("input:text[name=coupon_image]").val(file.attributes.url); //テキストフォームに選択したURLを追加
              $("#media").append(\'<img src="\'+file.attributes.url+\'" />\'); //プレビュー用にメディアアップローダーで選択した画像を表示させる
            });
          });
          custom_uploader.open();
        });
        //クリアボタンを押した時の処理
        $("input:button[name=media-clear]").click(function() {
          $("input:text[name=coupon_image]").val(""); //テキストフォームをクリア
          $("#media").empty(); //id mediaタグの中身をクリア
        });
      })(jQuery);
    </script>';
  echo 'クーポン有効期限（開始日）　 <input type="date" name="coupon_startdate" value="'.get_post_meta($post->ID, 'coupon_startdate', true).'" size="50" /><br>';
  echo 'クーポン有効期限（終了日）　 <input type="date" name="coupon_enddate" value="'.get_post_meta($post->ID, 'coupon_enddate', true).'" size="50" />　<br>';
}

// カスタムフィールドの保存
function save_custom_field_coupon($post_id) {
  $custom_fields = ['coupon_image', 'coupon_startdate', 'coupon_enddate'];
  if (isset($_POST['nonce-coupon_info'] ) && $_POST['nonce-coupon_info']) {
    if (check_admin_referer('action-coupon_info', 'nonce-coupon_info')) {
      foreach( $custom_fields as $d ) {
        if(!empty($_POST[$d])) {
          update_post_meta($post_id, $d, $_POST[$d]);
        }
        else {
          delete_post_meta($post_id, $d);
        }
      }
    }
  }
}
add_action( 'save_post', 'save_custom_field_coupon' );

?>
