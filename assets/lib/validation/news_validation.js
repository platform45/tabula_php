			$(document).ready(function() {
				
				$.session.remove('slider_search');
				$.session.remove('footer_search');
				$.session.remove('handbook_search');
				$.session.remove('sponsors_search');
				$.session.remove("school_city");
				$.session.remove("school_search");
				$.session.remove('sports_search');
				$.session.remove('schedule_search');
				$.session.remove('search_season');
				$.session.remove('search_sport');
				$.session.remove("brac_year");
				$.session.remove("brac_season");
				$.session.remove('extracurricular_search');
				
				
				$('#from').datepicker();
				$('#from').datepicker("option", "dateFormat", "M d, yy");
				$('#from').datepicker("option", "showAnim", "slide");
				$('#from').datepicker("option","changeYear",true);
				$('#from').datepicker("option","changeMonth",true);
				$('#from').datepicker("option","showMonthAfterYear",true);
				$('#from').datepicker("option","showButtonPanel",true);
				$('#from').datepicker("option","onClose",function(selectedDate ){$( "#to" ).datepicker( "option", "minDate", selectedDate );});
				
				$('#to').datepicker();
				$('#to').datepicker("option", "dateFormat", "M d, yy");
				$('#to').datepicker("option", "showAnim", "slide");
				$('#to').datepicker("option","changeYear",true);
				$('#to').datepicker("option","changeMonth",true);
				$('#to').datepicker("option","showMonthAfterYear",true);
				$('#to').datepicker("option","showButtonPanel",true);
								
				
				
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
						{"type":"date"},
						null,
						{ "bsearchable": false, "bSortable": false },
						{ "bsearchable": false, "bSortable": false },
						{ "bsearchable": false, "bSortable": false }		
					],
					"dom" : '<tlip>',
					"iDisplayLength":25,
					"lengthMenu": [ 25, 50, 75, 100 ]
				 });
				
				$("#test_paginate").on("click",function(){
					info = oTable.page.info(); 
					$.session.set('page_number',info.page);
					var order = oTable.order(); 
					$.session.set('sort_column',order[0][0]); 
					$.session.set('sort_order',order[0][1]);
					//alert($.session.get('page_number'));
				});
				
				$("#test").on("click",function(){
					var order = oTable.order(); 
					$.session.set('sort_column',order[0][0]); 
					$.session.set('sort_order',order[0][1]);
					info = oTable.page.info(); 
					$.session.set('page_number',info.page);
				});
				
				$("#filter").keypress(function(e){
					var keycode = (e.keyCode ? e.keyCode : e.which);
					if(keycode==13)
					{
						if($('#filter').val()!='')
						{
							oTable
								.columns( 1 )
								.search( $('#filter').val() )
								.draw();
							
							$.session.set("news_search",$("#filter").val());
						}
						oTable.draw(); 
						check_pagination();
						$.session.set("news_from",$("#from").datepicker("getDate"));
						$.session.set("news_to",$("#to").datepicker("getDate"));
						
						$.fn.dataTable.ext.search.push(
							function( settings, data, dataIndex ) {
								
								var min = new Date($('#from').val()).getTime();
								var max = new Date($('#to').val()).getTime();
								var curr_date = new Date(data[2]).getTime(); // use data for the DATE column 
								if ((min <= curr_date && curr_date <= max)||
								   (isNaN( min ) && curr_date <= max)     ||
								   (min <= curr_date && isNaN( max ))     ||
								   (isNaN( min ) && isNaN( max ))
									)//True condition...
									{
										return true;
									}
										return false;
							}
						);
					}
				});
				
				$("#from").keypress(function(e){
					var keycode = (e.keyCode ? e.keyCode : e.which);
					if(keycode==13)
					{
						if($('#filter').val()!='')
						{
							oTable
								.columns( 1 )
								.search( $('#filter').val() )
								.draw();
							$.session.set("news_search",$("#filter").val());
						}
						oTable.draw(); 
						check_pagination();
						$.session.set("news_from",$("#from").datepicker("getDate"));
						$.session.set("news_to",$("#to").datepicker("getDate"));
						
						$.fn.dataTable.ext.search.push(
							function( settings, data, dataIndex ) {
								
								var min = new Date($('#from').val()).getTime();
								var max = new Date($('#to').val()).getTime();
								var curr_date = new Date(data[2]).getTime(); // use data for the DATE column 
								if ((min <= curr_date && curr_date <= max)||
								   (isNaN( min ) && curr_date <= max)     ||
								   (min <= curr_date && isNaN( max ))     ||
								   (isNaN( min ) && isNaN( max ))
									)//True condition...
									{
										return true;
									}
										return false;
							}
						);
					}
				});
				
				$("#to").keypress(function(e){
					var keycode = (e.keyCode ? e.keyCode : e.which);
					if(keycode==13)
					{
						if($('#filter').val()!='')
						{
							oTable
								.columns( 1 )
								.search( $('#filter').val() )
								.draw();
							$.session.set("news_search",$("#filter").val());
							
						}
						oTable.draw(); 
						check_pagination();
						
						$.session.set("news_from",$("#from").datepicker("getDate"));
						$.session.set("news_to",$("#to").datepicker("getDate"));
						$.fn.dataTable.ext.search.push(
							function( settings, data, dataIndex ) {
								
								var min = new Date($('#from').val()).getTime();
								var max = new Date($('#to').val()).getTime();
								var curr_date = new Date(data[2]).getTime(); // use data for the DATE column 
								if ((min <= curr_date && curr_date <= max)||
								   (isNaN( min ) && curr_date <= max)     ||
								   (min <= curr_date && isNaN( max ))     ||
								   (isNaN( min ) && isNaN( max ))
									)//True condition...
									{
										return true;
									}
										return false;
							}
						);
					}
				});
				
				function check_pagination()
				{
					var page_info = oTable.page.info();
					var page_length = page_info.pages;
					//if(parseInt($.session.get('page_number')) <= page_length)
					//	oTable.page(parseInt($.session.get('page_number'))).draw(false);
					if(parseInt($.session.get("sort_column")) && $.session.get("sort_order"))
					{
						oTable
							.order([parseInt($.session.get("sort_column")),$.session.get("sort_order")])
							.draw(false);
					}
					if(parseInt($.session.get('page_number')) <= page_length)
						oTable.page(parseInt($.session.get('page_number'))).draw(false);
				}
				
				$('#show').click(function(){
					if($('#filter').val()!='')
					{
						
						oTable
							.columns( 1 )
							.search( $('#filter').val() )
							.draw();
						$.session.set("news_search",$("#filter").val());
					}
					oTable.draw();
					
					check_pagination();
					
					$.session.set("news_from",$("#from").datepicker("getDate"));
					$.session.set("news_to",$("#to").datepicker("getDate"));
					
					$.fn.dataTable.ext.search.push(
						function( settings, data, dataIndex ) {
							
							var min = new Date($('#from').val()).getTime();
							var max = new Date($('#to').val()).getTime();
							var curr_date = new Date(data[2]).getTime(); // use data for the DATE column 
							if ((min <= curr_date && curr_date <= max)||
							   (isNaN( min ) && curr_date <= max)     ||
							   (min <= curr_date && isNaN( max ))     ||
							   (isNaN( min ) && isNaN( max ))
								)//True condition...
								{
									return true;
								}
									return false;
						}
					);
					
				});
				
				if($.session.get("addedit")==1)
				{
					if($.session.get("news_search") || $.session.get("news_from") || $.session.get("news_to"))
					{
						$("#filter").val($.session.get("news_search"));
						$('#from').datepicker('setDate',$.session.get("news_from"));
						$('#to').datepicker('setDate',new Date($.session.get("news_to")).getTime());
						//$("#from").val($.session.get("news_from"));
						
						
						$.session.set("is_table_status",0);
						$('#show').click();
					}
					else
					{
						$.session.set("addedit",0);
						/*$.session.set("page_number",0);
						$.session.set("sort_column",0);
						$.session.set("sort_order","asc");*/
						
					}
					check_pagination();
				}
				else if($.session.get("is_table_status")==1)
				{
					
					$.session.set("is_table_status",0);
					$('#clear').click();
				}
				else
				{
					$.session.set("page_number",0);
					$.session.set("sort_column",0);
					$.session.set("sort_order","asc");
				}
				
				$('#clear').click( function ()
				{
					oTable.state.clear();
					$.session.remove("news_search");
					$.session.remove("news_from");
					$.session.remove("news_to");
					$.session.remove("sort_column");
					$.session.remove("sort_order");
					$.session.set("page_number",0);
					$("#filter").val("");
					$("#from").val("");
					$("#to").val("");
					location.reload();
					
				});
			 
			 
	});
	
	/*
	* Programmer Name: AD
	* Purpose: Custom filter for range search on DATE field.
	* Date: 17 Oct 2014
	* Dependency: None.
	*/
	
	$.fn.dataTable.ext.search.push(
		function( settings, data, dataIndex ) {
			
			var min = new Date($('#from').val()).getTime();
			var max = new Date($('#to').val()).getTime();
			var curr_date = new Date(data[2]).getTime(); // use data for the DATE column 
			if ((min <= curr_date && curr_date <= max)||
			   (isNaN( min ) && curr_date <= max)     ||
			   (min <= curr_date && isNaN( max ))     ||
			   (isNaN( min ) && isNaN( max ))
				)//True condition...
				{
					$.session.set("news_from",$("#from").val());
					$.session.set("news_to",$("#to").val());
					return true;
				}
					return false;
		}
	);
		