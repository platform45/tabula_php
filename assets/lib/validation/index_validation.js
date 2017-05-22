$(document).ready(function() {
				$(".sidebar-nav #menu1").addClass("act");
				
				$.session.remove('slider_search');
				$.session.remove('footer_search');
				$.session.remove('handbook_search');
				$.session.remove('sponsors_search');
				$.session.remove("school_city");
				$.session.remove("news_search");
				$.session.remove("news_from");
				$.session.remove("news_to");
				$.session.remove("school_search");
				$.session.remove('sports_search');
				$.session.remove('schedule_search');
				$.session.remove('search_season');
				$.session.remove('search_sport');
				$.session.remove("brac_year");
				$.session.remove("brac_season");
				$.session.remove('extracurricular_search');
				
				/*
				* Programmer Name: AD
				* Purpose: DataTable initialisation.
				* Date: 07 Oct 2014
				* Dependency: admin.php
				*/
				 var oTable = $('#test').DataTable({
					"columns":[
						{ "bsearchable": false, "bSortable": false },
						{ "bsearchable": false, "bSortable": false },
						{ "bsearchable": false, "bSortable": false },
						{ "bsearchable": false, "bSortable": false },
						{ "bsearchable": false, "bSortable": false }		
					],
					"iDisplatlength": 25,
					"lengthMenu": [25,50,75,100],
					"dom" : '<tlip>'
				 });
			});
			
			