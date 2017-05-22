$(document).ready(function() {


    /*
				* Programmer Name: AD
				* Purpose: To ensure that both password fields have matching content.
				* Date: 07 Oct 2014
				* Dependency: admin.php
				*/
    $('#conf_password').blur(function() {

        var str1 = $('#new_password').val();
        var str2 = $('#conf_password').val();
        if(str1.localeCompare(str2) != 0)
        {
            $('#error_pwd').removeAttr('hidden');
        }
        else
        {
            $('#error_pwd').attr('hidden','true');
        }
    });
                                
                                
    jQuery.validator.addMethod( 'validemail', function(value) {
        return /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,100}|[0-9]{1,3})(\]?)$/.test(value);
    },"Please enter a valid email address.");


    $('#user_form').validate({
        rules:{
            txtusername:{
                required:true
            },
            txtfname:{
                required:true
            },
            txtlname:{
                required:true
            },
            txtemail:{
                required:true,
                validemail: true,
                email:true
            },
            image:{
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
            }
        },
        messages:{
            txtusername:{
                required:"Please enter username."
            },
            txtfname:{
                required:function(){
                    console.log(user_type);
                    if( user_type == 3 )
                    {
                        return "Please enter restaurant name.";
                    }
                    else
                    {
                        return "Please enter first name.";
                    }
                }
            },
            txtlname:{
                required:"Please enter last name."
            },
            txtemail:{
                required:"Please enter email .",
                email:"Please enter a valid email."
            },
            image:{
                required:"Please select image."
            }
        }
    });




});

function progressHandlingFunction(e){
    if(e.lengthComputable){
        $('progress').attr({
            value:e.loaded,
            max:e.total
        });
    }
}
