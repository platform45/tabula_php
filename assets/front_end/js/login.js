$(document).ready(function()
  {
      $("#sign_in_btn").click(function (e) {
          $('#loginModal').keypress(function (event) {
              var keycode = (event.keyCode ? event.keyCode : event.which);
              if (keycode == '13') {
                  $("#login_form_submit").click();
              }
          });
      });

      $("#sign_up_btn").click(function (e) {
		  $('#signupModal').keypress(function (event) {
              var keycode = (event.keyCode ? event.keyCode : event.which);
              if (keycode == '13') {
                  $("#sign_form").submit();
              }
          });
      });



      $("#dob").datepicker({
          dateFormat: 'dd-mm-yy',
          maxDate: new Date(),
          use24hours: false,
          changeMonth: true,
          changeYear: true
      });

    var base_url =$("#base_url").val();
    
    $("#state").change(function()
    {
      $.post(base_url+"admin/cities/get_city_by_region",
      {
        region_id: $("#state").val()
      },
      
      function(option_list)
      {
		  
        $("#city").html(option_list);
        //$("#city").trigger("change");
        //$('#city').selectpicker("refresh");
        $("#city").val('').trigger('change');
      });
    });

   
    $('#password').keypress(function( e )
    {    
        if(e.which === 32) 
            return false;
    });
    
     $('#confirm_password').keypress(function( e )
    {    
        if(e.which === 32) 
            return false;
    });
    
    // disable alpha and special characters
    $('#contact_number').keydown(function(event)
    {
      // Allow special chars + arrows       
      if (event.keyCode == 46 || event.keyCode == 189 || event.keyCode == 109 || event.keyCode == 190 || event.keyCode == 110 || event.keyCode == 8 || event.keyCode == 9 
        || event.keyCode == 27 || event.keyCode == 13 || event.keyCode == 173 
        || (event.keyCode == 65 && event.ctrlKey === true) 
        || (event.keyCode >= 35 && event.keyCode <= 39)){
            return;
      }
      else
      {
        // If it's not a number stop the keypress
        if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 ))
        {
            event.preventDefault(); 
        }   
      }
    });
    
    // method for validationg password
    jQuery.validator.addMethod( 'passwordMatch', function(value)
    {
      if(value != '')
        return /^.*(?=.{6,20})(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[_~\-!@#\$%\^&\*\(\)]).*$/.test(value);
      else
        return true;

    });
	
	jQuery.validator.addMethod('checkValue', function()
    {
		
        return true;
    });


    
    // $( "#dob" ).datetimepicker();
    $('#sign_form').validate({
        ignore: ':not(select:hidden, input:visible, textarea:visible)',
      rules:{
        first_name:{
          required:true
        },
        email_address:{
          required:true,
          email:true,
          remote:{
             url: base_url+"front/home/check_email_exist",
             type: "post",
             data: {
                 "title": function(){ return $("#email_address").val(); }
             }
          }
        },
        contact_number:{
          required:true,
          minlength:10,
          maxlength:10          
        },
        password:{
          required:true,
          minlength:6,
          passwordMatch:true
        },
        conf_password:{
          required:true,
          equalTo: "#password"
        },
        dob:{
          required:true          
        },
        gender:{
          required:true,
          checkValue: true          
        },
        city:{
          required:true,
		  checkValue: true		  
        },
        state:{
          required:true          
        },
        radio:{
          required:true          
        },
        profile_image:{
          required:true,
          accept:'jpg,jpeg'
        }

      },
      messages:{
        first_name:{
          required:"Please enter full name."
        },
        email_address:{
          required:"Please enter email.",
          email:"Please enter valid email address",
          remote: "Email address already exist."
        },
        contact_number:{
          required:"Please enter contact number.",
          minlength:"Please enter valid contact number.",
          maxength:"Please enter valid contact number."
        },
        password:{
          required:"Please enter password.",
          minlength:"Please enter valid password.",
          passwordMatch:"Please enter valid password."
        },
        conf_password:{
          required:"Please confirm password.",
          equalTo: "Password and confirm password must match."
        },
        dob:{
          required:"Please select date of birth."          
        },
        gender:{
          required:"Please select gender."          
        },
        city:{
          required:"Please select city."          
        },
        state:{
          required:"Please select state."          
        },
        radio:{
          required:"Please accept terms and conditions."          
        },
        profile_image:{
          required:"Please select image.",
          accept:"Only jpg and jpeg images are accepted."
        }

      },
      errorPlacement: function(error, element)
      {
        if(element.attr("name") == "gender")
        {
          error.appendTo( element.parent("div") );
        } 
        else if(element.attr("name") == "state")
        {
          error.appendTo( element.parent("div") );
        } 
        else if(element.attr("name") == "city")
        {
          error.appendTo( element.parent("div") );
        } 
        else if(element.attr("name") == "profile_image")
        {
          error.insertAfter( element.parent(".fileUpload") );
        } 
        else
        {
          error.insertAfter(element);
        } 
      }
    });
    
      $('#login_form').validate({
        rules:{
            email:{
                email:true,
                required:true                
            },
            user_type:{                
                required:true                
            },
            password:{
                required:true                
            }            
        },
        messages:
          {
             email:
            {
                email: "Please enter proper email address.",
                required:"Email is required."               
            },
             user_type:{                
                required:"Please select user type."                
            },
            password:
            {
                required:"Password is required."                
            }                       
        }
    });	
    
      $('#forgetForm').validate({
        rules:{
            email:{
                required:true,
                email:true
            }
                     
        },
        messages:{
             email:{
            required:"Please enter email address." ,
            email:"Please enter valid email address."
          }
        }
    });	
    
    $('#forget_password').validate({
        rules:{
            email:{
                required:true,
                email:true
            }
                     
        },
        messages:{
             email:{
                required:"Please enter email address." ,
                email:"Please enter valid email address."
                       
        }
        }
    });	
    
    
    // open forget password field
    $("#forget_pass").on("click",function(){
       if($("#open_form").css('display')=='none'){
		  $("#open_form").show("500");
	}
        else{
		 $("#open_form").hide("500");
	}
        
    });   
    
    // open modal on create account click
    $("#sign_up_modal").on("click",function()
    {
       $('#loginModal').modal('hide');
         setTimeout(function () {
           $('#signupModal').modal('show');
        }, 1000);
    });
	
	

      $("#login_form_submit").click(function(){
		  
		  
          $("#error_message_for_user_login").html('');
          $("#login_form").validate();
          var is_validate = $("#login_form").valid();
          if(is_validate)
          {
              var base_url = $("#base_url").val();
              var user_type = $("#user_type").val();
              var email = $("#user_email").val();
              var password = $("#user_password").val();

              $.ajax({
                  url: base_url + "user_login",
                  type: "POST",
                  data: {
                      email : email,
                      password : password,
                      user_type : user_type
                  },
                  datatype: 'json',
                  success: function(data){
                      var data = jQuery.parseJSON(data);
                      if(data.status == 1)
                      {
                          location.reload();
                      }
                      else
                      {
                          $("#error_message_for_user_login").html(data.message);
                      }
                  }
              });
          }
      });
	  
	  $("#forget_pass_form_submit").click(function(){
		  
		  var $this = $(this);
          $("#error_message_for_forget_pass").html('');
          $("#forget_password").validate();
          var is_validate = $("#forget_password").valid();
          if(is_validate)
          {
              var base_url = $("#base_url").val();
              var email = $("#forget_pass_email").val();
              $this.text("Processing...");

              $.ajax({
                  url: base_url + "forget_password",
                  type: "POST",
                  data: {
                      email : email
                  },
                  datatype: 'json',
                  success: function(data){
                      var data = jQuery.parseJSON(data);
                      if(data.status == 1)
                      {
                          location.reload();
                      }
                      else
                      {
                          $("#error_message_for_forget_pass").html(data.message);
						  $this.text("Send");
                      }
                  }
              });
          }
      });
	  
	  $("#sign_up_submit").click(function(){
		$("#sign_up_gender_error_placement").html('');
		$("#sign_up_city_error_placement").html('');		
		$("#sign_up_state_error_placement").html('');
		$("#sign_up_country_error_placement").html('');					  
		
		var is_validate = $("#sign_form").valid();
		var gender = $("#gender").val();
		var country = $("#country").val();
		var state = $("#state").val();
		var city = $("#city").val();
		
		if(gender == '')
		{
			$("#sign_up_gender_error_placement").html('Please select gender');	
			is_validate = 0;
		}
		
		if(city == '')
		{
			$("#sign_up_city_error_placement").html('Please select city');				
			is_validate = 0;
		}
		
		if(state == '')
		{
			$("#sign_up_state_error_placement").html('Please select state');				
			is_validate = 0;
		}
		
		if(country == '')
		{
			$("#sign_up_country_error_placement").html('Please select country');				
			is_validate = 0;
		}


		if(is_validate){
			$('form#sign_form').submit();
		 }
	  });
  });

   