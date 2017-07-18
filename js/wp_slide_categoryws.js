 jQuery(document).ready(function($){
	 
		
	 
 	/*************************************** New Add Slider starts here ***********************************************/
 	var add_swrapper  = $(".add_additional_sections"); //Fields wrapper
    var new_button_section      = $(".new_field_section"); //Add button ID
	
	 $(new_button_section).click(function(){ //on add input button click
        //e.preventDefault();
		var vcounter = parseInt(($(".new_block_section").length)) + 1; 
		
		 $(add_swrapper).append('<div class="row new_block_section" style="border:1px solid #CCC; width:70%; float:left;" id="new_blocksection_'+vcounter+'"><div style="margin-top:2%; width:100%;"><div class="col-md-3"><label for="title">Image '+vcounter+'</label></div><div class="col-md-8"><input id="image_'+vcounter+'" type="file" name="image_'+vcounter+'" class="img" counter="'+vcounter+'"/><p class="description">Select Image</p></div><div class="col-md-3"></div><div class="col-md-8" style="text-align:center;"><div id="imagePreview'+vcounter+'" class="imagePreviews" style="display:none;"></div></div></div><div class="col-md-3"><label for="title">Title</label></div><div class="col-md-8"><input type="text"  name="title_'+vcounter+'" id="title_'+vcounter+'" class="form-control" placeholder="Title"  value="" /><p class="description">Enter the title</p></div></div>');            
            
       
    });
	
	<!---------------- Remove sections remove button ----------------------->
	 $('.new_section_fields_wrap').on("click",".new_remove_field_section", function(e){ //user click on remove text
        e.preventDefault(); 		
		var counter = parseInt(($(".new_block_section").length));  
		//alert(counter); 
		if(counter > 1)
        {
			$('#new_blocksection_' + counter).hide('slow', function(){ $('#new_blocksection_' + counter).remove(); });	         
        }
        else{
            alert('Not Allowed');
        }
		
    });
	

	 $("#section_image_1").on("change", function()
    {
       
		var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
 
        if (/^image/.test( files[0].type)){ // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file
 
            reader.onloadend = function(){ // set image data as background of div
				$("#imagePreview1").css("display", "block");
				$("#imagePreview1").css("height", "130");
				$("#imagePreview1").css("width", "150");
                $("#imagePreview1").css("background-image", "url("+this.result+")");
            }
        }
    });
	
	
	$('.add_additional_sections').on('change', 'input:file', function (){
		
		var id = $(this).attr("counter");		
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
 
        if (/^image/.test( files[0].type)){ // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file
 
            reader.onloadend = function(){ // set image data as background of div
				$("#imagePreview"+id).css("display", "block");
				$("#imagePreview"+id).css("height", "130");
				$("#imagePreview"+id).css("width", "150");
                $("#imagePreview"+id).css("background-image", "url("+this.result+")");
            }
        }
    });
 	
 	/*************************************** Edit time blocks starts here ***********************************************/
	<!---------------- Add Sections add section button ----------------------->
	var swrapper  = $(".additional_sections"); //Fields wrapper
    var add_button_section      = $(".add_field_section"); //Add button ID
	
	 $(add_button_section).click(function(){ //on add input button click
        //e.preventDefault();
		var scounter = parseInt(($(".block_section").length)) + 1; 
		      	
	 $(swrapper).append('<div class="row block_section" style="border:1px solid #CCC; width:70%; float:left;" id="blocksection_'+scounter+'"><div style="margin-top:2%; width:100%;"><div class="col-md-3"><label for="title">Image '+scounter+'</label></div><div class="col-md-8"><input id="image_'+scounter+'" type="file" name="image_'+scounter+'" class="img" counter="'+scounter+'"/><p class="description">Select Image</p></div><div class="col-md-3"></div><div class="col-md-8"><div id="imagePreview'+scounter+'" class="imagePreviews" style="display:none;"></div></div></div><div class="col-md-3"><label for="title">Title</label></div><div class="col-md-8"><input type="text"  name="title_'+scounter+'" id="title_'+scounter+'" class="form-control" placeholder="Title"  value="" /><p class="description">Enter the title</p></div></div>');       
            
       
    });
	
	<!---------------- Remove sections remove button ----------------------->
	 $('.section_fields_wrap').on("click",".remove_field_section", function(e){ //user click on remove button
        e.preventDefault(); 		
		var counter = parseInt(($(".block_section").length)); 
		nonce = $('#data-nonce').val();	
		if(counter > 1)
        {		
		//var del_slide = $('#slide_img_id'+counter).val();
		var del_slide = "";
		var del_slide = $('#slide_img_id'+counter).val();
		
		var cat_id = $('#editedrecord').val();
		/*var info = 'id=' + del_slide + '& category=' + <?php //echo $_GET['category']; ?>;*/
		if(confirm("Sure you want to delete this slide? There is NO undo!"))
		{
			
		$.ajax({
		type: "POST",
		url: myAjax.ajaxurl,		
		data : {action: "wpsc_slide_catgryws_remove_slide", id : del_slide, category : cat_id,nonce: nonce},		
		success: function(response){
			
			var res = response.trim();
			
			if((res == "Removed") || (res == "empty-slide")) {
              				
				$('#blocksection_' + counter).hide('slow', function(){ $('#blocksection_' + counter).remove(); });				
            }			
            else {
               alert("Unable to remove this slide")
            }
			
		}
		});
		
		}
		return false;     
		 
        }
        else{
            alert('Not Allowed');
        }
		
    });
	
	$('#section_div').on('change', 'input:file', function (){
		
		var id = $(this).attr("counter");		
		$("#imagePreview"+id).html('');
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
 
        if (/^image/.test( files[0].type)){ // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file
 
            reader.onloadend = function(){ // set image data as background of div
				$("#imagePreview"+id).css("display", "block");
				$("#imagePreview"+id).css("height", "130");
				$("#imagePreview"+id).css("width", "150");
                $("#imagePreview"+id).css("background-image", "url("+this.result+")");
            }
        }
});
	
	$('.additional_sections').on('change', 'input:file', function (){	
		var id = $(this).attr("counter");
		$("#imagePreview"+id).html('');		
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
 
        if (/^image/.test( files[0].type)){ // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file
 
            reader.onloadend = function(){ // set image data as background of div
				$("#imagePreview"+id).css("display", "block");
				$("#imagePreview"+id).css("height", "130");
				$("#imagePreview"+id).css("width", "150");
                $("#imagePreview"+id).css("background-image", "url("+this.result+")");
            }
        }
});
	
});
 

function clicked(id) {
      
	   if (confirm('Are you sure you want to delete?')) {
	 
           window.location="?page=wpcs_manage_slider&action=delete&category="+id;
       } else {
           return false;
       }
    }
