<?php 
/*
Plugin Name: Wp Slide Categorywise
Plugin URI: https://neelamsamariya.wordpress.com/
Description: Responsive slider for displaying slider images categorywise.This plugin allows you to add as many slides to a category and display it in a slider.
Author: Neelam Samariya
Version: 1.0
Author URI: https://neelamsamariya.wordpress.com/
*/
$siteurl = get_option('siteurl');
if ( ! defined( 'WPSC_FOLDER' ) ) {
				define( 'WPSC_FOLDER', basename( dirname( __FILE__ ) ) );
}
if ( ! defined( 'WPSC_DIR' ) ) {
	define( 'WPSC_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WPSC_URL' ) ) {
	define( 'WPSC_URL', plugin_dir_url( WPSC_FOLDER ).WPSC_FOLDER.'/' );
}

global $wpdb;

$upload_dir = wp_upload_dir();
if ( ! defined( 'WPSC_IMAGE_DIR' ) ) {

	if ( ! is_dir( $upload_dir['basedir'].'/slider_images' ) ) {
		/************************ Make directory to upload the slider images **********************************/
		mkdir( $upload_dir['basedir'].'/slider_images', 0700 );
	}
	define( 'WPSC_IMAGE_DIR',$upload_dir['basedir'].'/slider_images/' );
	define( 'WPSC_IMAGE_URL',$upload_dir['baseurl'].'/slider_images/' );

}
function wpsc_slide_catgryws_install() {
	global $wpdb;
	global $cs_db_version;
	$slides_table = $wpdb->prefix . 'category_slider';	
	$charset_collate = $wpdb->get_charset_collate();
	if($wpdb->get_var("SHOW TABLES LIKE '$slides_table'") != $slides_table){
	// sql to create slides table for plugin
	$sql = "CREATE TABLE $slides_table (
			id int(20) NOT NULL AUTO_INCREMENT,		
			category_id int(10) NOT NULL,
			slide_image varchar(250),
			title varchar(250),		
			PRIMARY KEY id (id)
			) $charset_collate;";
	}	
	$settings_table = $wpdb->prefix . 'cs_settings'; 
			//Check table is exists, if not install new table
			if($wpdb->get_var("SHOW TABLES LIKE '$settings_table'") != $settings_table){
				// sql to create settings table for plugin
				$cs_sql = "CREATE TABLE " . $settings_table . " (
				  id int(11) NOT NULL AUTO_INCREMENT,				 
				  hidecontrol VARCHAR(10),
				  adaptiveheight VARCHAR(10),
				  responsive VARCHAR(10),
				  auto VARCHAR(10),
				  mode VARCHAR(10),
				  slideshowspeed VARCHAR(10),
				  autocontrols VARCHAR(10),				  
				  controls VARCHAR(10),	
				  pager VARCHAR(10),
				  pagerType VARCHAR(10),	
				  autoHover VARCHAR(10),	
				  captions VARCHAR(10),				 
				  PRIMARY KEY  (id)
				);";
	}
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	dbDelta( $cs_sql );
	add_option( 'cs_db_version', $cs_db_version );	
	
}
//Insert id to table install - use to update database
	if(!function_exists('cs_add_id_to_table_category_slider')){
		function cs_add_id_to_table_category_slider(){
			global $wpdb;
			$table_name = $wpdb->prefix . 'cs_settings';
			$wpdb->insert($table_name, array(
				'id' => 1,				
				'hidecontrol' => 'no',
				'adaptiveheight' => 'yes',
				'responsive' => 'yes',
				'auto' => 'no',
				'mode' => 'horizontal',
				'slideshowspeed' => 500,
				'autocontrols' => 'no',				
				'controls' => 'yes',
				'pager' => 'yes',
				'pagerType' => 'full',
				'autoHover' => 'no',
				'captions' => 'no',
			));
		}
	}
function wpsc_slide_catgryws_uninstall()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "category_slider";
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
	
	$settings_table = $wpdb->prefix . 'cs_settings'; 
	$wpdb->query("DROP TABLE IF EXISTS $settings_table");
}
register_activation_hook(__FILE__,'wpsc_slide_catgryws_install');
register_activation_hook(__FILE__, 'cs_add_id_to_table_category_slider');
register_deactivation_hook(__FILE__ , 'wpsc_slide_catgryws_uninstall' );

/* Add category slider in Admin Menu Item*/
add_action('admin_menu','wpsc_slide_cat_admin_menu');
/**
 * This function used to setup admin menu.
 * @author Neelam Code
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
function wpsc_slide_cat_admin_menu(){
	$pagehook1 = add_menu_page(__('Wp Slide Categorywise','wpsc_slide_categorywise'),__('Wp Slide Categorywise','wpsc_slide_categorywise'),'manage_options','wp_slide_categorywise','wpsc_slider_catgryws_settings','');	
	 
	$pagehook2 = add_submenu_page('wp_slide_categorywise', __('Add Slider','wpsc_slide_categorywise'), __('Add Slider','wpsc_slide_categorywise'),'manage_options', 'add_slider', 'doAddWpscSlider');
	$pagehook4 = add_submenu_page('wp_slide_categorywise', __('Manage Slider','wpsc_slide_categorywise'), __('Manage Slider','wpsc_slide_categorywise'),'manage_options', 'wpcs_manage_slider', 'wpcs_manage_slider');

	add_action('load-'.$pagehook1, 'wpsc_load_slider_catgryws');
	add_action('load-'.$pagehook2, 'wpsc_load_slider_catgryws');	
	add_action('load-'.$pagehook4, 'wpsc_load_slider_catgryws');		
}
require_once(WPSC_DIR.'/class.tabular.php');
include_once(WPSC_DIR."/wpcs-manage-slider.php");
/**
 * This function used to load scripts and styles for admin.
 * @author Neelam Code
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
function wpsc_load_slider_catgryws()
{	
	
	wp_enqueue_style('google_bootstrap_css',plugins_url( 'css/bootstrap.css' , __FILE__ ));		
	wp_enqueue_script('jquery');	
	wp_enqueue_script('jquery-validation-plugin', plugins_url('js/jquery.validate.min.js', __FILE__ ), array('jquery'), '', true);
    
}

/**
 * This function used to call settings page.
 * @author Neelam Code
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
function wpsc_slider_catgryws_settings()
{
	 include WPSC_DIR.'/setting.php';
	 wpsc_slide_catgryws_slider();
}
/**
 * This function used to call add slides page.
 * @author Neelam Code
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
function doAddWpscSlider()
{
	 include WPSC_DIR.'/add_slider.php';
}
/*************************
Shortcode Function
***********************/
include_once("wpcs-show-slider.php");
function wpsc_slider_catgryws_actions()
{
add_shortcode('wpsc_categorywise_slides','wpsc_show_categryws_slider');
}
add_action('init', 'wpsc_slider_catgryws_actions');

/**
 * This function used to show success/failure message in backend.
 * @author Neelam Code
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
function wpsc_slider_catgryws_showMessage($message, $errormsg = false)
{
	if( empty($message) )
	return;
	
	if ( $errormsg ) {
		echo '<div id="message" class="error">';
	}
	else {
		echo '<div id="message" class="updated">';
	}
	echo "<p><strong>$message</strong></p></div>";
} 
/**
 * This function used to call admin ajax.
 * @author Neelam Code
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
add_action( 'init', 'wpsc_catgryslider_script_enqueuer' );
function wpsc_catgryslider_script_enqueuer() {
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script('slider_categoryws', WPSC_URL.'js/wp_slide_categoryws.js', array('jquery'), '', true);
   wp_localize_script( 'slider_categoryws', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  
 
}
add_action("wp_ajax_wpsc_slide_catgryws_remove_slide", "wpsc_slide_catgryws_remove_slide");

function wpsc_slide_catgryws_remove_slide() {
	global $wpdb;
   	if ( !wp_verify_nonce( $_REQUEST['nonce'], "wp_slide_categorywise_nonce")) {
      exit("No naughty business please");
   	}
	if ( ! current_user_can( 'manage_options') ) {
		return;
	}
	$getid = ""; 
	if(isset($_REQUEST['id']))
	{
		$getid = sanitize_text_field($_REQUEST['id']);
	}
	
	if($getid == "")
	{
		echo 'empty-slide';
	}
	else{
		$slidecatid = sanitize_text_field($_REQUEST['category']);
		$theImage = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."category_slider WHERE id = ".$getid,OBJECT );
		/********************** Unlink Image from folder ********************/
		$query = "DELETE FROM ".$wpdb->prefix."category_slider WHERE id = ".$getid." and category_id =".$slidecatid;
		if($wpdb->query($query)){		
			 $image_path = WPSC_IMAGE_DIR. "/" .$theImage->slide_image;
			 if (file_exists($image_path)) {
				unlink($image_path);
				echo 'Removed';			
				//echo 'File '.$old_image_name.' has been deleted';
			  } else {
				echo 'Could not delete '.$theImage->slide_image.', file does not exist';
			 }
		}
	}
   die();
}
/**
 * This function used to load tabs content via admin-ajax file.
 * @author Neelam Code
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
add_action("wp_ajax_wpsc_load_tabcontent", "wpsc_load_tabcontent");
add_action("wp_ajax_nopriv_wpsc_load_tabcontent", "wpsc_load_tabcontent"); 

function wpsc_load_tabcontent() {
	global $wpdb;
	$table_name = $wpdb->prefix . "category_slider";	
	$id = sanitize_text_field($_REQUEST['id']);
	$slidecatid = sanitize_text_field($_REQUEST['category']);
	$tabs_data = $wpdb->get_results("SELECT * FROM ".$table_name." where category_id=".$slidecatid, OBJECT);
	$display_block = "";
	$display_block .= '<li class="active_tab">
		<div id="banner-slide'.$id.'">				
		<ul class="bxslider">';
		foreach($tabs_data as $tabs_val){
			$display_block .= '<li><img src="'.WPSC_IMAGE_URL.$tabs_val->slide_image.'" title="'.stripslashes($tabs_val->title).'" />									
			</li>';
		}        			
		$display_block .= '</ul>
		</div>
		</li>';
echo $display_block;	
die();
}
function CleanStringCategorySliderForm($content)
{	
	//echo 'innnnn';
	//exit;
	return wp_kses($content,'');
}
function CleanStringtext($content){
	return sanitize_text_field($content);
	}
	
	
/*add_action( 'init', 'wpsc_deregister_heartbeat', 1 );
function wpsc_deregister_heartbeat() {
	global $pagenow;

	if ( 'post.php' != $pagenow && 'post-new.php' != $pagenow )
		wp_deregister_script('heartbeat');
}*/
?>