$(document).ready(function() {
							
				/*
				* Programmer Name: AD
				* Purpose: DataTable initialisation.
				* Date: 07 Oct 2014
				* Dependency: admin.php
				*/
				 var oTable = $('#test').DataTable({
					"columns":[
						null,
						null,
						null,
						null,
						null,
						{ "bsearchable": false, "bSortable": false },
						{ "bsearchable": false, "bSortable": false }		
					],
					"scrollY":        "400px",
					"scrollCollapse": true,
					stateSave: true,
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"dom" : '<tip>'
				 });
				
				/*
				* Programmer Name: AD
				* Purpose: To display confirmation dialog before deletion and passing the id to the confirm delete dialog.
				* Date: 07 Oct 2014
				* Dependency: admin.php
				*/
				$(".delete_button").click(function() {
				   var id = this.id;
				   var lang_id = $(".delete_button").attr("lang_id");

				   // Assign the id to a custom attribute called data-id and language id
				   $("#myModal").attr("data-id", id);
				   $("#myModal").attr("lang_id", lang_id);
				   $("#myModal").attr("aria-hidden",false);
				   //alert($("#myModal").attr("lang_id"));
				   
				});
				
				/*
				* Programmer Name: AD
				* Purpose: To redirect to the controller for deletion.
				* Date: 07 Oct 2014
				* Dependency: admin.php
				*/
				$(".btn-danger").click(function(){

					var str = "<?php echo base_url().'admin/delete_slider/'?>";
					var teststr = str.concat($("#myModal").attr("lang_id"));
					teststr = teststr.concat("/");
					teststr = teststr.concat($("#myModal").attr("data-id"));
					//alert(teststr);
					window.location.href=teststr;

				}); 
	});