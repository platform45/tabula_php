<script type="text/javascript" >
$(document).ready(function() {
        <?php $arr = $this->session->userdata('menu')?>
        $(".sidebar-nav #menu<?php echo $arr['Channel'][1]?>").addClass("act");

        /*
        * Programmer Name: AD
        * Purpose: DataTable initialisation.
        * Date: 07 Oct 2014
        * Dependency: admin.php
        */
            var oTable = $('#test').DataTable({
                "columns":[
                        null,
                        null,
                        { "bsearchable": false, "bSortable": false },
                        { "bsearchable": false, "bSortable": false },
                        { "bsearchable": false, "bSortable": false },
                        { "bsearchable": false, "bSortable": false },
                        { "bsearchable": false, "bSortable": false },
                        { "bsearchable": false, "bSortable": false },	
                ],
                "dom" : '<tlip>',
                "iDisplayLength":25,
                "lengthMenu": [ 25, 50, 75, 100 ],
                "aaSorting": [],
                "fnDrawCallback": function( nRow, aData, iDataIndex ) {
                        // Bold the grade for all 'A' grade browsers
                            $('#test tr').removeClass('first');
                            $('#test tr').removeClass('last');
                            $('#test tr:first-child').addClass('first');
                            $('#test tr:last-child').addClass('last');
                        }
            });


        $("#test_paginate").on("click",function(){info = oTable.page.info(); $.session.set('page_number',info.page);});
        $("#test").on("click",function(){var order = oTable.order(); $.session.set('sort_column',order[0][0]); $.session.set('sort_order',order[0][1]);});

        function check_pagination()
        {
                var page_info = oTable.page.info();
                var page_length = page_info.pages;
                //if(parseInt($.session.get('page_number')) <= page_length)
                //	oTable.page(parseInt($.session.get('page_number'))).draw(false);
                if(parseInt($.session.get("sort_column")) && $.session.get("sort_order"))
                {
                        oTable
                                .order([parseInt($.session.get("sort_column")),$.session.get("sort_order")])
                                .draw(false);
                }
                if(parseInt($.session.get('page_number')) <= page_length)
                        oTable.page(parseInt($.session.get('page_number'))).draw(false);
        }

        /*
        * Programmer Name: AD
        * Purpose: To display confirmation dialog before deletion and passing the id to the confirm delete dialog.
        * Date: 07 Oct 2014
        * Dependency: admin.php
        */
        $("#test").on("click",".delete_button",function() {
            var id = this.id;

            // Assign the id to a custom attribute called data-id and language id
            $("#myModal").attr("data-id", id);
            $("#myModal").attr("aria-hidden",false);
            //alert($("#myModal").attr("lang_id"));

        });

        /*
        * Programmer Name: AD
        * Purpose: To redirect to the controller for deletion.
        * Date: 07 Oct 2014
        * Dependency: admin.php
        */
        $("#btn-danger1").click(function(){
            $.ajax({
                        url:'<?php echo base_url();?>admin/channel/channel_count',
                        type:"POST",
                        success:function(data){
                                 if(data>1)
                                     {
                                   $.ajax({
                                        url:'<?php echo base_url();?>admin/channel/check_having_category',
                                        type:"POST",
                                        data:{channel_id : $("#myModal").attr("data-id")},
                                        success:function(data){
                                            
                                        if(data==1)
                                            {
                                                var str = "<?php echo base_url().'admin/channel/delete_channel/'?>";
                                                var teststr = str;
                                                teststr = teststr.concat($("#myModal").attr("data-id"));
                                                //alert(teststr);
                                                window.location.href=teststr;
                                            }  
                                            else
                                            {
                                                $().toastmessage('showErrorToast', "You cannot delete this channel as its includes category/video/ads");
                                            }
                                        }
                                        });
                                       
                                     }
                                     else{
                                        $().toastmessage('showSuccessToast', "Atleast 1 record should be available."); 
                                     }
                               
                           }


                });   
        }); 


        $("#test").on("click",".status",function (){
                var id = this.id;
                $("#btn-status").attr("data-id", id);
                $("#btn-status").attr("data-status",$(this).attr("data-status"));
        });


        $("#btn-status").click(function(){
              var spanid= $("#btn-status").attr("data-id");
                var changeStatus = $("#btn-status").attr("data-status");
               
                $.ajax({
                        url:'<?php echo base_url();?>admin/channel/channel_count',
                        type:"POST",
                        success:function(data){
                                
                                if(changeStatus==0)
                                     {
                                         change_state();
                                     }
                                     else{
                                            if(data>1)
                                                {	
                                                   change_state();
                                                }
                                                else{
                                                   $().toastmessage('showSuccessToast', "Atleast 1 record should be active."); 
                                                }
                                     }    
                           }
                });  	

        });
        
        
        function change_state()
        {
           
            var spanid= $("#btn-status").attr("data-id");
                var changeStatus = $("#btn-status").attr("data-status");
                $.ajax({
                        url:'<?php echo base_url();?>admin/channel/update_channel_status',
                        type:"POST",
                        data:{"channel_id": $("#btn-status").attr("data-id") },
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
        }

        $("#filter").keypress(function(e){
                var keycode = (e.keyCode ? e.keyCode : e.which);
                if(keycode==13)
                {
                        if($('#filter').val() != '')
                        {
                                var test = $('#filter').val();

                                oTable
                                        .columns( 1 )
                                        .search( $('#filter').val() )
                                        .draw();
                                $.session.set("brand_search",test);

                        }
                        check_pagination();
                }
        });


        $('#show').click(function(){
                if($('#filter').val() != '')
                {
                        var test = $('#filter').val();

                        oTable
                                .columns( 1 )
                                .search( $('#filter').val() )
                                .draw();

                        $.session.set("brand_search",test);



                }
                 else {
                    $('#clear').click();
                }

                        check_pagination();
        });
        //alert($.session.get("is_table_status"));
        if($.session.get("addedit")==1)
        {
                if($.session.get("brand_search"))
                {
                        $("#filter").val($.session.get("brand_search"));
                        //$.session.set("addedit",0);
                        $.session.set("is_table_status",0);
                        $('#show').click();
                        //alert($.session.get("addedit"));
                }
                else
                {
                        $.session.set("addedit",0);
                }
                check_pagination();
        }
        else if($.session.get("is_table_status")==1)
        {
                //alert($.session.get("addedit"));
                $.session.set("is_table_status",0);
                $('#clear').click();
        }
        else
        {
                $.session.set("page_number",0);
                $.session.set("sort_column",0);
                $.session.set("sort_order","asc");
        }

        $('#clear').click( function ()
        {

                //alert($.session.get("addedit"));oTable.state.clear();
                $.session.remove('brand_search');
                $('#filter').val('');
                $.session.remove("sort_column");
                $.session.remove("sort_order");
                $.session.set("page_number",0);
                location.reload();
        });
});
</script>
<style>
            .first .moveup{
                display: none;
            }
            .last .movedown{
                display: none;
            }
        </style>
<div class="content">
            <div class="header">
                    <h1 class="page-title">Transection History</h1>
                            <ul class="breadcrumb">
                                    <li><a href="<?php echo base_url();?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
                                    <li class="active">Transection History</li>
                            </ul>
            </div>
            <div class="main-content">

                    <div id="custom_filter"> 
                            <input type="text" class="form-control" style="width:20%;display:inline;" id="filter" name="filter" placeholder="Search Channel" value="<?php echo set_value('filter');?>"/>
                            <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
                            <input type="button" class="btn btn-primary" id="clear" name="clear" style="margin-left:10px" value="Clear" />
                            <a href="<?php echo base_url();?>admin/channel/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Channel </a>
                    </div>

            <br/>
            
				<div class="table-responsive">
                    <table class="display hover cell-border table" id="test">
                        <thead>
                            <tr class="table_th_tr">
                                <th class="row-col_4">SEQ. NO.</th>
                                <th style="text-align:center;">USERNAME</th>
                                <th class="row-col_3">AMOUNT</th>
                                <th class="row-col_1">ACCOUNT STATUS</th>
                            <th class="row-col_1"> DATE</th>
                                <th class="row-col_1">TRANSACTION NUMBER</th>
                                <th class="row-col_1">VIEW</th>
                                <th class="row-col_1">DELETE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;?>
                            <?php 
                            if($transectionData)
                            foreach($transectionData as $rec):                                
                                ?>
                            <tr style="text-align:center;">
                                <td> <?php echo $i++;?></td>
                                <td style="text-align:left;"> <?php echo $rec['user_first_name']!="" ? $rec['user_first_name']." ".$rec['user_last_name']: "--"; ?></td>
                                 <td style="text-align:left;"> <?php echo $rec['amount']; ?></td>
                                 <td style="text-align:left;"> <?php echo $rec['account_status']; ?></td>
                                 <td style="text-align:left;"> <?php echo $rec['date']; ?></td>
                                 <td style="text-align:left;"> <?php echo $rec['transection_no']; ?></td>
                                 
                                <td><a href="<?php echo base_url().'admin/transaction/transection_view/'.$rec['transect_id'];?>" title=""><i class="fa fa-pencil-square-o fa-2x" ></i></a></td>
                                <td><a href="#myModal" class="delete_button" id="<?php echo $rec['transect_id'];?>" role="button" data-toggle="modal" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a></td>
                            </tr>
                            <?php endforeach ?>
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
                                            <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete?<br>This cannot be undone.</p>
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