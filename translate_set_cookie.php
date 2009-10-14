<?php

require('../../../wp-load.php');

if($_GET['admin_translate_cookie'] == 'yes') :
  setcookie(WP_TRANSLATE_THEME_COOKIE, 'yes', time()+3600, "/", ".284.com", 0);
  wp_redirect($_SERVER['HTTP_REFERER']);

elseif($_GET['admin_translate_cookie'] == 'no') :
setcookie(WP_TRANSLATE_THEME_COOKIE, 'no', time()+3600, "/", ".284.com", 0);

  wp_redirect($_SERVER['HTTP_REFERER']);

endif;

?>
