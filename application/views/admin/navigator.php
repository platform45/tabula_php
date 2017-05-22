<?php
/*
* Programmer Name:AD
* Purpose: View for navigation area on each page.
* Date:6 Oct 2014
* Dependancy: None
*/
?>

<!--<div class="top_border"></div>-->
<div class="navbar navbar-default" role="navigation">

  <div class="navbar-header">
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a href="<?php echo base_url();?>admin/dashboard">
      <span class="navbar-brand" style=""><img src="<?php echo $this->config->item('assets');?>images/logo.png" alt="" /></span></a>
    </div>

    <div class="navbar-collapse" style="height: 1px;">
      <ul id="main-menu" class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
           <?php
           if($this->session->userdata('user_image')=="")
           {
            $image=$this->config->item('assets')."upload/adminuser/No_Image.jpg";
          }
          else{
            if( $this->session->userdata('user_type') == 3 )
              $image=$this->config->item('assets').'upload/member/'.$this->session->userdata('user_image');
            else if( $this->session->userdata('user_type') == 1)
              $image=$this->config->item('assets').'upload/subadmin/'.$this->session->userdata('user_image');
            else
             $image=$this->config->item('assets').'upload/adminuser/'.$this->session->userdata('user_image');
         }

         ?>
         <img height="30" width="30" src="<?php echo $image; ?>" style="border-radius:20px;"/>
         <strong>Welcome <?php echo $this->session->userdata('user_first_name');?></strong>
         <i class="fa fa-caret-down"></i>
       </a>

       <ul class="dropdown-menu" style="width:100%;">
         <?php if($this->session->userdata('user_type') == 3) { ?>
         <li><a class="i1" href="<?php echo base_url();?>admin/restaurant_login">Edit Profile</a></li>
         <?php  } else{ ?>
         <li><a class="i1" href="<?php echo base_url();?>admin/user">My Account</a></li>
         <?php } ?>
         <li class="divider"></li>
         <li style="float:center;"><a class="i2" tabindex="-1" href="<?php echo base_url();?>admin/admin/logout">Logout</a></li>

       </ul>
     </li>
   </ul>

 </div>
</div>