<script type="text/javascript" >
        $(document).ready(function() {
            <?php $arr = $this->session->userdata('menu');
            ?>
            $(".sidebar-nav #menu<?php echo $arr['Role'][1];?>").addClass("act");

            /*
            * Programmer Name: AD
            * Purpose: DataTable initialisation.
            * Date: 27 Oct 2014
            * Dependency: admin.php
            */
                var oTable = $('#test').DataTable({
                    "columns":[
                            null,
                            null,
                            null,
                            { "bsearchable": false, "bSortable": false },
                            { "bsearchable": false, "bSortable": false },
                            { "bsearchable": false, "bSortable": false }		
                    ],

                    "dom" : '<tlip>',
                    "iDisplayLength":25,
                    "lengthMenu": [ 25, 50, 75, 100 ]
                });
				
                /*
                * Programmer Name: AD
                * Purpose: To display confirmation dialog before deletion and passing the id to the confirm delete dialog.
                * Date: 27 Oct 2014
                * Dependency: admin.php
                */
                $("#test").on("click",".delete_button",function() {
                    var id = this.id;
                    var lang_id = $(".delete_button").attr("lang_id");

                    // Assign the id to a custom attribute called data-id and language id
                    $("#myModal").attr("data-id", id);
                    $("#myModal").attr("lang_id", lang_id);
                    $("#myModal").attr("aria-hidden",false);
                    //alert($("#myModal").attr("lang_id"));

                });

                /*
                * Programmer Name: AD
                * Purpose: To redirect to the controller for deletion.
                * Date: 27 Oct 2014
                * Dependency: admin.php
                */
                $("#btn-danger1").click(function(){

                        var str = "<?php echo base_url().'admin/role/delete_role/';?>";
                        var teststr = str.concat($("#myModal").attr("data-id"));

                        window.location.href=teststr;

                }); 

                $('.status').click(function(){
                        var id = this.id;
                        $("#btn-status").attr("data-id", id);
                        $("#btn-status").attr("data-status",$(this).attr("data-status"));
                });

                $("#btn-status").click(function(){
                        var spanid= $(this).attr("data-id");
                        var changeStatus = $(this).attr("data-status");
                        $.ajax({
                                        url:'<?php echo base_url();?>admin/role/update_role_status',
                                        type:"POST",
                                        data:{"role_id": $(this).attr("data-id"),"changeStatus":changeStatus },
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

                $("#filter_role").keypress(function(e){
                        var keycode = (e.keyCode ? e.keyCode : e.which);
                        if(keycode==13)
                        {
                            if($('#filter_role').val() != '')
                            {
                                $.session.set("role_search",$('#filter_role').val());
                                oTable
                                        .columns( 1 )
                                        .search( $('#filter_role').val() )
                                        .draw();
                            }
                        }
                });



                $('#show').click(function(){
                    if($('#filter_role').val() != '')
                    {
                        $.session.set("role_search",$('#filter_role').val());

                        oTable
                                .columns( 1 )
                                .search( $('#filter_role').val() )
                                .draw();

                    }
                    else
                    {
                        //oTable.search('');
                        $('#clear').click();
                    }	
                });

                if($.session.get("addedit")==1)
                {
                        if($.session.get("role_search"))
                        {
                                $("#filter_role").val($.session.get("role_search"));
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
                        $.session.remove("role_search");
                        $('#filter').val('');
                        location.reload();
                });
            });
	</script>
<div class="content">
            <div class="header">
                    <h1 class="page-title">Role</h1>
                            <ul class="breadcrumb">
                                    <li><a href="<?php echo base_url();?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
                                    <li class="active">Role</li>
                            </ul>
            </div>
            <div class="main-content">
			
				
                        <div id="custom_filter"> 
                                <input type="text" class="form-control" style="width:15%;display:inline;" id="filter_role" name="filter_role" placeholder="Search role" value=""/>
                                <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
                                <input type="button" class="btn btn-primary " id="clear" name="clear" style="margin-left:10px" value="Clear" />
                                <a href="<?php echo base_url();?>admin/role/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Role </a>
                        </div>

                <br/>
			<div class="table-responsive">
            <table class="display hover cell-border table" id="test">
                <thead>
                    <tr class="table_th_tr">
                         <th class="row-col_4">SEQ. NO.</th>
                        <th class="row-col_1">ROLE</th>
                        <th class="row-col_1">DESCRIPTION</th>
                        <th class="row-col_1">STATUS</th>
                        <th class="row-col_1">EDIT</th>
                        <th class="row-col_1" >DELETE</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i=1;?>

                <?php 
                if($roleData)
                {
                    foreach($roleData as $rec):
                        ?>

                    <tr style="text-align:center;">

                                    <td> <?php echo $i++;?></td>
                                    <td style="text-align:left;"> <?php echo $rec['role_type']; ?></td>
                                    <td style="text-align:left;"> <?php echo $rec['role_description']; ?></td>
                                    <td> <?php if($rec['role_id']!=3) {  ?><a href="#myStatus" role="button" data-toggle="modal" id="<?php echo $rec['role_id'];?>" class="status" data-status="<?php echo $rec['role_status'];?>"><span id="status<?php echo $rec['role_id'];?>" class="status_icon"><?php if($rec['role_status']==0):?><i class="fa fa-ban fa-2x" ></i><?php else:?><i class="fa fa-check fa-2x" ></i><?php endif?><?php } else {echo "--";} ?></span></a></td>
                                    <td> <a href="<?php echo base_url().'admin/role/addedit/'.$rec['role_id'];?>" title="Edit Role"><i class="fa fa-pencil-square-o fa-2x" ></i></a></td>
                                    <td><?php if($rec['role_id']!=3) {  ?><a href="#myModal" class="delete_button" id="<?php echo $rec['role_id'];?>" role="button" data-toggle="modal" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a><?php } else {echo "--";} ?></td>
                    </tr>

                    <?php endforeach;
                }?>

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
                                            <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete role?<br>This cannot be undone.</p>
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