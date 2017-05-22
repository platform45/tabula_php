 $('.op-main-banner').not('.op-innerpage-banner').css('min-height', $(window).height());
$(window).resize(function(){
  $('.op-main-banner').not('.op-innerpage-banner').css('min-height', $(window).height());
 })
$('.op-main-slider').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 3000,
      fade:true,
      arrows: true,
      dots: true,
	  prevArrow:'<i class="slick-prev slick-arrow icon-arrow-left"></i>',
  	  nextArrow:'<i class="slick-next slick-arrow icon-arrow-right"></i>',
      responsive: [
        {
          breakpoint: 992,
          settings: {
            arrows: false,
            slidesToScroll: 1,
            slidesToShow: 1
          }
        }        
      ]
  });
$('.popular-slider').slick({
  centerMode: true,
  centerPadding: '60px',
  slidesToShow: 3,
  prevArrow:'<span class="pop-slide-left"><i class="icon-left"></i></span>',
  nextArrow:'<span class="pop-slide-right"><i class="icon-right"></i></span>',
  arrows: true,
  responsive: [
    {
      breakpoint: 768,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: 3
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: 1
      }
    }
  ]
});
  $('nav').affix({
         offset: {
           top: 100
         }
         }); 

function toggleIcon(e) {
        $(e.target)
            .prev('.panel-heading')
            .find(".more-less")
            .toggleClass('icon-plus icon-minus');
    }
    $('.panel-group').on('hidden.bs.collapse', toggleIcon);
    $('.panel-group').on('shown.bs.collapse', toggleIcon);			
	
	(function($){
$(document).ready(function(){

$('.sidebar_accordian li.active').addClass('open').children('ul').show();
	$('.sidebar_accordian li.has-sub>a').on('click', function(){
		$(this).removeAttr('href');
		var element = $(this).parent('li');
		if (element.hasClass('open')) {
			element.removeClass('open');
			element.find('li').removeClass('open');
			element.find('ul').slideUp(200);
		}
		else {
			element.addClass('open');
			element.children('ul').slideDown(200);
			element.siblings('li').children('ul').slideUp(200);
			element.siblings('li').removeClass('open');
			element.siblings('li').find('li').removeClass('open');
			element.siblings('li').find('ul').slideUp(200);
		}
	});

});
})(jQuery);
