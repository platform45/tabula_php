<script type="text/javascript">
        
        $(document).ready(function(){
            <?php $arr = $this->session->userdata('menu');
            ?>
            $(".sidebar-nav #menu<?php echo $arr['Push Notifications'][1];?>").addClass("act");
            
            $("#frmNotifications").validate({
                     rules: {
                         txtnotification:{
                             required:true,
                             maxlength: 250
                         }
                     },
                     messages:{
                             txtnotification:{
                                     required:"Please enter notification text.",
                                     maxlength:"Maximum 250 characters are allowed."
                             }
                     }
             });
             
             $("#txtnotification").keyup(function(){
                var iCharacters      = $(this).val().length;
                var iCharatersLeft   = 250 - iCharacters;
                if(iCharatersLeft >= 0)
                    $("#no_of_characters").html(iCharatersLeft);
                else
                    $("#no_of_characters").html(0);
             });
        });
</script>
<style>
    .character-limit{
        display: inline-block;
        width: 100%;
    }
</style>
<div class="content">
        <div class="header">
            <h1 class="page-title">Send Push Notification</h1>
            <ul class="breadcrumb">
                    <li><a href="<?php echo $this->config->item("admin_url");?>dashboard">Home</a> </li>
                    <li><a href="<?php echo $this->config->item("admin_url");?>push_notification">Push Notifications</a></li>
            </ul>
        </div>
        <div class="main-content">
                <div class="error jx_indicator_error">* indicates required field.</div>
                    <div class="panel panel-default" align="left" style="border:0px;box-shadow: none;">
                    <div class="panel-body" >
                    <div class="dialog1">
                    <form id="frmNotifications" action="<?php echo $this->config->item("admin_url");?>push_notification" method="POST" class="form-horizontal">
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Notification<span class="error" >*</span></label>
                                    <div class="col-sm-6">
                                        <textarea id="txtnotification" name="txtnotification" class="form-control" maxlength="250" rows="5" style="resize: none;"></textarea>
                                        <span class="error"><?php echo form_error('txtnotification');?></span>
                                        <div class="character-limit">
                                            <span id="no_of_characters">250</span> Characters Left
                                        </div>
                                    </div>
                            </div>
                            
                            <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                    <input type="submit" value="Send" class="btn btn-primary"/>
                                    <input type="Reset" value="Cancel" class="btn btn-primary"/>
                            </div>
                            </div>
                    </form>
                    </div>
                    </div>
            </div>					
        </div>
</div>