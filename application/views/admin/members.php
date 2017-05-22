<script type="text/javascript" >
    $(document).ready(function() {
<?php $arr = $this->session->userdata('menu');
?>
        $(".sidebar-nav #menu<?php echo $arr['Guests'][1]; ?>").addClass("act");

        /*
         * Programmer Name: Akash Deshmukh
         * Purpose: DataTable initialisation.
         * Date: 02 Sept 2016
         * Dependency: members.php
         */
        var oTable = $('#test').DataTable({
            "columns":[
               null,         
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false }		
            ],

            "dom" : '<tlip>',
            "iDisplayLength":25,
            "lengthMenu": [ 25, 50, 75, 100 ]
        });
        
        $("#test").on("click",".delete_button",function() {
            var id = this.id;
            var lang_id = $(".delete_button").attr("lang_id");
            $("#myModal").attr("data-id", id);
            $("#myModal").attr("lang_id", lang_id);
            $("#myModal").attr("aria-hidden",false);
        });

        $("#btn-danger1").click(function(){

            var str = "<?php echo base_url() . 'admin/members/delete_members/'; ?>";
            var teststr = str.concat($("#myModal").attr("data-id"));

            window.location.href=teststr;
        }); 

       $(document).on("click",".status", function(){
            var id = this.id;
            $("#btn-status").attr("data-id", id);
            $("#btn-status").attr("data-status",$(this).attr("data-status"));
        });

        $( "#btn-status" ).bind( "click", function() {
            var spanid= $(this).attr("data-id");
            var changeStatus = $(this).attr("data-status");
            $.ajax({
                url:'<?php echo base_url(); ?>admin/members/update_members_status',
                type:"POST",
                data:{"user_id": $(this).attr("data-id"),"changeStatus":changeStatus },
                success:function(){
                    if(changeStatus == 1)
                    {
                        $("#status"+spanid+" i.fa-2x").removeClass("fa-check");
                        $("#status"+spanid+" i.fa-2x").addClass("fa-ban");
                        $("#"+spanid).attr("data-status",0);
                    }
                    else
                    {
                        $("#status"+spanid+" i.fa-2x").removeClass("fa-ban");
                        $("#status"+spanid+" i.fa-2x").addClass("fa-check");
                        $("#"+spanid).attr("data-status",1);
                    }
                    $().toastmessage('showSuccessToast', "Status changed successfully.");
                }
            });
        }); 
             
        $("#filter_role").change(function(e){
                            
            if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')
            {
                $.session.set("members_email",$('#filter_email').val());
                $.session.set("members_search",$('#filter_members').val());
                $.session.set("members_role",$('#filter_role').val());
                oTable
                .columns( 1)
                .search( $('#filter_members').val() )
                .draw();
                oTable
                .columns( 2 )
                .search( $('#filter_email').val() )
                .draw();
                oTable
                .columns( 3 )
                .search( $('#filter_role').val() )
                .draw();
            }
            else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')
            {
                $.session.set("members_search",$('#filter_members').val());
                $.session.set("role_search",$('#filter_role').val());

                oTable
                .columns( 1 )
                .search( $('#filter_members').val() )
                .draw();
                oTable
                .columns( 3 )
                .search( $('#filter_role').val() )
                .draw();
            }
                                    
            else if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')
            {                     
                ;                                            $.session.set("members_search",$('#filter_members').val());
                $.session.set("members_role",$('#filter_role').val());

                oTable
                .columns( 1 )
                .search( $('#filter_members').val() )
                .draw();
                oTable
                .columns( 2 )
                .search( $('#filter_email').val() )
                .draw();
            }
                                    
            else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() == '')
            {                      
                $.session.set("members_email",$('#filter_email').val());

                oTable
                .columns( 2 )
                .search( $('#filter_email').val() )
                .draw();
            }
            else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')
            {                     
                $.session.set("members_email",$('#filter_email').val());

                oTable
                .columns( 1 )
                .search( $('#filter_members').val() )
                .draw();
            }
            else if($('#filter_email').val() == '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')
            {                      
                $.session.set("members_role",$('#filter_role').val());

                oTable
                .columns( 3 )
                .search( $('#filter_role').val() )
                .draw();
            }
            else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')
            {                     
                $.session.set("members_role",$('#filter_role').val());                                            
                $.session.set("members_email",$('#filter_email').val());
                                            
                oTable
                .columns( 2 )
                .search( $('#filter_email').val() )
                .draw();

                oTable
                .columns( 3 )
                .search( $('#filter_role').val() )
                .draw();
            }
            else
            {
                $("#clear").click();
            }              
        });

        $("#filter_email").keypress(function(e){
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode==13)
            {
                if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')
                {
                    $.session.set("members_email",$('#filter_email').val());
                    $.session.set("members_search",$('#filter_members').val());
                    $.session.set("members_role",$('#filter_role').val());
                    oTable
                    .columns( 1)
                    .search( $('#filter_members').val() )
                    .draw();
                    oTable
                    .columns( 2 )
                    .search( $('#filter_email').val() )
                    .draw();
                    oTable
                    .columns( 3 )
                    .search( $('#filter_role').val() )
                    .draw();
                }
                else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')
                {
                    $.session.set("members_search",$('#filter_members').val());
                    $.session.set("role_search",$('#filter_role').val());

                    oTable
                    .columns( 1 )
                    .search( $('#filter_members').val() )
                    .draw();
                    oTable
                    .columns( 3 )
                    .search( $('#filter_role').val() )
                    .draw();
                }
                                    
                else if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')
                {                  
                    ;                                            $.session.set("members_search",$('#filter_members').val());
                    $.session.set("members_role",$('#filter_role').val());

                    oTable
                    .columns( 1 )
                    .search( $('#filter_members').val() )
                    .draw();
                    oTable
                    .columns( 2 )
                    .search( $('#filter_email').val() )
                    .draw();
                }
                                    
                else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() == '')
                {                    
                    $.session.set("members_email",$('#filter_email').val());

                    oTable
                    .columns( 2 )
                    .search( $('#filter_email').val() )
                    .draw();
                }
                else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')
                {                   
                    $.session.set("members_email",$('#filter_email').val());

                    oTable
                    .columns( 1 )
                    .search( $('#filter_members').val() )
                    .draw();
                }
                else if($('#filter_email').val() == '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')
                {                     
                    $.session.set("members_role",$('#filter_role').val());
                    oTable
                    .columns( 3 )
                    .search( $('#filter_role').val() )
                    .draw();
                }
                else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')
                {                   
                    $.session.set("members_role",$('#filter_role').val());                                            
                    $.session.set("members_email",$('#filter_email').val());
                                            
                    oTable
                    .columns( 2 )
                    .search( $('#filter_email').val() )
                    .draw();

                    oTable
                    .columns( 3 )
                    .search( $('#filter_role').val() )
                    .draw();
                }
                else
                {
                    $("#clear").click();
                }
            }
        });

        $("#filter_members").keypress(function(e){
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode==13)
            {
                if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')
                {
                    $.session.set("members_email",$('#filter_email').val());
                    $.session.set("members_search",$('#filter_members').val());
                    $.session.set("members_role",$('#filter_role').val());
                    oTable
                    .columns( 1)
                    .search( $('#filter_members').val() )
                    .draw();
                    oTable
                    .columns( 2 )
                    .search( $('#filter_email').val() )
                    .draw();
                    oTable
                    .columns( 3 )
                    .search( $('#filter_role').val() )
                    .draw();
                }
                else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')
                {
                    $.session.set("members_search",$('#filter_members').val());
                    $.session.set("role_search",$('#filter_role').val());

                    oTable
                    .columns( 1 )
                    .search( $('#filter_members').val() )
                    .draw();
                    oTable
                    .columns( 3 )
                    .search( $('#filter_role').val() )
                    .draw();
                }
                                    
                else if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')
                {                  
                    ;                                            $.session.set("members_search",$('#filter_members').val());
                    $.session.set("members_role",$('#filter_role').val());

                    oTable
                    .columns( 1 )
                    .search( $('#filter_members').val() )
                    .draw();
                    oTable
                    .columns( 2 )
                    .search( $('#filter_email').val() )
                    .draw();
                }
                                    
                else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() == '')
                {                    
                    $.session.set("members_email",$('#filter_email').val());

                    oTable
                    .columns( 2 )
                    .search( $('#filter_email').val() )
                    .draw();

                }
                else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')
                {                  
                    $.session.set("members_email",$('#filter_email').val());

                    oTable
                    .columns( 1 )
                    .search( $('#filter_members').val() )
                    .draw();
                }
                
                else if($('#filter_email').val() == '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')
                {                   
                    $.session.set("members_role",$('#filter_role').val());

                    oTable
                    .columns( 3 )
                    .search( $('#filter_role').val() )
                    .draw();

                }
                else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')
                {                  
                    $.session.set("members_role",$('#filter_role').val());                                            
                    $.session.set("members_email",$('#filter_email').val());
                                            
                    oTable
                    .columns( 2 )
                    .search( $('#filter_email').val() )
                    .draw();

                    oTable
                    .columns( 3 )
                    .search( $('#filter_role').val() )
                    .draw();
                }
                else
                {
                    $("#clear").click();
                }
            }
        });

        $('#show').click(function(){
                        
            if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')
            {
                $.session.set("members_email",$('#filter_email').val());
                $.session.set("members_search",$('#filter_members').val());
                $.session.set("members_role",$('#filter_role').val());
                oTable
                .columns( 1)
                .search( $('#filter_members').val() )
                .draw();
                oTable
                .columns( 2 )
                .search( $('#filter_email').val() )
                .draw();
                oTable
                .columns( 3 )
                .search( $('#filter_role').val() )
                .draw();
            }
            
            else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')
            {
                $.session.set("members_search",$('#filter_members').val());
                $.session.set("role_search",$('#filter_role').val());

                oTable
                .columns( 1 )
                .search( $('#filter_members').val() )
                .draw();
                oTable
                .columns( 3 )
                .search( $('#filter_role').val() )
                .draw();
            }
                                    
            else if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')
            {
                                       
                ;     $.session.set("members_search",$('#filter_members').val());
                $.session.set("members_role",$('#filter_role').val());

                oTable
                .columns( 1 )
                .search( $('#filter_members').val() )
                .draw();
                oTable
                .columns( 2 )
                .search( $('#filter_email').val() )
                .draw();
            }
                                    
            else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() == '')
            {                        
                $.session.set("members_email",$('#filter_email').val());

                oTable
                .columns( 2 )
                .search( $('#filter_email').val() )
                .draw();
            }
            else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')
            {                       
                $.session.set("members_email",$('#filter_email').val());

                oTable
                .columns( 1 )
                .search( $('#filter_members').val() )
                .draw();

            }
            else if($('#filter_email').val() == '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')
            {                      
                $.session.set("members_role",$('#filter_role').val());

                oTable
                .columns( 3 )
                .search( $('#filter_role').val() )
                .draw();
            }
            else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')
            {
                                        
                $.session.set("members_role",$('#filter_role').val());                                            
                $.session.set("members_email",$('#filter_email').val());
                                            
                oTable
                .columns( 2 )
                .search( $('#filter_email').val() )
                .draw();

                oTable
                .columns( 3 )
                .search( $('#filter_role').val() )
                .draw();
            }
            else
            {
                $("#clear").click();
            }
        });

        if($.session.get("addedit")==1)
        {
            if($.session.get("members_email") || $.session.get("members_search"))
            {
                $("#filter_email").val($.session.get("members_email"));
                $("#filter_members").val($.session.get("members_search"));
                $.session.set("is_table_status",0);
                $('#show').click();
            }
            else
            {
                $.session.set("addedit",0);
            }
        }
        else if($.session.get("is_table_status")==1)
        {
            //alert($.session.get("addedit"));
            $.session.set("is_table_status",0);
            $('#clear').click();
        }

        $('#clear').click( function ()
        {
            oTable.state.clear();
            $.session.remove("members_email");
            $.session.remove("members_search");
            $('#filter').val('');
            location.reload();
        });
    });
                
    function search()
    {                     
    }
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title">Guests</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">Guests</li>
        </ul>
    </div>
    <div class="main-content">
        <div id="custom_filter"> 
            <input type="text" class="form-control" style="width:17%;display:inline;" id="filter_members" name="filter_members" placeholder="Search Guest" value=""/>
            <input type="text" class="form-control " style="width:17%;display:inline;margin-left:10px;" id="filter_email" name="filter_email" placeholder="Search email" value=""/>
            <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
            <input type="button" class="btn btn-primary " id="clear" name="clear" style="margin-left:10px" value="Clear" />
            <a href="<?php echo base_url(); ?>admin/users/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Guest</a>
        </div>
        <br/>
        <div class="table-responsive">
            <table class="display hover cell-border table" id="test">
                <thead>
                    <tr class="table_th_tr">
                        <th class="row-col_4">SEQ. NO.</th>                    
                        <th class="row-col_1">NAME</th>
                        <th class="row-col_1">EMAIL</th>
                        <th class="row-col_1">STATUS</th>
                        <th class="row-col_1">EDIT</th>
                        <th class="row-col_1">DELETE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php
                    if ($members) {
                        foreach ($members as $rec):
                            ?>
                            <tr style="text-align:center;">
                                <td> <?php echo $i++; ?></td>
                                <td style="text-align:center;"> <?php echo $rec['user_first_name']; ?></td>
                                <td style="text-align:center;"> <?php echo $rec['user_email']; ?></td>
                                <td> <a title="Change status" href="#myStatus" role="button" data-toggle="modal" id="<?php echo $rec['user_id']; ?>" class="status" data-status="<?php echo $rec['user_status']; ?>"><span id="status<?php echo $rec['user_id']; ?>" class="status_icon"><?php if ($rec['user_status'] == 0): ?><i class="fa fa-ban fa-2x" ></i><?php else: ?><i class="fa fa-check fa-2x" ></i><?php endif ?></span></a></td>
                                <td><a href="<?php echo base_url() . 'admin/users/addedit/' . $rec['user_id']; ?>" title="Edit user"><i class="fa fa-pencil-square-o fa-2x" ></i></a></td>
                                <td><a href="#myModal" class="delete_button" id="<?php echo $rec['user_id']; ?>" role="button" data-toggle="modal" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a></td>
                            </tr>
                            <?php
                        endforeach;
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="modal small fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                        <h3 id="myModalLabel">Delete Confirmation</h3>
                    </div>
                    <div class="modal-body">
                        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete this record ?<br>This cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
                        <button class="btn btn-primary" id="btn-danger1" data-dismiss="modal">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal small fade" id="myStatus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                        <h3 id="myModalLabel">Change Status</h3>
                    </div>
                    <div class="modal-body">
                        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to change the status?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
                        <button class="btn btn-primary" id="btn-status" data-dismiss="modal">Change</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>