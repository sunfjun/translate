<?php
/**
 * @package Sunfjun Wp Translate 
 * @author FengJun Sun 
 * @version 100
 */
/*
Plugin Name: Wp Translate Theme [ jun ] 
Plugin URI: http://jobwp.cn/#
Description: Theme Translate
Author: FengJun Sun
Version: 100
Author URI: http://jobwp.cn/
*/ 


define('WP_TRANSLATE_THEME_COOKIE', 'wptranslatetheme_' . COOKIEHASH);
$theme_translate_db_version = "100";
$t_a = array();
$funs = array();
define( 'TRANSLATE_FOLDER_NAME', dirname( plugin_basename(__FILE__) ) );
define( 'TRANSLATE_URL_PATH', trailingslashit(get_option('siteurl').'/wp-content/plugins/' . TRANSLATE_FOLDER_NAME ));
register_activation_hook(__FILE__,'translate_install');
add_action('admin_menu', 'translate_m');
add_action('init','get_t_v');
add_action('wp_footer','in_t_s');
add_action('wp_head','in_t_s_css');


function translate_install () {
	global $wpdb;
	$table_name = $wpdb->prefix . "ajax_translate" ;
	if ($wpdb->get_var("SHOW TABLES LIEK '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  translatekey tinytext NOT NULL,
			  language tinytext NOT NULL,
			  themefilename tinytext NOT NULL,
			  translatevalue text NOT NULL,
			  UNIQUE KEY id (id)
			)DEFAULT CHARACTER SET utf8";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		add_option("theme_translate_db_version", $theme_translate_db_version);
		
	}
	
	
	 $installed_ver = get_option( "theme_translate_db_version" );

   if( $installed_ver != $theme_translate_db_version ) {

    $sql = "CREATE TABLE " . $table_name . " (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  translatekey tinytext NOT NULL,
		  language tinytext NOT NULL,
		  themefilename tinytext NOT NULL,
		  translatevalue text NOT NULL,
		  UNIQUE KEY id (id)
	)DEFAULT CHARACTER SET utf8;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      update_option( "theme_translate_db_version", $theme_translate_db_version );
  }
}

function translate_m () {
	add_options_page(__('Translate', 'translate'), __('Translate', 'translate'), 8, __FILE__, 'translate_options_page');
}
function translate_options_page ($true) {
	require(ABSPATH . '/wp-admin/includes/admin.php');
	global $funs;
		printf("
			<div id=\"wpbody-content\">
			<div id=\"icon-tools\" class=\"icon32\"><br/></div>
			<div class=\"wrap\" id=\"wpbody-content-wrap\">
			<h2>Translates</h2>");
	printf("<form action=\"%stranslate_post.php\" method=\"post\" accept-charset=\"utf-8\">",TRANSLATE_URL_PATH);
	settings_fields('translate_post');
	print("
				<table border=\"0\" cellspacing=\"5\" cellpadding=\"5\">");
			foreach($funs as $key => $value){
				print("<tr><td><h3>$key</h3></td></tr>");
				foreach ($value as $k){
					printf("
						<tr><td><label for=\"k\">%s</label></td><td><input type=\"text\" name=\"t_key[%s][%s]\" value=\"%s\" id=\"tkey\"></td></tr>
					",$k,$key,$k,get_v($k));
				}
			}
			print("
					</table>
					<p><input id=\"t_update\" type=\"submit\" value=\"Update\"></p>
					</form></div></div>
					");
}

function translat_readdir($dir) {
  $handle=opendir($dir);
  $i=0;
  while($file=readdir($handle)) {
    if (($file!=".")and($file!="..")and($file!=".git")and($file!=".svn")and(substr($file,-4,4) == ".php")) {
      $translat_readdir_list[$i]=$file;
      $i=$i+1;
     }
  }
  closedir($handle);
  return $translat_readdir_list;
}

function get_t_f_list ($files){
	global $funs;
  if (is_array($files)){
    foreach ($files as $file) {
     if(is_file(TEMPLATEPATH . '/' . $file))
        $content = file_get_contents(TEMPLATEPATH . '/' . $file);
        preg_match_all("/(\Wt\([\'\"])(.*)([\'\"]\))/x",$content,$funlist); 
        if(count($funlist[0]) == 0)continue;
        $fla = array();
        foreach($funlist[2] as $fl){
          array_push($fla,$fl);
        } 
        $funs["$file"] = $fla;
      } 
   }
  
  return $funs;
}

function wp_insert_translate ($t_key) {
	global $wpdb;
	$table_name = $wpdb->prefix . "ajax_translate" ;
	$language = WPLANG;
	       		if(is_array($t_key)){
		 foreach ( $t_key as $tk => $tv){
			$themefilename = $tk;
				foreach( $tv as $k => $v){
					$translatekey = $k;
					$translatevalue  = $v;
					$id = authentication_k($k);
					if($id){
					  $results = $wpdb->update( $table_name, array( 'themefilename' => $wpdb->escape($themefilename) ,
																													'language' => $wpdb->escape($language),
				 																									'translatevalue' => $wpdb->escape($translatevalue),
				 																									'translatekey' =>$wpdb->escape($translatekey)), array( 'id' => $id )) ;
					}else{
						$insert = "INSERT INTO " . $table_name ." (translatekey, translatevalue, language, themefilename) " . "VALUES ('" . $wpdb->escape($translatekey) . "','" . $wpdb->escape($translatevalue) . "','" . $wpdb->escape($language) . "','" . $wpdb->escape($themefilename) . "')"; 
					  $results = $wpdb->query( $insert );
						}
						
						
				}
		}
	}
	return true;
}
function authentication_k ($k) {
	global $wpdb;
	$table_name = $wpdb->prefix . "ajax_translate" ;
	$language = WPLANG;
	
	$r =  $wpdb->get_var("SELECT id FROM $table_name WHERE language = '$language' AND translatekey = '$k'");
  return $r;
}

function get_v ($k) {
	global $wpdb;
	$table_name = $wpdb->prefix . "ajax_translate" ;
	$language = WPLANG;
	
	$v =  $wpdb->get_var("SELECT translatevalue FROM $table_name WHERE language = '$language' AND translatekey = '$k'");
  return $v;
}
function get_t_v(){
	if (is_user_logged_in()){
		$dirlist = translat_readdir(TEMPLATEPATH);
		get_t_f_list($dirlist);
	}
}
function in_t_s_css(){
	ob_start();
	?>
	<script src="http://www.google.com/jsapi"></script>
	<script type='text/javascript'>
	  google.load("jquery", "1.3.2");
	  google.load("jqueryui", "1.7.1");
	</script>
	<link rel="stylesheet" href="<?php echo TRANSLATE_URL_PATH . 'translate.css' ?>" type="text/css" media="screen" />
  <script type="text/javascript" src="<?php echo TRANSLATE_URL_PATH . 'translate.js' ?>"></script>
	<?php 
	   $inplaceedit = ob_get_contents();
		 ob_end_clean();
		if(is_user_logged_in()){
		  echo $inplaceedit; 
		}else{
			return true;
		}

}
function in_t_s () {
	if (is_user_logged_in()){
		if($_COOKIE[WP_TRANSLATE_THEME_COOKIE] == 'yes'){
			require(ABSPATH . '/wp-admin/includes/admin.php');
			global $t_a,$funs;
			printf("
			<form action=\"%stranslate_set_cookie.php\" method=\"get\" accept-charset=\"utf-8\">
		    <input type=\"hidden\" name=\"admin_translate_cookie\" value=\"no\">
		    <p><input id=\"t_close\" type=\"button\" value=\"close\" onclick=\"c_t();return false;\"></p>
				<p><input type=\"submit\" value=\"Translate\" id=\"translate_cookie\"></p>
			</form>
			",TRANSLATE_URL_PATH);
			printf("
				<div id=\"wpbody-content\">
				<div id=\"icon-tools\" class=\"icon32\"><br/></div>
				<div class=\"wrap\" id=\"wpbody-content-wrap\">
				<h2>Translates</h2>");
			if (isset($_GET['updated'])):
			printf("<div id=\"message\" class=\"updated fade\"><p><strong>Settings saved.</strong></p></div>");
			endif;
			printf("<form action=\"%stranslate_post.php\" method=\"post\" accept-charset=\"utf-8\">",TRANSLATE_URL_PATH);
			settings_fields('translate_post');
			print("
						<table border=\"0\" cellspacing=\"5\" cellpadding=\"5\">");
					foreach($funs as $key => $value){
						foreach ($value as $k){
							if (!$t_a[$k])continue;
							printf("
								<tr><td><label for=\"k\">%s</label></td><td><input type=\"text\" name=\"t_key[%s][%s]\" value=\"%s\" id=\"tkey\"></td></tr>",$k,$key,$k,get_v($k));
						}
			}
			print("</table><p><input id=\"t_update\" type=\"submit\" value=\"Update\"></p></form></div></div>");
			}else{
			printf("
			<form action=\"%stranslate_set_cookie.php\" method=\"get\" accept-charset=\"utf-8\">
		    <input type=\"hidden\" name=\"admin_translate_cookie\" value=\"yes\">
				<p><input type=\"submit\" value=\"Translate\" id=\"translate_cookie\"></p>
			</form>
			",TRANSLATE_URL_PATH);
		}
	}else{
		return true;
	}
}
function t($tsk){
	global $wpdb,$t_a;
	$table_name = $wpdb->prefix . "ajax_translate" ;
	$language = WPLANG;
	if(!$tsk)return;
	$translatekey = $tsk;
	$translate  = $wpdb->get_results("SELECT * FROM $table_name WHERE language = '$language' AND translatekey = '$translatekey'");
	if ($translate[0]->translatevalue){
		$show_t =  $translate[0]->translatevalue;
	}else{
		$show_t = $translatekey;
	}
	echo $show_t;
	$t_a[$tsk] = $tsk;
  return $t_a;
}
