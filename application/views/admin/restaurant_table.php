<script type="text/javascript" >
    $(document).ready(function() {
        $.session.set("currentDate", '');
<?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #menu<?php echo $arr['Table Management'][1]; ?>").addClass("act");

        var oTable = $('#restaurant_tables').DataTable({
            "columns":[
                { "bsearchable": false, "bSortable": false },
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

        // Delete button functioning
        $("#restaurant_tables").on("click",".delete_button",function() {
            var id = this.id;
            var lang_id = $(".delete_button").attr("lang_id");
            $("#myModal").attr("data-id", id);
            $("#myModal").attr("lang_id", lang_id);
            $("#myModal").attr("aria-hidden",false);
        });

        $("#btn-danger1").click(function() {
            var str = "<?php echo base_url() . 'admin/tables/delete_table/'; ?>";
            var teststr = str.concat( $("#myModal").attr("data-id") );
            window.location.href = teststr;
        });

        // Change status
        $('.status').click(function(){
            var id = this.id;
            $("#btn-status").attr("data-id", id);
            $("#btn-status").attr("data-status",$(this).attr("data-status"));
        });

        $("#btn-status").click(function(){
            var spanid= $(this).attr("data-id");
            var changeStatus = $(this).attr("data-status");
            $.ajax({
                url:'<?php echo base_url(); ?>admin/tables/update_table_status',
                type:"POST",
                data:{ "table_id": $(this).attr("data-id"), "change_status":changeStatus },
                success:function()
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
            });
        });

    });
</script>

<div class="content">

    <div class="header">
        <h1 class="page-title">Restaurant Tables</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">Tables</li>
        </ul>
    </div>

    <div class="main-content">

        <div id="custom_filter">
            <a href="<?php echo base_url(); ?>admin/tables/addedit" class="btn btn-primary pull-right" style="margin-bottom: 25px;">
                <i class="fa fa-plus"></i> Add Table
            </a>
        </div>

        <div class="table-responsive">
            <table class="display hover cell-border table" id="restaurant_tables">

                <thead>
                    <tr class="table_th_tr">
                        <th class="row-col_4">SEQ. NO.</th>
                        <th class="row-col_1">TABLE NAME</th>
                        <th class="row-col_1">TABLE CAPACITY</th>
                        <th class="row-col_1">STATUS</th>
                        <th class="row-col_1">EDIT</th>
                        <th class="row-col_1">DELETE</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $i = 1;
                    if ($restaurant_tables) {
                        foreach ($restaurant_tables as $table) {
                            ?>
                            <tr style="text-align:center;">
                                <td> <?php echo $i++; ?></td>
                                <td style="text-align:center;"> <?php echo $table->table_name; ?></td>
                                <td style="text-align:center;"> <?php echo $table->table_capacity; ?></td>
                                <td>
                                    <a title="Change status" href="#myStatus" role="button" data-toggle="modal" id="<?php echo $table->table_id; ?>" class="status" data-status="<?php echo $table->status; ?>">
                                        <span id="status<?php echo $table->table_id; ?>" class="status_icon">
                                            <?php if ($table->status == 0) { ?>
                                                <i class="fa fa-ban fa-2x" ></i>
                                            <?php } else { ?>
                                                <i class="fa fa-check fa-2x" ></i>
                                            <?php } ?>
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo base_url() . 'admin/tables/addedit/' . $table->table_id; ?>" title="Edit table">
                                        <i class="fa fa-pencil-square-o fa-2x" ></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="#myModal" class="delete_button" id="<?php echo $table->table_id; ?>" role="button" data-toggle="modal" title="Delete">
                                        <i class="fa fa-times-circle-o fa-2x"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
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