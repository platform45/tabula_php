$(document).ready(function(e) {
		
                $(".goo-collapsible > li > span > a").on("click", function(e){
                
		
		e.preventDefault()
                var menuid=this.id;
                
		//if submenu is hidden, does not have active class  
		if(!$(this).hasClass("active")) {
			
			// hide any open menus and remove active classes
			$(".goo-collapsible li ul").slideUp(350);
			$(".goo-collapsible li span a").removeClass("active");
		  
			// open submenu and add the active class
			$(this).next("ul").slideDown(350);
			$(this).addClass("active");
                        $("#dropdown"+menuid+" ul").css("display","block");
                        
                        $(this).html("-");
		//if submenu is visible    
		}else  {
			
			//hide submenu and remove active class
			$(this).removeClass("active");
			$(this).next("ul").slideUp(350);
                        $("#dropdown"+menuid+" ul").css("display","none");
                       
                        $(this).html("+");
		}
	});
});


