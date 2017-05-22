<?php
/*
* Programmer Name:AD
* Purpose: View to display menu on desired pages.
* Date:6 Oct 2014
* Dependency: None
*/
?>

<div class="sidebar-nav">
    <a href="<?php echo base_url(); ?>admin/dashboard">
        <span class="logo" style="display:none;"><img src="<?php echo $this->config->item('assets'); ?>images/logo.png"
                                                      alt=""/></span></a>
    <ul class="dashboard-menu nav nav-list collapse in">


        <?php
        $arrSessMnu = $this->session->userdata('menu');
        if (!empty($arrSessMnu))
            foreach ($arrSessMnu as $row => $val):?>

                <li><a id="menu<?php echo $val[1]; ?>" class="nav-header m1"
                       href="<?php echo base_url() . "admin/" . $val[0]; ?>"><img src="<?php echo ADMIN_MENU_ICONS; ?><?php echo $val[2]; ?>.png" style="padding-right:5px;"></img><?php echo $row; ?> </a>
                </li>

            <?php endforeach; ?>
        <!--<li><a class="nav-header m2" href="#">News</a></li>
        <li><a class="nav-header m3" href="#">User</a></li>-->
    </ul>
</div>