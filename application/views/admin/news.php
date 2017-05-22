<script type="text/javascript" >
    $(document).ready(function() {
<?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #menu<?php echo $arr['News'][1]; ?>").addClass("act");

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

        });

        /*
         * Programmer Name: AD
         * Purpose: To redirect to the controller for deletion.
         * Date: 07 Oct 2014
         * Dependency: admin.php
         */
        $("#btn-danger1").click(function(){
            $.ajax({
                url:'<?php echo base_url(); ?>admin/news/news_count',
                type:"POST",
                success:function(data){
                    if(data>1)
                    {
                        var str = "<?php echo base_url() . 'admin/news/delete_news/' ?>";
                        var teststr = str;
                        teststr = teststr.concat($("#myModal").attr("data-id"));
                        window.location.href=teststr;
                    }
                    else{
                        $().toastmessage('showSuccessToast', "Atleast 1 record should be available"); 
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
                url:'<?php echo base_url(); ?>admin/news/news_state_count',
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
                            $().toastmessage('showSuccessToast', "Atleast 1 record should be active"); 
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
                url:'<?php echo base_url(); ?>admin/news/update_news_status',
                type:"POST",
                data:{"news_id": $("#btn-status").attr("data-id") },
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
                    $().toastmessage('showSuccessToast', "Status changed successfully");
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
                    $.session.set("slider_search",test);

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

                $.session.set("slider_search",test);

            }
            else {
                $('#clear').click();
            }

            check_pagination();
        });
        if($.session.get("addedit")==1)
        {
            if($.session.get("slider_search"))
            {
                $("#filter").val($.session.get("slider_search"));
                $.session.set("is_table_status",0);
                $('#show').click();
            }
            else
            {
                $.session.set("addedit",0);
            }
            check_pagination();
        }
        else if($.session.get("is_table_status")==1)
        {
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
            $.session.remove('slider_search');
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
        <h1 class="page-title">News</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">News</li>
        </ul>
    </div>
    <div class="main-content">

        <div id="custom_filter"> 
            <input type="text" class="form-control" style="width:20%;display:inline;" id="filter" name="filter" placeholder="Search news" value="<?php echo set_value('filter'); ?>"/>
            <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
            <input type="button" class="btn btn-primary" id="clear" name="clear" style="margin-left:10px" value="Clear" />
            <a href="<?php echo base_url(); ?>admin/news/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add News </a>
        </div>

        <br/>

        <div class="table-responsive">
            <table class="display hover cell-border table" id="test">
                <thead>
                    <tr class="table_th_tr">
                        <th class="row-col_4">SEQ. NO.</th>
                        <th style="text-align:center;">TITLE</th>
                        <th class="row-col_3">IMAGE</th>
                        <th class="row-col_1">DATE</th>
                        <th class="row-col_1">STATUS</th>
                        <th class="row-col_1">EDIT</th>
                        <th class="row-col_1">DELETE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php
                    if ($sliderData)
                        foreach ($sliderData as $rec):
                            $path = $rec['news_image'];
                            $ext = pathinfo($path, PATHINFO_EXTENSION);
                            $fileName = $path;
                            $thumb_img = $fileName . "." . $ext;
                            ?>
                            <tr style="text-align:center;">
                                <td> <?php echo $i++; ?></td>
                                <td style="text-align:left;"> <?php echo $rec['news_title']; ?></td>
                                <td> <img width=100  id="preview" name="preview" src="<?php if ($rec['news_image']) echo $this->config->item('assets') . 'upload/news/' . $path; else echo $this->config->item('assets') . 'upload/No_Image_thumb.jpg'; ?>"/> </td>
                                <td style="text-align:left;"> <?php
                    $newDate = date("d-m-Y", strtotime($rec['news_date']));
                    echo $newDate;
                            ?></td>
                                <!-- <td>
                                    <a data-id="<?php echo $rec['news_id']; ?>" style="margin-right:15px;" title="Move Up" class="moveup pull-right" id="mp_<?php echo $rec['news_id']; ?>" href="<?php echo base_url() . 'admin/news/change_sequence/up/' . $rec['news_id']; ?>">
                                        <i class="fa  fa-arrow-circle-up fa-2x"></i>
                                    </a>
                                    <a data-id="<?php echo $rec['news_id']; ?>" style="margin-right:15px;" title="Move Down" class="movedown pull-right" id="mp_<?php echo $rec['news_id']; ?>" href="<?php echo base_url() . 'admin/news/change_sequence/down/' . $rec['news_id']; ?>">
                                        <i class="fa  fa-arrow-circle-down fa-2x"></i>
                                    </a>
                                </td>  -->  
                                <td> <a title="Change Status" href="#myStatus" role="button" data-toggle="modal" id="<?php echo $rec['news_id']; ?>" class="status" data-status="<?php echo $rec['news_status']; ?>"><span id="status<?php echo $rec['news_id']; ?>" class="status_icon"><?php if ($rec['news_status'] == 0): ?><i class="fa fa-ban fa-2x" ></i><?php else: ?><i class="fa fa-check fa-2x" ></i><?php endif ?></span></a></td>
                                <td><a href="<?php echo base_url() . 'admin/news/addedit/' . $rec['news_id']; ?>" title="Edit News"><i class="fa fa-pencil-square-o fa-2x" ></i></a></td>
                                <td><a href="#myModal" class="delete_button" id="<?php echo $rec['news_id']; ?>" role="button" data-toggle="modal" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a></td>
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