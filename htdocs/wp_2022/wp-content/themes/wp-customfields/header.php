<!DOCTYPE html>
<html dir="ltr" lang="ja">
<?php if ( is_home() && is_front_page() ) :?>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
<?php else: ?>
 <head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/article#">
<?php endif; ?>

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,shrink-to-fit=no">
  <meta name="format-detection" content="telephone=no">
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <!-- <link rel="stylesheet" href="<?php echo get_theme_file_uri(); ?>/_assets/css/style.css" type="text/css" /> -->
  <!-- <link rel="shortcut icon" href=""> -->
<?php wp_enqueue_script('jquery'); ?>
<?php wp_head(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lalezar&display=swap">
<?php if( is_user_logged_in() ) : ?>
  <!-- WPにログインしている場合はヘッダーをWP管理バー分（32px）下にずらす -->
  <style type="text/css">
    .header { margin-top: 32px; }
  </style>
<?php endif; ?>
</head>
<body <?php body_class(); ?>>
<?php if (check_sub_site('site2')): ?>
<!-- /site2/配下のサイト -->
<header class="header">
  <div class="titleLogo">
    <a href="/site2/"><img src="<?php echo home_url(); ?>/wp_2022/wp-content/uploads/2_logo.svg" alt="BrandLogo" width="102" height="62" /></a>
  </div>
  <nav class="gnav" id="gnav">
    <ul class="gnavList">
      <li><a href="<?php echo home_url(); ?>/site2/about/">About</a></li>
      <li><a href="<?php echo home_url(); ?>/site2/news/">News</a></li>
      <li><a href="<?php echo home_url(); ?>/site2/item/">Item</a></li>
      <li><a href="<?php echo home_url(); ?>/site2/store/">Store list</a></li>
      <li><a href="<?php echo home_url(); ?>/site2/feature_story/">Feature story</a></li>
    </ul>
  </nav>
</header>
<?php else: ?>
<!-- /配下のサイト -->
<header class="header">
  <div class="titleLogo">
    <a href="/"><img src="<?php echo home_url(); ?>/wp_2022/wp-content/uploads/logo.svg" alt="WP CustomFields" width="102" height="62" /></a>
  </div>
  <nav class="gnav" id="gnav">
    <ul class="gnavList">
      <li><a href="<?php echo home_url(); ?>/about/">パステルアートについて</a></li>
      <li><a href="<?php echo home_url(); ?>/course/">講座</a></li>
      <li><a href="<?php echo home_url(); ?>/faq/">よくある質問</a></li>
      <li><a href="<?php echo home_url(); ?>/blog/">BLOG</a></li>
      <li class="btn"><a href="<?php echo home_url(); ?>/apply/">講座申込</a></li>
    </ul>
  </nav>
</header>
<?php endif; ?>
