
			 /*
			* Purpose: Initialise Tinymce editor.
			* Date: 11 Oct 2014
			* Input Parameter:
			*            None
			*  Output Parameter:
			*            None
			*/
			 tinymce.init({
				selector: "textarea#en_news_content",
				theme: "modern",
				plugins: [
					 "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
					 "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
					 "save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"
				],
				directionality : 'ltr',
				browser_spellcheck : true,
				height : 400,
				external_filemanager_path:"<?php echo base_url();?>assets/lib/tinymce/filemanager/",
				filemanager_title:"Responsive Filemanager" ,
				external_plugins: { "filemanager" : "filemanager/plugin.js"}

			 });
			/*
			* Purpose: Initialise Tinymce editor.
			* Date: 11 Oct 2014
			* Input Parameter:
			*            None
			*  Output Parameter:
			*            None
			*/
			 tinymce.init({
				selector: "textarea#es_news_content",
				theme: "modern",
				plugins: [
					 "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
					 "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
					 "save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"
				],
				directionality : 'ltr',
				browser_spellcheck : true,
				height : 400,
				external_filemanager_path:"<?php echo base_url();?>assets/lib/tinymce/filemanager/",
				filemanager_title:"Responsive Filemanager" ,
				external_plugins: { "filemanager" : "filemanager/plugin.js"}

			 });
			
			/*
			* Purpose: Jquery initialization.
			* Date: 14 Oct 2014
			* Input Parameter:
			*            None
			*  Output Parameter:
			*            None
			*/
			
			$(document).ready(function(){
				
				$('#news_date').datepicker();
				$('#news_date').datepicker('setDate',new Date('<?php echo $record_en['new_date'];?>'));
				$('#news_date').datepicker("option", "dateFormat", "yy-mm-dd");
				$('#news_date').datepicker("option", "showAnim", "slide");
				$('#news_date').datepicker("option","changeYear",true);
				$('#news_date').datepicker("option","changeMonth",true);
				$('#news_date').datepicker("option","showMonthAfterYear",true);
				$('#news_date').datepicker("option","showButtonPanel",true);
				
				
				$('#news_status').val("<?php echo $record_en['new_status'];?>");
				
				//$('#news_image').val("<?php echo $record_en['new_image'];?>");
				$('#news_image').change(function(){
					var formData = new FormData($('form#tab1')[0]);
					$.ajax({
							type:'POST',
							url:'<?php echo base_url();?>admin/upload_image',
							xhr: function() {  // Custom XMLHttpRequest
									var myXhr = $.ajaxSettings.xhr();
									if(myXhr.upload){ // Check if upload property exists
										myXhr.upload.addEventListener('progress',progressHandlingFunction, false); // For handling the progress of the upload
									}
									return myXhr;
								},
							data: formData,
							success:function(){
								var str = "<?php echo base_url().'assets/images/upload/';?>";
								var img_str = $("#news_image").val();
								img_str = img_str.replace(/ {2,}/g," ");
								img_str = img_str.replace(/ /g,"_");
								str = str.concat(img_str);
								$('#preview').attr('src',str);
							},
							cache: false,
							contentType: false,
							processData: false
						
						});
						
				});
				
				
				$('#tab1').validate({
					rules:{
						en_news_title:{
							required:true
						},
						news_date:{
							required:true
						},
						news_image:{
						required:function(){
									if($('#new_img').val())
									{
										return false;
									}
									else
									{
										return true;
									}
							}
						},
						en_news_content:{
							required:true
						},
						es_news_title:{
							required:true
						}
					},
					messages: {
						en_news_title:{
							required:"Required."
						},
						news_date:{
							required:"Required."
						},
						news_image:{
							required:"Required."
						},
						en_news_content:{
							required:"Required."
						},
						es_news_title:{
							required:"Required."
						}
						
					},
					
				});
			});
			
			function progressHandlingFunction(e){
				if(e.lengthComputable){
					$('progress').attr({value:e.loaded,max:e.total});
				}
			}
			 