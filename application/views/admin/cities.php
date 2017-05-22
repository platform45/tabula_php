<script type="text/javascript" >
    $(document).ready(function() {
<?php $arr = $this->session->userdata('menu');
?>
        $(".sidebar-nav #menu<?php echo $arr['City'][1]; ?>").addClass("act");

        /*
         * Programmer Name: Akash Deshmukh
         * Purpose: DataTable initialisation.
         * Date: 02 Sept 2016
         * Dependency: admin.php
         */
        var oTable = $('#test').DataTable({
            "columns":[

                { "bSortable": false, "bSortable": false },
                { "bSortable": false, "bSortable": false },
                { "bSortable": false, "bSortable": false },
                { "bSortable": false, "bSortable": false },
                { "bSortable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": false }
            ],
            "ajax": {
                "url" : "<?php echo $this->config->item("admin_url");?>cities/city_listing",
                'type': 'POST',
                beforeSend: function( ) {
                   $("#loader").show();
                },
                complete: function( ) {
                    $("#loader").hide();
                    /* Bind Status button after getting response */
                    $('.status').click(function(){
                        var id = this.id;
                        $("#btn-status").attr("data-id", id);
                        $("#btn-status").attr("data-status",$(this).attr("data-status"));
                    });
                }
            },
            "dom" : '<tlip>',
            "iDisplayLength":25,
            "lengthMenu": [ 25, 50, 75, 100 ],
            "processing": true,
            "serverSide": true,
        });

        /*
         * Programmer Name: Akash Deshmukh
         * Purpose: To display confirmation dialog before deletion and passing the id to the confirm delete dialog.
         * Date: 02 Sept 2016
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

        $("#test_paginate").on("click",function(){info = oTable.page.info(); $.session.set('page_number',info.page);});
        $("#test").on("click",function(){var order = oTable.order(); $.session.set('sort_column',order[0][0]); $.session.set('sort_order',order[0][1]);});

           $("#country_id").change(function(){
           var country_id = $("#country_id").val();
            $.post("<?php echo $this->config->item("admin_url") . 'cities/get_region_by_country'; ?>",
            {country_id: country_id,isList:'N'},
            function(option_list){
                $("#region_id").html(option_list);
            });
        })

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
         * Purpose: To redirect to the controller for deletion.
         * Date: 02 Sept 2016
         * Dependency: admin.php
         */
        $("#btn-danger1").click(function(){

            var str = "<?php echo $this->config->item("admin_url") . 'cities/delete_cities/'; ?>";
            var teststr = str.concat($("#myModal").attr("data-id"));
            $.session.set("addedit",1);
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
                url:'<?php echo $this->config->item("admin_url"); ?>cities/update_status',
                type:"POST",
                data:{"city_id": $(this).attr("data-id"),"changeStatus":changeStatus },
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

        $("#filter_cities").keypress(function(e){
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode==13)
            {
                if($('#filter_cities').val() != '')
                {
                    $.session.set("cities_search",$('#filter_cities').val());
                    oTable
                    .columns( 1 )
                    .search( $('#filter_cities').val() )
                    .draw();
                }
            }
        });



        if($.session.get("addedit")==1)
        {
            if($.session.get("cities_search"))
            {
                $("#filter_cities").val($.session.get("cities_search"));
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
            $.session.remove("cities_search");
            $('#filter_cities').val('');
            location.reload();
        });

                  $("#country_id").keypress(function(e){
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode==13)
            {
                if($('#country_id').val() != '')
                {
                    $.session.set("countries_search",$('#country_id').val());
                    oTable
                    .columns( 3 )
                    .search( $('#country_id').val() )
                    .draw();
                }
            }
        });

        $('#show').click(function(){

           if($('#filter_cities').val() != '')
            {
                $.session.set("cities_search",$('#filter_cities').val());

                oTable
                .columns( 1 )
                .search( $('#filter_cities').val() )
                .draw();

            }

            if($('#region_id').val() != '')
            {
                $.session.set("regions_search",$('#region_id').val());

                oTable
                .columns( 2 )
                .search( $('#region_id').val() )
                .draw();

            }

            else if($('#country_id').val() != '')
            {
                $.session.set("countries_search",$('#country_id').val());

                oTable
                .columns( 3 )
                .search( $('#country_id').val() )
                .draw();

            }
        });

        if($.session.get("addedit")==1)
        {
            if($.session.get("regions_search"))
            {
                $("#country_id").val($.session.get("regions_search"));
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
            $.session.remove("countries_search");
            $('#country_id').val('');
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
        <h1 class="page-title">City Management</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->item("admin_url"); ?>dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">Cities</li>
        </ul>
    </div>
    <div class="main-content">
        <div id="custom_filter">

            <select class="form-control" name="country_id" id="country_id" aria-invalid="false" style="width:18%;display:inline;">
                                    <option value="" >Select Country</option>
                                    <?php foreach ($countriesData as $res) {
                                        ?>
                                        <option value="<?php echo $res['cou_id'];?>" data-country-id="<?php echo $res['cou_id'];?>"><?php echo htmlentities(stripslashes($res['cou_name'])); ?></option>
                                    <?php }
                                    ?>
                                </select>
           <select class="form-control" name="region_id" id="region_id" aria-invalid="false" style="width:18%;display:inline;" >
                                    <option value="" >Select State</option>
                                    <?php foreach ($regionsData as $res) {
                                        ?>
                                        <option value="<?php echo $res['region_name']; ?>" data-state-id="<?php echo $res['region_id']; ?>"><?php echo htmlentities(stripslashes($res['region_name'])); ?></option>
                                    <?php }
                                    ?>
                                </select>
                                <input type="text" class="form-control" style="width:18%;display:inline;" id="filter_cities" name="filter_cities" placeholder="Search City" value=""/>
            <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
            <input type="button" class="btn btn-primary " id="clear" name="clear" style="margin-left:10px" value="Clear" />
            <div class="pull-right jx_addnew_btn_wrap">
                <a href="<?php echo $this->config->item("admin_url"); ?>cities/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add City </a>
            </div>
        </div>
        <br/>
        <div class="table-responsive">
            <table class="display hover cell-border" id="test">
                <thead>
                    <tr class="table_th_tr">
                        <th class="row-col_4">SEQ. NO.</th>
                        <th class="row-col_2">CITY NAME</th>
                        <th class="row-col_2">STATE NAME</th>
                        <th class="row-col_2">COUNTRY NAME</th>
                        <th class="row-col_2">STATUS</th>
                        <th class="row-col_4">EDIT</th>
                        <th class="row-col_4">DELETE</th>
                    </tr>
                </thead>

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
                        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete this city?<br>This cannot be undone.</p>
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