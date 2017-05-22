(function () {
  'use strict';

  if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
    var msViewportStyle = document.createElement('style')
    msViewportStyle.appendChild(
      document.createTextNode(
        '@-ms-viewport{width:auto!important}'
      )
    )
    document.querySelector('head').appendChild(msViewportStyle)
  }

})();



$('.mobileslider').slick({
  dots: true,
  infinite: true,
  speed: 500,
  fade: true,
  autoplay: true,
  cssEase: 'linear'
});

$('.innersliderin').slick({
  dots: true,
  infinite: true,
  speed: 500,
  fade: true,
  cssEase: 'linear'
});



$('.reviews').slick({
  centerMode: true,
  infinite: true,
  arrows: false,
  centerPadding: '0',
  slidesToShow: 3,
  autoplay: true,
  responsive: [
    {
      breakpoint: 991,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '40px',
        slidesToShow: 1
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


$('.restaurantsdetail').slick({
  centerMode: true,
  infinite: true,
  arrows: false,
  centerPadding: 0,
  slidesToShow: 3,
  autoplay: true,
  responsive: [
    {
      breakpoint: 767,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: 0,
        slidesToShow: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: 0,
        slidesToShow: 1
      }
    }
  ]
});

$(function () {
  
  
  if($('#datetimepicker1').length){
    $('#datetimepicker1').datetimepicker({
      minDate: new Date(),
	  defaultDate: new Date(),
      format: 'L',
      format: 'DD-MM-YYYY'
    });
  }
  
  if($('#dob_profile_datepicker').length){
    $('#dob_profile_datepicker').datetimepicker({
      maxDate: new Date(),
      format: 'L',
      format: 'DD-MM-YYYY'
    });
  }
  
  if($('#timepicker1').length){
    $('#timepicker1').datetimepicker({
      format: 'HH:mm'
    });
  }

  
});


$(".navbar-toggle.menu_icon").click(function(){
    $("body").toggleClass("shadow-visible");
});



$("#filter").click(function(){
    $("body").addClass("filteroverflow");
}); 

$("#filterclose").click(function(){
    $("body").removeClass("filteroverflow");
}); 



$("#searchbutton").click(function(){
    $("#searchbutton-box").addClass("searchvisibility");
}); 

$("#serchpopup").click(function(){
    $("#searchbutton-box").removeClass("searchvisibility");
}); 

if($('#campTab').length){
  $('#campTab').tabCollapse();  
}
  
  $('[data-toggle="tab"]').each(function(){
    var windowHash = window.location.hash;
    if(windowHash){
      $('[href="'+windowHash+'"]').trigger('click');
      $("html, body").animate({ scrollTop: $('.bodyContainer').offset().top }, 600);          
     }
  });
  
  $('.toplinks .location.collapse').on('shown.bs.collapse', function (e) {
    console.log(e.currentTarget.id);
  $('.toplinks .location.collapse').not($('#'+e.currentTarget.id)).collapse('hide');
  })
  $('.navbar-collapse').on('shown.bs.collapse', function (e) {
    console.log(e.currentTarget.id);
    $('.toplinks .location.collapse').collapse('hide');
  })
  


$(function () {
    // $('.modal').modal({
    //     show: true,
    //     keyboard: false,
    //     backdrop: 'static'
    // });
    
    $('#myModal').on('hidden.bs.modal', function (e) {
          $('body').removeClass('filteroverflow');
    })

    $('.search-poup').on('hidden.bs.modal', function (e) {
          $('.searchbutton-box').removeClass('searchvisibility');
    })
});


$(".fancybox-button").fancybox({
    prevEffect    : 'none',
    nextEffect    : 'none',
    closeBtn    : false,
    helpers   : {
      title : { type : 'inside' },
      buttons : {}
    }
  });