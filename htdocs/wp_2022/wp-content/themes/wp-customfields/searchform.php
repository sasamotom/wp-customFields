<form method="get" class="searchForm -footer" role="search" action="<?php echo esc_url(home_url('/')); ?>">
  <input class="searchTextBox" type="search" name="s" placeholder="検索" value="<?php if (is_search()) { echo get_search_query(); } ?>">
  <button class="searchBtn" type="submit"><img src="/wp_2022/wp-content/uploads/icon_search.svg" alt=""></button>
</form>
