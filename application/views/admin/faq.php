<script type="text/javascript" >
    $(document).ready(function() {
<?php $arr = $this->session->userdata('menu');
?>
        $(".sidebar-nav #menu<?php echo $arr['FAQ'][1]; ?>").addClass("act");

        /*
         * Programmer Name: Akash Deshmukh
         * Purpose: DataTable initialisation.
         * Date: 14 Sept 2016
         * Dependency: admin.php
         */
        var oTable = $('#test').DataTable({
            "columns":[
                { "bSortable": false, "bSortable": false },
                { "bSortable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false }		
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
        /*
         * Programmer Name: Akash Deshmukh
         * Purpose: To display confirmation dialog before deletion and passing the id to the confirm delete dialog.
         * Date: 14 Sept 2016
         * Dependency: admin.php
         */
        $("#test").on("click",".delete_button",function() {
            var id = this.id;
            var lang_id = $(".delete_button").attr("lang_id");

            // Assign the id to a custom attribute called data-id and language id
            $("#myModal").attr("data-id", id);
            $("#myModal").attr("lang_id", lang_id);
            $("#myModal").attr("aria-hidden",false);

        });

        /*
         * Programmer Name: Akash Deshmukh
         * Purpose: To redirect to the controller for deletion.
         * Date: 14 Sept 2016
         * Dependency: admin.php
         */
        $("#btn-danger1").click(function(){

            var str = "<?php echo base_url() . 'admin/faq/delete_faq/'; ?>";
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
                url:'<?php echo base_url(); ?>admin/faq/update_status',
                type:"POST",
                data:{"faq_id": $(this).attr("data-id"),"changeStatus":changeStatus },
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

        $("#filter_faq").keypress(function(e){
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode==13)
            {
                if($('#filter_faq').val() != '')
                {
                    $.session.set("faq_search",$('#filter_faq').val());
                    oTable
                    .columns( 1 )
                    .search( $('#filter_faq').val() )
                    .draw();
                }
            }
        });

        $('#show').click(function(){
            if($('#filter_faq').val() != '')
            {
                $.session.set("faq_search",$('#filter_faq').val());

                oTable
                .columns( 1 )
                .search( $('#filter_faq').val() )
                .draw();

            }
            else
            {
                $('#clear').click();
            }	
        });

        if($.session.get("addedit")==1)
        {
            if($.session.get("faq_search"))
            {
                $("#filter_faq").val($.session.get("faq_search"));
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
            $.session.set("is_table_status",0);
            $('#clear').click();
        }

        $('#clear').click( function ()
        {
            oTable.state.clear();
            $.session.remove("faq_search");
            $('#filter').val('');
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
        <h1 class="page-title">FAQ</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">FAQ</li>
        </ul>
    </div>
    <div class="main-content">


        <div id="custom_filter"> 
            <input type="text" class="form-control" style="width:19%;display:inline;" id="filter_faq" name="filter_faq" placeholder="Search faq" value=""/>
            <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
            <input type="button" class="btn btn-primary " id="clear" name="clear" style="margin-left:10px" value="Clear" />
           <!--  <?php //if ($cnt != 3) { ?> -->
                <a href="<?php echo base_url(); ?>admin/faq/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Create FAQ </a>
         <!--    <?php// } ?> -->
        </div>

        <br/>
        <div class="table-responsive">
            <table class="display hover cell-border table" id="test">
                <thead>
                    <tr class="table_th_tr">
                        <th class="row-col_4">SEQ. NO.</th>
                        <th class="row-col_2">QUESTION</th>
                        <th class="row-col_4">SEQUENCE</th>
                        <th class="row-col_4">STATUS</th>
                        <th class="row-col_4">EDIT</th>
                        <th class="row-col_4">DELETE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>

                    <?php
                    if ($faqData) {
                        foreach ($faqData as $rec):
                            ?>
                            <tr style="text-align:center;">
                                <td> <?php echo $rec['faq_sequenceno']; ?></td>
                                <td style="text-align:left;"> <?php echo stripslashes($rec['faq_question']); ?></td>
                                <td>
                                    <a data-id="<?php echo $rec['faq_id']; ?>" style="margin-right:15px;" title="Move Up" class="moveup pull-right" id="mp_<?php echo $rec['faq_id']; ?>" href="<?php echo base_url() . 'admin/faq/change_sequence/up/' . $rec['faq_id']; ?>">
                                        <i class="fa  fa-arrow-circle-up fa-2x"></i>
                                    </a>
                                    <a data-id="<?php echo $rec['faq_id']; ?>" style="margin-right:15px;" title="Move Down" class="movedown pull-right" id="mp_<?php echo $rec['faq_id']; ?>" href="<?php echo base_url() . 'admin/faq/change_sequence/down/' . $rec['faq_id']; ?>">
                                        <i class="fa  fa-arrow-circle-down fa-2x"></i>
                                    </a>
                                </td>
                                <td> <a href="#myStatus" faq="button" data-toggle="modal" id="<?php echo $rec['faq_id']; ?>" title="Change Status" class="status" data-status="<?php echo $rec['status']; ?>"><span id="status<?php echo $rec['faq_id']; ?>" class="status_icon"><?php if ($rec['status'] == 0): ?><i class="fa fa-ban fa-2x" ></i><?php else: ?><i class="fa fa-check fa-2x" ></i><?php endif ?></span></a></td>
                                <td><a href="<?php echo base_url() . 'admin/faq/addedit/' . $rec['faq_id']; ?>" title="Edit Faq"><i class="fa fa-pencil-square-o fa-2x" ></i></a></td>
                                <td><a href="#myModal" class="delete_button" id="<?php echo $rec['faq_id']; ?>" faq="button" data-toggle="modal" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a></td>
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
                        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete faq?<br>This cannot be undone.</p>
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