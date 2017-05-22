<script type="text/javascript" >
    $(document).ready(function() {
<?php $arr = $this->session->userdata('menu');
?>
        $(".sidebar-nav #menu<?php echo $arr['Region Management'][1]; ?>").addClass("act");

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
         * Purpose: To redirect to the controller for deletion.
         * Date: 27 Oct 2014
         * Dependency: admin.php
         */
        $("#btn-danger1").click(function(){

            var str = "<?php echo $this->config->item("admin_url") . 'state/delete_regions/'; ?>";
            var teststr = str.concat($("#myModal").attr("data-id"));
            $.session.set("addedit",1);
            window.location.href=teststr;

        }); 

        $('.status').click(function(){
            var id = this.id;
            $("#btn-status").attr("data-id", id);
            $("#btn-status").attr("data-status",$(this).attr("data-status"));
        });
								
        $('.errorStatus').click(function(){
            $().toastmessage('showErrorToast', "Region status can not be changed because cities are associated with it");
        });
								
        $('.errorDelete').click(function(){
            $().toastmessage('showErrorToast', "Region can not be deleted because cities are associated with it");
        });

        $("#btn-status").click(function(){
            var spanid= $(this).attr("data-id");
            var changeStatus = $(this).attr("data-status");
            $.ajax({
                url:'<?php echo $this->config->item("admin_url"); ?>state/update_status',
                type:"POST",
                data:{"region_id": $(this).attr("data-id"),"changeStatus":changeStatus },
                success:function(res){
                    if(res == 0)
                    {
                        $().toastmessage('showErrorToast', "Region status can not be changed because cities are associated with it.");
                    }
                    else 
                    {    
																												
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

                }

            });
        }); 

        $("#filter_regions").keypress(function(e){
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode==13)
            {
                if($('#filter_regions').val() != '')
                {
                    $.session.set("regions_search",$('#filter_regions').val());
                    oTable
                    .columns( 1 )
                    .search( $('#filter_regions').val() )
                    .draw();
                }
            }
        });



        $('#show').click(function(){
            if($('#filter_regions').val() != '')
            {
                $.session.set("regions_search",$('#filter_regions').val());

                oTable
                .columns( 1 )
                .search( $('#filter_regions').val() )
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
            if($.session.get("regions_search"))
            {
                $("#filter_regions").val($.session.get("regions_search"));
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
            $.session.remove("regions_search");
            $('#filter_regions').val('');
            location.reload();
        });
                
        $(".row-col_4").removeClass("sorting_asc");
                
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
        <h1 class="page-title">Region Management</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->item("admin_url"); ?>dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">Regions</li>
        </ul>
    </div>
    <div class="main-content">


        <div id="custom_filter"> 
            <input type="text" class="form-control" style="width:15%;display:inline;" id="filter_regions" name="filter_regions" placeholder="Search Region" value=""/>
            <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
            <input type="button" class="btn btn-primary " id="clear" name="clear" style="margin-left:10px" value="Clear" />
            <div class="pull-right jx_addnew_btn_wrap">
                <a href="<?php echo $this->config->item("admin_url"); ?>state/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Region </a>
            </div>
        </div>

        <br/>
        <div class="table-responsive">
            <table class="display hover cell-border" id="test">
                <thead>
                    <tr class="table_th_tr">
                        <th class="row-col_4">SEQ. NO.</th>
                        <th class="row-col_2">REGION NAME</th>
                        <th class="row-col_2">STATUS</th>
                        <th class="row-col_4">EDIT</th>
                        <th class="row-col_4">DELETE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>

                    <?php
                    if ($regionsData) {
                        foreach ($regionsData as $rec):
                            ?>

                            <tr style="text-align:center;">

                                <td> <?php echo $i++; ?></td>

                                <td style="text-align:left;"> <?php echo htmlentities(stripslashes($rec['state_name'])); ?></td> 

                                <td> <a 

                                        <?php if ($rec['status'] > 0 && $rec['status'] == 1) { ?> 			class="errorStatus grey" 
                                        <?php } else { ?>
                                            data-toggle="modal" class="status"
                                        <?php } ?>
                                        href="#myStatus" role="button" id="<?php echo $rec['state_id']; ?>" data-status="<?php echo $rec['status']; ?>"><span id="status<?php echo $rec['state_id']; ?>" class="status_icon"><?php if ($rec['status'] == 0): ?><i class="fa fa-ban fa-2x" title="Inactive"></i><?php else: ?><i class="fa fa-check fa-2x" title="Active"></i><?php endif ?></span></a></td>                                         

                                <td><a href="<?php echo $this->config->item("admin_url") . 'state/addedit/' . $rec['state_id']; ?>" title="Edit "><i class="fa fa-pencil-square-o fa-2x" ></i></a></td>
                                <td>

                                    <a 

                                        <?php //if ($rec['city_count'] > 0) { ?> 			class="errorDelete grey" 
                                        <?php //} 
//                                        else { ?>
                                            data-toggle="modal" class="delete_button"
                                        //<?php //} ?>

                                        href="#myModal"  id="<?php echo $rec['state_id']; ?>" faq="button" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a></td>
                            </tr>

                            <?php
                        endforeach;
                    }
                    ?>

                </tbody>
            </table>
        </div>
        <div class="modal small fade" id="myModal" tabindex="-1" faq="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                        <h3 id="myModalLabel">Delete Confirmation</h3>
                    </div>
                    <div class="modal-body">
                        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete this region?<br>This cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
                        <button class="btn btn-primary" id="btn-danger1" data-dismiss="modal">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal small fade" id="myStatus" tabindex="-1" faq="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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