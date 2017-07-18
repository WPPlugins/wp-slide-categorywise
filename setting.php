<?php
/**
 * This function is used to display category slider settings in backend.
 * @package    Wp Slide Categorywise
 * @author     Neelam Samariya <nitsy85@gmail.com>
 * Websites: https://neelamsamariya.wordpress.com/
 * Technical Support:  Free Forum Support - nitsy85@gmail.com
 */

	
	if(!function_exists('wpsc_slide_catgryws_slider')){
		function wpsc_slide_catgryws_slider(){
			global $wpdb;
			$table_name = $wpdb->prefix . 'cs_settings';
			//Get data from database
			$data = $wpdb->get_row("SELECT * FROM ".$table_name." WHERE id=1");			
			$data_hidecontrol = $data->hidecontrol;
			$data_adaptiveheight = $data->adaptiveheight;
			$data_responsive = $data->responsive;
			$data_auto = $data->auto;
			$data_mode = $data->mode;
			$data_slideshowspeed = $data->slideshowspeed;					
			$data_autocontrols = $data->autocontrols;
			$data_controls = $data->controls;
			$data_pager = $data->pager;
			$data_pagerType = $data->pagerType;
			$data_autoHover = $data->autoHover;
			$data_captions = $data->captions;
			
			if ( isset( $_POST['wpsc-setting-nonce'] ) ) {
				if ( ! wp_verify_nonce( $_POST['wpsc-setting-nonce'], 'wpsc-setting-save-nonce' ) ) {
				return;
				}
			}
			
			if ( ! current_user_can( 'manage_options') ) {
			return;
			}
			if(isset($_REQUEST['submit'])):
			
			$data_hidecontrol = sanitize_text_field($_POST['hidecontrol']);
			$data_adaptiveheight = sanitize_text_field($_POST['adaptiveheight']);
			$data_responsive = sanitize_text_field($_POST['responsive']);
			$data_auto = sanitize_text_field($_POST['auto']);
			$data_mode = sanitize_text_field($_POST['mode']);
			$data_slideshowspeed = sanitize_text_field($_POST['slideshowspeed']);
			$data_autocontrols = sanitize_text_field($_POST['autocontrols']);			
			$data_controls = sanitize_text_field($_POST['controls']);
			$data_pager = sanitize_text_field($_POST['pager']);
			$data_pagerType = sanitize_text_field($_POST['pagerType']);
			$data_autoHover = sanitize_text_field($_POST['autoHover']);
			$data_captions = sanitize_text_field($_POST['captions']);
			
			//update to database
			$wpdb->update( 
				$table_name, 
				array( 
					
					'hidecontrol' => $data_hidecontrol,
					'adaptiveheight' => $data_adaptiveheight,
					'responsive' => $data_responsive,
					'auto' => $data_auto,
					'mode' => $data_mode,
					'slideshowspeed' => $data_slideshowspeed,
					'autocontrols'	=> $data_autocontrols,					
					'controls'	=> $data_controls,	
					'pager'	=> $data_pager,					
					'pagerType'	=> $data_pagerType,	
					'autoHover'	=> $data_autoHover,	
					'captions'	=> $data_captions,
									
				), 
				array( 'id' => 1 )
			);
			//alert message
			$message = 'Setting is updated';
			endif;
			?>
            <script type="text/javascript">
			var q = jQuery.noConflict();
			q(document).ready(function(){			
			q( "#settingsform" ).validate({
				rules: {				
				slideshowspeed: {				
				digits: true,
				maxlength: 3
				},				
				},
				 messages: {                                
				 slideshowspeed:"Maximum length is 4, only numbers allowed",
				 }
                                
				});			
			});
			</script>
			<?php /*?><div class="wrap">
				<h2><?php _e('Setting for Basic Category Slider', 'category_slider')?></h2><?php */?>
                <div class="wpcs-wrap"> 
                <div class="col-md-11">  
                
                <h3><span class="glyphicon glyphicon-asterisk"></span><?php _e('How to Use', 'wp_slide_categorywise') ?></h3>
		<div id="dashboard-widgets-container" class="wpcs-overview">
		    <div id="dashboard-widgets" class="metabox-holder">
				<div id="post-body">
					<div id="dashboard-widgets-main-content">
						<div class="postbox-container" id="main-container" style="width:75%;">
							<?php _e('Go through the steps below to create your categorywise slider:', 'wp_slide_categorywise') ?>
							<p>							
							<b><?php _e('Step 1', 'category_slider') ?></b> - <?php _e('Use our Add Slider tab to add slides for particular category', 'wp_slide_categorywise') ?> <a href="<?php echo admin_url('admin.php?page=add_slider') ?>"><?php _e('Here', 'wp_slide_categorywise') ?></a>. <?php _e('You can add as many slides for particular category as you want', 'wp_slide_categorywise') ?> .</li>							
							</p>
							<p>
							<b><?php _e('Step 2', 'wp_slide_categorywise') ?></b> - <?php _e(' To add slider to your post/page you can use the shortcode [wpsc_categorywise_slides]', 'wp_slide_categorywise') ?> .</li>
							</p>
							<p>
							<b><?php _e('Step 3', 'wp_slide_categorywise') ?></b> - <?php _e('If you want to do slider settings you can easily customize it from the below settings section', 'wp_slide_categorywise') ?> .</li>
							</p>
						</div>
			    		<div class="postbox-container" id="side-container" style="width:24%;">
						</div>						
					</div>
				</div>
		    </div>
		</div>
                
                
                <div id="icon-options-general" class="icon32"><br/></div>
                <h3><span class="glyphicon glyphicon-asterisk"></span><?php _e('Setting for Basic Category Slider', 'wpsc_slide_categorywise')?></h3> 
                <div class="wpcs-overview">

				<?php if (!empty($message)): ?>
				<div id="message" class="updated"><p><?php echo $message ?></p></div>
				<?php endif;?>

				<form id="settingsform" method="POST">
					<table class="form-table" style="background-color:inherit;">
						<tbody>	
                        	
                             <tr>
								<th scope="row"><label><?php _e('Hide control on end:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="hidecontrol">
										<option value="yes" <?php echo (stripslashes($data_hidecontrol) == 'yes') ? 'selected' : '';?>><?php _e('Yes', 'wpsc_slide_categorywise')?></option>
										<option value="no" <?php echo (stripslashes($data_hidecontrol) == 'no') ? 'selected' : '';?>><?php _e('No', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('If yes, "Next" control will be hidden on last slide and vice-versa? (yes/no)', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
							 <tr>
								<th scope="row"><label><?php _e('Adaptive Height:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="adaptiveheight">
										<option value="yes" <?php echo (stripslashes($data_adaptiveheight) == 'yes') ? 'selected' : '';?>><?php _e('Yes', 'wpsc_slide_categorywise')?></option>
										<option value="no" <?php echo (stripslashes($data_adaptiveheight) == 'no') ? 'selected' : '';?>><?php _e('No', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('Dynamically adjust slider height based on each slide\'s height', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
							
							 <tr>
								<th scope="row"><label><?php _e('Responsive:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="responsive">
										<option value="yes" <?php echo (stripslashes($data_responsive) == 'yes') ? 'selected' : '';?>><?php _e('Yes', 'wpsc_slide_categorywise')?></option>
										<option value="no" <?php echo (stripslashes($data_responsive) == 'no') ? 'selected' : '';?>><?php _e('No', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('Enable or disable auto resize of the slider. Useful if you need to use fixed width sliders.', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
                        						
							<tr>                            
								<th scope="row"><label><?php _e('Slideshow Auto:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="auto">
										<option value="yes" <?php echo (stripslashes($data_auto) == 'yes') ? 'selected' : '';?>><?php _e('Yes', 'wpsc_slide_categorywise')?></option>
										<option value="no" <?php echo (stripslashes($data_auto) == 'no') ? 'selected' : '';?>><?php _e('No', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('This is setting for Slideshow to be manual or autoslide', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
                            
                            <tr>                            
								<th scope="row"><label><?php _e('Slideshow Animation:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="mode">
										<option value="fade" <?php echo (stripslashes($data_mode) == 'fade') ? 'selected' : '';?>><?php _e('Fade', 'wpsc_slide_categorywise')?></option>
										<option value="vertical" <?php echo (stripslashes($data_mode) == 'vertical') ? 'selected' : '';?>><?php _e('Vertical', 'wpsc_slide_categorywise')?></option>
										<option value="horizontal" <?php echo (stripslashes($data_mode) == 'horizontal') ? 'selected' : '';?>><?php _e('Horizontal', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('This is setting for Slideshow animation type', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
                            
                            
                            <tr>
								<th scope="row"><label><?php _e('Slideshow Speed:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<input type="text" name="slideshowspeed" id="slideshowspeed" value="<?php echo stripslashes($data_slideshowspeed);?>" class="required b-width-vertical" />
									<p style="font-size: 12px;"><i><?php _e('Slide transition duration (in ms), Value is an integer number', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
                            
                             <tr>
								<th scope="row"><label><?php _e('Autocontrols:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="autocontrols">
										<option value="yes" <?php echo (stripslashes($data_autocontrols) == 'yes') ? 'selected' : '';?>><?php _e('Yes', 'wpsc_slide_categorywise')?></option>
										<option value="no" <?php echo (stripslashes($data_autocontrols) == 'no') ? 'selected' : '';?>><?php _e('No', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('Start/Stop controls will be added at the bottom of the slider', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
                           
                            
                             <tr>
								<th scope="row"><label><?php _e('Controls:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="controls">
										<option value="yes" <?php echo (stripslashes($data_controls) == 'yes') ? 'selected' : '';?>><?php _e('Yes', 'wpsc_slide_categorywise')?></option>
										<option value="no" <?php echo (stripslashes($data_controls) == 'no') ? 'selected' : '';?>><?php _e('No', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('"Next" / "Prev" controls will be added', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>                            
                            
                             <tr>
								<th scope="row"><label><?php _e('Pager:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="pager">
										<option value="no" <?php echo (stripslashes($data_pager) == 'no') ? 'selected' : '';?>><?php _e('No', 'wpsc_slide_categorywise')?></option>
										<option value="yes" <?php echo (stripslashes($data_pager) == 'yes') ? 'selected' : '';?>><?php _e('Yes', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('A pager will be added', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
                            
                             <tr>
								<th scope="row"><label><?php _e('Pager Type:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="pagerType">										
										<option value="full" <?php echo (stripslashes($data_pagerType) == 'full') ? 'selected' : '';?>><?php _e('Full', 'wpsc_slide_categorywise')?></option>
                                        <option value="short" <?php echo (stripslashes($data_pagerType) == 'short') ? 'selected' : '';?>><?php _e('Short', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('If \'full\', a pager link will be generated for each slide. If \'short\', a x / y pager will be used (ex. 1 / 5)', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label><?php _e('Pause on Hover:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="autoHover">
										<option value="no" <?php echo (stripslashes($data_autoHover) == 'no') ? 'selected' : '';?>><?php _e('No', 'wpsc_slide_categorywise')?></option>
										<option value="yes" <?php echo (stripslashes($data_autoHover) == 'yes') ? 'selected' : '';?>><?php _e('Yes', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('Pause the slideshow when hovering over slider', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
                            
                           
                            <tr>
								<th scope="row"><label><?php _e('Show Caption:', 'wpsc_slide_categorywise')?></label></th>
								<td>
									<select name="captions">										
										<option value="yes" <?php echo (stripslashes($data_captions) == 'yes') ? 'selected' : '';?>><?php _e('Yes', 'wpsc_slide_categorywise')?></option>
                                        <option value="no" <?php echo (stripslashes($data_captions) == 'no') ? 'selected' : '';?>><?php _e('No', 'wpsc_slide_categorywise')?></option>
									</select>
									<p style="font-size: 12px;"><i><?php _e('Show captions for images using the image title tag', 'wpsc_slide_categorywise')?></i></p>
								</td>
							</tr>
							
							
						</tbody>
					</table>
					<?php wp_nonce_field( 'wpsc-setting-save-nonce', 'wpsc-setting-nonce' );?>
					<p><input type="submit" value="<?php _e('Save', 'wpsc_slide_categorywise')?>" id="submit" class="button-primary" name="submit"></p>

				</form>
			</div>
		<?php
		}
	}