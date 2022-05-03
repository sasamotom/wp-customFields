<?php
// Instagramタイムライン表示
// 【参考URL】
// https://arts-factory.net/insta_api/
// https://www.webdesignleaves.com/pr/plugins/instagram_graph_api.html
$instagram = null;
$id = '17841440379516830';  // ビジネスアカウントID
$token = 'EAAHk5elvrzUBAOrd4fb6UA0SzHFaIbJrAzK2Qlh9w46LKuaWb1KWj3MurrhHkqIHVIIC9I5PxQ3VLw2qT15Drxt1TfHBOsDDdZB6L7gpMfcyKwKHnM8joEBgQgaU3IVdol4QlEQhVx0YjpBePHW8p2IJ1wMyMvCZBkw4Tktlv0uf4selbHWrRLGUX92UEZD'; // アクセストークン３
$count = '4';               // 表示する画像数
$url = 'https://graph.facebook.com/v13.0/' . $id . '?fields=name,media.limit(' . $count. '){caption,media_url,thumbnail_url,permalink,like_count,comments_count,media_type}&access_token=' . $token;
$curl = curl_init();  // セッションを初期化
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl); // 結果を変数に代入（失敗すれば false、成功すれば取得結果が格納される）
curl_close($curl);  //cURL セッションを終了
if ($response) {
  $instagram = json_decode($response);
  if(isset($instagram->error)){
      $instagram = null;
  }
}
?>
<div class="instagramContainer">
  <ul class="picList">
<?php
foreach ($instagram->media->data as $val) {
  // var_dump($val);
  if($val->media_type ==='VIDEO') {
    $src = $val->thumbnail_url;
    $video = '<span class="video_icon"></span>';
  }
  else{
    $src = $val->media_url;
    $video = '';
  }
?>
    <li>
      <a href="<?php echo $val->permalink; ?>" target="_blank">
        <img src="<?php echo $src; ?>" alt="<?php echo isset($val->caption) ? $val->caption: ''; ?>">
        <?php echo $video ? $video : ''; //ビデオの場合は追加の span 要素を出力?>
        <!-- <span class="like_count"><?php echo $val->like_count; ?></span> -->
        <!-- <span class="comments_count"><?php echo $val->comments_count; ?></span> -->
      </a>
    </li>
<?php
}
?>
  </ul>
</div>
