<?php
/**
 * This code is used to add category slides on backend.
 * @author Neelam Samariya code <nitsy85@gmail.com>
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
global $wpdb;
$main_table = $wpdb->prefix ."category_slider";

if ( isset( $_POST['wpsc-slides-nonce'] ) ) {


	if ( ! wp_verify_nonce( $_POST['wpsc-slides-nonce'], 'wpsc-slides-save-nonce' ) ) {
	return;
	}
}

if ( ! current_user_can( 'manage_options') ) {
	return;
}



if(isset($_POST['slidesubmit']))
{	
	
	$path_array  = wp_upload_dir();
	$insert = array();
        
        ini_set("post_max_size", "60M");
        ini_set("upload_max_filesize", "60M");
        ini_set("memory_limit", "20000M"); 
	$maxsize    = 2097152; //2 MB
	if( sanitize_text_field($_POST['category_id']) == 0 )
	{
	   $error[] = __( 'Please select category.', 'wpsc_slide_categorywise' );
	}
	if(empty($error))
	{
		
		if(isset($_FILES))
		{
			$categoryid = sanitize_text_field($_POST['category_id']);	
			
			/*echo '<pre>';
			print_r($_FILES);
			echo '</pre>';
							exit;*/
			
			if(count($_FILES) > 0){
				
					/*********************** File Upload ****************************/
					$path = str_replace('\\', '/', $path_array['basedir']).'/slider_images';
					$i=1;
					for($k=0;$k<count($_FILES);$k++)
					{
						if(($_FILES["image_".$i]["size"] > 0) && ($_FILES["image_".$i]["size"] < $maxsize)){
							$old_name = sanitize_file_name( wp_unslash($_FILES["image_".$i]["name"]));
							if(preg_match("/\.(gif|png|jpg|jpeg|JPEG|PNG|GIF|JPG)$/", $old_name)){
							$split_name = explode( '.', $old_name );					
							$newname = $old_name;
							 if ($pos = strrpos($old_name, '.')) {
								$name = substr($old_name, 0, $pos);
								$ext = strtolower(substr($old_name, $pos));
							 } else {
								$name = $old_name;
							 }		
							$newpath = $path. "/" . $old_name;
							$time = time();
							if(file_exists($newpath)) {
								$newname = $name .'_'.$time. $ext;                       
							}                   
							move_uploaded_file($_FILES["image_".$i]["tmp_name"],$path. "/" . $newname);		
							/*********************** File Upload Ends ****************************/						
					
							$create_block_data = array(
							'category_id' => $categoryid,
							'slide_image' => $newname,
							'title' => sanitize_text_field($_POST['title_'.$i]),
							/*'content' => sanitize_text_field($_POST['content_'.$i])			*/
							);
							
							/*print_r($create_block_data);
							exit;*/
							$wpdb->insert($main_table,$create_block_data);				
							$insert[] = 'image '.($i);
							}
							else{							
								$error[] = __( 'invalid image '.($i).' . Only jpg,png,gif image types allowed', 'wpsc_slide_categorywise' );							
							}
							
						}//if file size > 0 ends
						else{							
							$error[] = __( 'image '.($i).' not selected or too large size', 'wpsc_slide_categorywise' );							
						}
						$i++;
					}//for
					
					if(empty($error))
					{						
						$success= __( 'Slider created Successfully.', 'wpsc_slide_categorywise' );
					}					
					
				
				}//if count files > 0
				
		}	
	}
	
}
	

?>
 
<div class="wpcs-wrap"> 
<div class="col-md-11">  
 
<div id="icon-options-general" class="icon32"><br/></div>
<h3><span class="glyphicon glyphicon-asterisk"></span>Add New Slides</h3>
<div id="dashboard-widgets-container">
    <div class="postbox-container" id="main-container" style="width:75%;margin-bottom: 2%;">
        <p>							
        <b><?php _e('Note', 'wpsc_slide_categorywise') ?></b> : <?php _e('Max file upload size is 2MB', 'wpsc_slide_categorywise') ?> 
        </p>
    </div>     
</div>
<div class="wpcs-overview">
 <form method="post" enctype="multipart/form-data" onSubmit="return validate();">
 <?php
if( !empty($error) )
{
	$inserted = "";
	if(!empty($insert))
	{
		$inserted = implode(',',$insert).' slides saved';
		$inserted = "Slider created.".$inserted;
	}
	$error_msg=implode('<br>',$error);
	$error_message = $inserted.'<br/>'.$error_msg;
	wpsc_slider_catgryws_showMessage($error_message,true);
}
if( !empty($success) )
{
    
    wpsc_slider_catgryws_showMessage($success);
}
?>
        <div class="form-horizontal">
          <div class="row">
          <div class="col-md-2">
            <label for="title">
             Category</label>
          </div>
          <?php
           $args = array(
			'type'                     => 'post',
			'child_of'                 => 0,
			'parent'                   => '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 0,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'category',
			'pad_counts'               => false 

); 
$categories = get_categories($args);
/*echo '<pre>';
print_r($categories);*/

?>
          <div class="col-md-3">
          
          <select name="category_id" class="form-control">
      		<option value="0">Select Category</option>
            <?php
            if(count($categories) > 0){
				foreach($categories as $categorydata)
				{
					echo '<option value="'.$categorydata->cat_ID.'">'.$categorydata->cat_name.'</option>';
				}
            }
			?>
		  </select>
          <p class="description">
              Select the category for which slides will be added
            </p>
          </div>
          </div>     
        
       <fieldset>
    	<legend>Add Slides</legend>
          
          <!-------Sections Start ---------->
          <div style="width:100%;" id="section_div">
          <div class="row new_block_section" style="border:1px solid #CCC; width:70%; float:left;" id="new_blocksection_1">
          <div style="margin-top:2%; width:100%;">
           	<div class="col-md-3">
            <label for="title">Image 1</label>
          	</div>
            <div class="col-md-8">
            <input id="section_image_1" type="file" name="image_1" class="img" counter="1"/>                
            <p class="description">Select Image</p>
            </div>
            <div class="col-md-3"></div>
            <div class="col-md-8" style="text-align:center;">
            <div id="imagePreview1" class="imagePreviews" style="display:none;"></div>
            </div>       
          </div> 
          
           <div class="col-md-3">
            <label for="title">Title</label>
           </div>
           <div class="col-md-8">
           <input type="text"  name="title_1" id="title_1" class="form-control" placeholder="<?php _e('Title/Description', 'wpsc_slide_categorywise')?>"  value="" />        
           <p class="description">Enter the title/description</p>
           </div>              
           <!--<div class="col-md-3">
           <label for="content">Content</label>
           </div>
           <div class="col-md-8">
           <textarea name="content_1" id="content_1" rows="1" cols="20" class="form-control"></textarea>    
           <p class="description">Enter the Content</p>
           </div>            -->
        </div><!--End block 1-->
            
            
        <div class="new_section_fields_wrap" style="float:right;">
          	<input type='button' value='Add Section' id='addButtonsection' class="new_field_section btn btn-sm btn-danger">
			<input type='button' value='Remove Section' id='removeButtonsection' class="new_remove_field_section btn btn-sm btn-danger">
        </div> 
        
         <div class="add_additional_sections">
        </div>       
        </div>        
       
          <!--------- Section Ends----------->
 
		</fieldset> 
       
               
        </div>
          
          <div class="row">
          <div class="col-md-7 col-md-offset-2">
		  	<?php wp_nonce_field( 'wpsc-slides-save-nonce', 'wpsc-slides-nonce' );?>
            <input type="submit" name="slidesubmit" id="submit" class="btn btn-primary" value="Save Slides"/>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>