<?php
/**
 * This function used to display Wp Slide Categorywise on frontend.
 * @author Neelam Samariya code <nitsy85@gmail.com>
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
function wpsc_show_categryws_slider($atts, $content=null){	

 	
	 wp_enqueue_style( 'tab-main',  plugin_dir_url( __FILE__ ). 'css/main.css');
	 wp_enqueue_style('tab-module', plugin_dir_url( __FILE__ ). 'css/tabModule.css');	
	 wp_enqueue_style('bxslide', plugin_dir_url( __FILE__ ). 'css/jquery.bxslider.css');
	 wp_enqueue_script('jquery');
	 wp_enqueue_script( 'tab-module-js', plugin_dir_url( __FILE__ ) . 'js/tabs/tabModule.js', array('jquery'), '', true ); 	
     wp_enqueue_script('bxslider', plugin_dir_url( __FILE__ ) . 'js/responsiveslider/jquery.bxslider.min.js', array('jquery'), '', true );	
	
 
 global $wpdb;

 $path_array  = wp_upload_dir(); 
 $path = $path_array['baseurl'].'/slider_images';

 $loading_img = WPSC_URL.'images/bx_loader.gif';
 //echo '<pre>';
// print_r($blocks_data);
 /******************************* Retrieving Settings For The Slider ************************************************/
 $table_setting = $wpdb->prefix . 'cs_settings';
 $data = $wpdb->get_row("SELECT * FROM ".$table_setting." WHERE id=1"); 

if($data->hidecontrol == 'yes'){ $slide_hidecontrol = 'true';} else { $slide_hidecontrol = 'false'; }	
if($data->adaptiveheight == 'yes'){ $slide_adaptiveheight = 'true';} else { $slide_adaptiveheight = 'false'; }	  
if($data->responsive == 'yes'){ $slide_responsive = 'true';} else { $slide_responsive = 'false'; }
if($data->auto == 'yes'){ $slide_auto = 'true';} else { $slide_auto = 'false'; } 
if(empty($data->mode)){ $slide_mode = 'horizontal';} else { $slide_mode = $data->mode; }	
if(empty($data->slideshowspeed)){ $slide_speed = 500;} else { $slide_speed = $data->slideshowspeed; }
if($data->autocontrols == 'yes'){ $slide_autocontrols = 'true';} else { $slide_autocontrols = 'false'; } 
if($data->controls == 'yes'){ $slide_controls = 'true';} else { $slide_controls = 'false'; } 
if($data->pager == 'yes'){ $slide_pager = 'true';} else { $slide_pager = 'false'; } 
if(empty($data->pagerType)){ $slide_pagerType = 'full';} else { $slide_pagerType = $data->pagerType; }
if($data->autoHover == 'yes'){ $slide_autoHover = 'true';} else { $slide_autoHover = 'false'; } 
if($data->captions == 'yes'){ $slide_captions = 'true';} else { $slide_captions = 'false'; } 								

/******************************** Main Display for Slider Starts ******************************/
 $table_name = $wpdb->prefix . "category_slider";
 
 $blocks_data = $wpdb->get_results("SELECT * FROM ".$table_name." GROUP BY category_id", OBJECT);	
 //print_r($blocks_data);
 $display_block = "";	
 if( !empty($blocks_data) )
 {	 	
	 ?> 
    
     <script type="text/javascript">
	 	var r = jQuery.noConflict();
		r(document).ready(function(){		
			
			 	tabModule.init();		
				var slidehidecontrol = <?php echo $slide_hidecontrol;?>;
				var slideadaptiveheight = <?php echo $slide_adaptiveheight;?>;
				var slideresponsive = <?php echo $slide_responsive;?>;
				var slideauto = <?php echo $slide_auto;?>;		 		
		 		var slidemode = "<?php echo $slide_mode; ?>";
				var slidespeed = <?php echo $slide_speed; ?>;
				var slideautocontrols = <?php echo $slide_autocontrols; ?>;
				var slidecontrols = <?php echo $slide_controls;?>;
				var slidepager = <?php echo $slide_pager;?>;
				var slidepagerType = "<?php echo $slide_pagerType;?>";
				var slideautoHover = <?php echo $slide_autoHover;?>;
				var slidecaptions = <?php echo $slide_captions;?>;
						
				r('.tab-legend').on( 'click', 'li', function (e) {   
				e.stopPropagation(); 
				var id = r(this).attr('id');
				
				var cat_id = r(this).attr('data-catid');
				r('.tab-content').find('li').remove();
				
			r('.tab-content').append('<li><div id="bx_load"><img src="<?php echo $loading_img; ?>" width="32" height="32"><div></li>');				
				
				r.ajax({
				type: "POST",
				url: myAjax.ajaxurl,		
				data : {action: "wpsc_load_tabcontent", id : id, category : cat_id},		
				success: function(response){					
					r('.tab-content').find('li').remove();
					
					r('.tab-content').append(response).fadeIn(5000);	
					var tab_num = r('#banner-slide'+id).find(".bxslider");		
					
				  	tab_num.bxSlider({
					hideControlOnEnd: slidehidecontrol,
					adaptiveHeight: slideadaptiveheight,
					responsive: slideresponsive,
					auto: slideauto,
					mode: slidemode,
					speed: slidespeed,
					autoControls: slideautocontrols,
					controls: slidecontrols,
					pager: slidepager,
					pagerType: slidepagerType,
					autoHover: slideautoHover,
					captions: slidecaptions					
					});
				}
				});
				
				 
				 });
				 
				  r('.bxslider').bxSlider({
				  hideControlOnEnd: slidehidecontrol,
					adaptiveHeight: slideadaptiveheight,
					responsive: slideresponsive,
					auto: slideauto,
					mode: slidemode,
					speed: slidespeed,
					autoControls: slideautocontrols,
					controls: slidecontrols,
					pager: slidepager,
					pagerType: slidepagerType,
					autoHover: slideautoHover,
					captions: slidecaptions,
					
				});
				 
			
				 
			
		});
	</script>
    <?php
	/*******************
	Image title will be the caption for image	
	****************/
	$display_block .= '<div class="tab tab-horiz">
			<ul class="tab-legend">';
			$m=1;
			foreach ($blocks_data as $blocks_val)
			{
				$category = get_cat_name( $blocks_val->category_id );	
				$display_block .= '<li id="'.$m.'" data-catid="'.$blocks_val->category_id.'">'.$category.'</li>';
				$m++;
			}			
			$first_slide = $blocks_data[0]->category_id;	
			$display_block .= '</ul>			
			<ul class="tab-content">';			
			$tabs_data = $wpdb->get_results("SELECT * FROM ".$table_name." where category_id=".$first_slide, OBJECT);
			$display_block .= '<li>
				
				<div id="banner-slide">				
				<ul class="bxslider">';
				foreach($tabs_data as $tabs_val){
					$display_block .= '<li><img src="'.$path.'/'.$tabs_val->slide_image.'" title="'.$tabs_val->title.'" />								
					</li>';
				}        			
				$display_block .= '</ul>
				</div>
				</li>';			
			$display_block .= '</ul>
		</div>
		';          
		        
		
	
 }
 else{
	 $display_block .= 'Please Add Images to display the slider';
 }
 return $display_block;
}
?>
