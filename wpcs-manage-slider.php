<?php
/**
 * This class used to manage category slides in backend.
 * @author Neelam Samariya code <nitsy85@gmail.com>
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */

if ( class_exists( 'WPSC_List_Table_Helper' ) and ! class_exists( 'Wpsc_slide_catgryws' ) ) {

	class Wpsc_slide_catgryws extends WP_List_Table {
	
	 var $slidecat_table_data;
	
	 
	 public function __construct() {
        parent::__construct( array(
            'singular' => 'categoryslider',
            'plural' => 'categorysliders',
            'ajax' => false
        ));
        //$this->prepare_items();
        //$this->display();
    }
    function get_columns() {
        $columns = array(     
		 	   
            'category_title'     => 'Category',
            'totalslides'   => 'Total Slides',
			
        );
        return $columns;
    }
   function column_default( $item, $column_name ) {
    switch( $column_name ) {  
		
        case 'category_title':
		case 'totalslides':
            return $item[ $column_name ];
        default:
            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }
    function prepare_items() {
	 global $wpdb;
        $query = "SELECT c.category_id, count(*) AS totalslides FROM ".$wpdb->prefix ."category_slider c GROUP BY c.category_id";
		
		$slidecat_table_data = $wpdb->get_results($query,ARRAY_A );
		$slider_data = array();
		if(count($slidecat_table_data) > 0){
			$t=0;
			foreach($slidecat_table_data as $table_values){				
				$category_title = get_cat_name( $table_values['category_id'] );
				$slider_data[$t]['category_id'] = $table_values['category_id'];
				$slider_data[$t]['category_title'] = $category_title;
				$slider_data[$t]['totalslides'] = $table_values['totalslides'];
				$t++;
			}
		}

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $slider_data;
	
    }
	
function column_category_title($item){
 
  $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&category=%s">Edit</a>',sanitize_text_field($_REQUEST['page']),'edit',$item['category_id']),
            'delete'    => sprintf('<a href="#" onclick="javascript:clicked('.$item['category_id'].');">Delete</a>'),
        );

  return sprintf('%1$s %2$s', $item['category_title'], $this->row_actions($actions) );
}	
}
}

/**
 * This function used to edit category slides in backend.
 * @author Neelam Code
 * @version 1.0.0
 * @package Wp Slide Categorywise
 */
function wpcs_manage_slider()
{
global $wpdb; 


if ( ! current_user_can( 'manage_options') ) {
return;
}

if ( isset( $_POST['nonce'] ) ) {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_slide_categorywise_nonce' ) ) {
		return;
		}
	}

/*************** This is used to delete category row along with its slides *************/
if( isset($_GET['action']) && $_GET['action']=='delete' && $_GET['category']!='' )
{
	$cat_id = sanitize_text_field($_GET['category']);
	$id = (int)$cat_id;
	/************************ Search for the number of slides in this category ***************************/
	$getslides = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."category_slider WHERE category_id=".$id);	
	if(!empty($getslides))
	{
		foreach ( $getslides as $slidedata ) 
		{
			$image_path = WPSC_IMAGE_URL. "/" .$slidedata->slide_image;
			$quer = $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."category_slider WHERE id=%d AND category_id=%s",$slidedata->id,$id));
			if($quer){
				 if (file_exists($image_path)) {
						unlink($image_path);							
				 }		
				
			} 
		
		}
	}

}


			
if( isset($_POST['update_slides']) && $_POST['update_slides']=='Update Slides' )
{	
	$path_array  = wp_upload_dir();
        
        ini_set("post_max_size", "60M");
        ini_set("upload_max_filesize", "60M");
        ini_set("memory_limit", "20000M"); 
	$maxsize    = 2097152; //2 MB
	if(isset($_FILES))
		{
			$categoryid = sanitize_text_field($_POST['editedrecord']);
			if(count($_FILES) > 0){
				
				/*********************** File Upload ****************************/
					$path = str_replace('\\', '/', $path_array['basedir']).'/slider_images';
					$i=1;					
					$slide_update_table=$wpdb->prefix."category_slider";
					for($k=0;$k<count($_FILES);$k++)
					{						
						if(isset($_POST['slide_img'.$i]))
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
									//Remove old image from folder
									$old_image_name = $path. "/" .sanitize_text_field($_POST['slide_img'.$i]);
									 if (file_exists($old_image_name)) {
										unlink($old_image_name);
										//echo 'File '.$old_image_name.' has been deleted';
									  } else {
										echo 'Could not delete '.$old_image_name.', file does not exist';
									  }
									//upload new image to folder                 
									move_uploaded_file($_FILES["image_".$i]["tmp_name"],$path. "/" . $newname);		
									/*********************** File Upload Ends ****************************/	
									//update
									$slideid = sanitize_text_field($_POST['slide_img_id'.$i]);							
									$wpdb->update( 
									$slide_update_table, 
									array( 
										'slide_image' => $newname,
										'title' => sanitize_text_field($_POST['title_'.$i]),																
									), 
									array( 'id' => $slideid,'category_id' => sanitize_text_field($_GET['category']) ) 
									);					
							/*$wpdb->query( $wpdb->prepare( "UPDATE $main_table_name SET slide_image = %s WHERE ID = $slideid", $newname ) );*/
								}
								else
								{
									$error[] = __( 'Invalid image '.($k+1).' couldnot be saved.Only jpg,gif,png file types allowed', 'wpsc_slide_categorywise' );
								}
							}//this image is edited
							else{
								$slideid = sanitize_text_field($_POST['slide_img_id'.$i]);
								$wpdb->update( 
								$slide_update_table, 
								array( 
										'slide_image' => sanitize_text_field($_POST['slide_img'.$i]),
										'title' => sanitize_text_field($_POST['title_'.$i]),																
								), 
								array( 'id' => $slideid,'category_id' => sanitize_text_field($_GET['category']) ) 
								);	
							}//image not edited
							
						}//this images already exists	
						else{
												
								if(($_FILES["image_".$i]["size"] > 0) && ($_FILES["image_".$i]["size"] < $maxsize)){
								$old_name = sanitize_file_name( wp_unslash($_FILES["image_".$i]["name"]));
								if(preg_match("/\.(gif|png|jpg|jpeg|JPEG|PNG|GIF|JPG)$/", $old_name)){
								$split_name = explode('.',$old_name);					
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
						
								$create_slide_data = array(
								'category_id' => sanitize_text_field($_GET['category']),
								'slide_image' => $newname,
								'title' => sanitize_text_field($_POST['title_'.$i]),
								/*'content' => CleanStringtext($_POST['content_'.$i])		*/		
								);
								$wpdb->insert($slide_update_table,$create_slide_data);	
								}
								else{							
									$error[] = __( 'invalid image '.($k+1).'. Only jpg,png,gif image types allowed', 'wpsc_slide_categorywise' );
								}
									
								}
								else{							
									$error[] = __( 'Image '.($k+1).' couldnot be saved or too large size', 'wpsc_slide_categorywise' );
								}
							
						}//insert new image as a row else end						
						$i++;
					}//for loop ends
					if(empty($error))
					{						
						$success= __( 'Slider edited successfully.', 'wpsc_slide_categorywise' );
					}	
				//exit;			
					
			}//if count files > 0		
	}		
}
if( isset($_GET['action']) && $_GET['action']=='edit' && $_GET['category']!='' )
{	
$category_record = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."category_slider WHERE category_id=%d",sanitize_text_field($_GET['category'])));
?>
<div class="wpcs-wrap"> <div class="col-md-11"> <div id="icon-options-general" class="icon32"><br></div>
<h3><span class="glyphicon glyphicon-asterisk"></span><?php _e('Edit Category Slides', 'wpsc_slide_categorywise')?></h3>
<div id="dashboard-widgets-container">
    <div class="postbox-container" id="main-container" style="width:75%;margin-bottom: 2%;">
        <p>							
        <b><?php _e('Note', 'wpsc_slide_categorywise') ?></b> : <?php _e('Max file upload size is 2MB', 'wpsc_slide_categorywise') ?> 
        </p>
    </div>     
</div>
<div class="wpcs-overview">
<form method="post" enctype="multipart/form-data">
<?php
$nonce = wp_create_nonce("wp_slide_categorywise_nonce");
if( !empty($error) )
{
	
        $error_msg=implode('<br>',$error);
	
	wpsc_slider_catgryws_showMessage($error_msg,true);
}
if( !empty($success) )
{
    
    $success= __( 'Slider Updated Successfully.', 'wpsc_slide_categorywise' );
    wpsc_slider_catgryws_showMessage($success);
}
?>
<div>
<div class="form-horizontal">
          <div class="row">
          <div class="col-md-2">
          <input type="hidden" name="data-nonce" id="data-nonce" value="<?php echo $nonce; ?>" />
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

if ($_GET['category']!='') { echo '<input id="editedrecord" name="editedrecord" type="hidden" value="'.$_GET['category'].'" />'; } ?>
          <div class="col-md-3">
          
          <select name="category_id" class="form-control" disabled="disabled">
      		<option value="0">Select Category</option>
            <?php
             if(count($categories) > 0){
				foreach($categories as $categorydata)
				{
					if(sanitize_text_field($_GET['category']) != "")
					{
						if($_GET['category'] == $categorydata->cat_ID){
							echo '<option value="'.$categorydata->cat_ID.'" selected>'.$categorydata->cat_name.'</option>';
						}
						else
						{
							echo '<option value="'.$categorydata->cat_ID.'">'.$categorydata->cat_name.'</option>';
						}
					}
					else
					{
						echo '<option value="'.$categorydata->cat_ID.'">'.$categorydata->cat_name.'</option>';
					}
					
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
         <div style="width:100%;" id="section_div">
          <?php
		 /******************** Path for images *******************/
		  
		  if(count($category_record) > 0){
			  $k=1;
			  foreach($category_record as $slidedata){
		  ?>
          <!-------Sections Start ---------->
         
          <div class="row block_section" style="border:1px solid #CCC; width:70%; float:left;" id="blocksection_<?php echo $k; ?>">
          <div style="margin-top:2%; width:100%;">
           	<div class="col-md-3">
            <label for="title">Image <?php echo $k; ?></label>
          	</div>
            <div class="col-md-8">
            <input id="image_<?php echo $k; ?>" type="file" name="image_<?php echo $k; ?>" class="img" counter="<?php echo $k; ?>"/>
            <input type="hidden" name="slide_img<?php echo $k; ?>" id="slide_img<?php echo $k; ?>" value="<?php echo stripslashes($slidedata->slide_image); ?>" />
            <input type="hidden" name="slide_img_id<?php echo $k; ?>" id="slide_img_id<?php echo $k; ?>" value="<?php echo stripslashes($slidedata->id); ?>" />                
            <p class="description">Select Image</p>
            </div>
            <div class="col-md-3"></div>
            <div class="col-md-8">           
            <div id="imagePreview<?php echo $k; ?>" class="imagePreviews"><?php if(stripslashes($slidedata->slide_image) != ""){?><img src="<?php echo WPSC_IMAGE_URL.stripslashes($slidedata->slide_image); ?>"  height="130" width="150"/><?php } ?></div>
            </div>       
          </div> 
          
           <div class="col-md-3">
            <label for="title">Title</label>
           </div>
           <div class="col-md-8">
           <input type="text"  name="title_<?php echo $k; ?>" id="title_<?php echo $k; ?>" class="form-control" placeholder="<?php _e('Title', 'wpsc_slide_categorywise')?>"  value="<?php echo  stripslashes($slidedata->title) ;?>" />        
           <p class="description">Enter the title</p>
           </div>
        </div><!--End block 1-->
        <?php
			$k++;
			  }//foreach loop ends
		  }//if count ends
		  ?>
        
         <div class="section_fields_wrap" style="float:right;">
          	<input type='button' value='Add Section' id='addButtonsection' class="add_field_section btn btn-sm btn-danger">
			<input type='button' value='Remove Section' id='removeButtonsection' class="remove_field_section btn btn-sm btn-danger">
        </div> 
        
        <div class="additional_sections">
        </div>       
        </div>   
       
          <!--------- Section Ends----------->
 
		</fieldset>
        <div class="row">
        <div class="col-md-7 col-md-offset-2">
        <input type="submit" name="update_slides" id="submit" class="btn btn-primary" value="<?php _e('Update Slides', 'wpsc_slide_categorywise')?>">
        </div>
      </div> 
        </div>
 
</form>
</div>
</div></div></div>
<?php
}
else
{
?>
<div class="wpcs-wrap">
<div class="col-md-12">   
<div id="icon-options-general" class="icon32"><br></div>
<h3><span class="glyphicon glyphicon-asterisk"></span><?php _e('Manage Category Slides', 'wpsc_slide_categorywise')?></h3>
<?php
$wpsc_category_list_table = new Wpsc_slide_catgryws();
$wpsc_category_list_table->prepare_items();
?>
<form method="post">
<?php
$wpsc_category_list_table->display();
?>

</form> 
</div></div> 
<?php } } ?>