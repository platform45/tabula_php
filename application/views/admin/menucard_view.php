<script type="text/javascript" >
    $(document).ready(function() {
<?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #menu<?php echo $arr['Restaurant'][1]; ?>").addClass("act");

        /*
         * Programmer Name: Akash Deshmukh
         * Purpose: DataTable initialisation.
         * Date: 02 Sept 2016
         * Dependency: Menucard.php
         */
        var oTable = $('#test').DataTable({
            "columns":[
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false }		
            ],
            "dom" : '<tlip>',
            "iDisplayLength":25,
            "lengthMenu": [ 25, 50, 75, 100 ],
            "aaSorting": [],
            "fnDrawCallback": function( nRow, aData, iDataIndex ) {
                $('#test tr').removeClass('first');
                $('#test tr').removeClass('last');
                $('#test tr:first-child').addClass('first');
                $('#test tr:last-child').addClass('last');
            }
        });
        $("#test_paginate").on("click",function(){info = oTable.page.info(); $.session.set('page_number',info.page);});

        function check_pagination()
        {
            var page_info = oTable.page.info();
            var page_length = page_info.pages;
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
         * Programmer Name: Akash Deshmukh
         * Purpose: To display confirmation dialog before deletion and passing the id to the confirm delete dialog.
         * Date: 02 Sept 2016
         * Dependency: admin.php
         */
        $("#test").on("click",".delete_button",function() {
            var id = this.id;

            // Assign the id to a custom attribute called data-id and language id
            $("#myModal").attr("data-id", id);
            $("#myModal").attr("aria-hidden",false);
        });

        /*
         * Programmer Name: Akash Deshmukh
         * Purpose: To redirect to the controller for deletion.
         * Date: 02 Sept 2016
         * Dependency: Menucard.php
         */
        $("#btn-danger1").click(function(){
            $.ajax({
                url:'<?php echo base_url() . "admin/menucard/slider_count/" ?>',
                type:"POST",
                success:function(data){
                    if(data>1)
                    {
                        var str = "<?php echo base_url() . 'admin/menucard/delete_slider/' ?>";
                        var teststr = str;
                        teststr = teststr.concat($("#myModal").attr("data-id"));
                        window.location.href=teststr;
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
                url:'<?php echo base_url() . "admin/menucard/slider_state_count/" ?>',
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
                url:'<?php echo base_url() . "admin/menucard/update_slider_status/" ?>',
                type:"POST",
                data:{"fm_id": $("#btn-status").attr("data-id") },
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
        <h1 class="page-title">Menucard</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/restaurant"> Restaurant</a> </li>
            <li class="active">Menucard</li>
        </ul>
    </div>
    <div class="main-content">
    <div id="custom_filter"> 

            <a href="<?php echo base_url() . 'admin/restaurant/' ?>" class="btn btn-primary pull-right"> Back </a><br><br>
        </div>
        <br/>


        <div class="table-responsive">
            <table class="display hover cell-border table" id="test">
                <thead>
                    <tr class="table_th_tr">
                        <th class="row-col_4">SEQ. NO.</th>
                        <th class="row-col_3">IMAGE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php
                    if ($sliderData)
                        foreach ($sliderData as $rec):
                            $path = $rec['fm_image'];
                            $ext = pathinfo($path, PATHINFO_EXTENSION);
                            $fileName = basename($path, "." . $ext);
                            $thumb_img = $fileName . "." . $ext;
                            ?>
                            <tr style="text-align:center; ">
                                <td> <?php echo $i++; ?></td>
                                <td> <img height="100px" width="auto"  id="preview" name="preview" src="<?php if ($rec['fm_image']) echo $this->config->item('assets') . 'upload/foodmenu/' . $thumb_img; else echo $this->config->item('assets') . 'upload/No_Image_thumb.jpg'; ?>"/> </td>
                 
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
                        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete this record?<br>This cannot be undone.</p>
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