<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript" >
    $(document).ready(function() {
<?php $arr = $this->session->userdata('menu');?>
        $(".sidebar-nav #menu<?php echo $arr['Email Templates'][1]?>").addClass("act");

 var oTable = $('#test').DataTable({
        "columns": [
            null, {
                "bsearchable": false,
                "bSortable": false
            }, {
                "bsearchable": false,
                "bSortable": false
            }, {
            
                "bsearchable": false,
                "bSortable": false
            }, {
                "bsearchable": false,
                "bSortable": false
            }, 
        ],

        "dom": '<tlip>',
        "iDisplayLength": 25,
        "lengthMenu": [25, 50, 75, 100]
    });

    $("#filter_to_email,#filter_from_email,#filter_email_title").keypress(function(e)
    {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == 13) {
            $('#show').click();
        }
    });
    
    $('#show').click(function()
    {
        $.session.set("id_num_search", $('#filter_from_email').val());
        $.session.set("name_search", $('#filter_to_email').val());
        $.session.set("city_search", $('#filter_email_title').val());

        oTable
            .columns(2)
            .search($.trim($('#filter_from_email').val()))
            .draw();
        oTable
            .columns(3)
            .search($.trim($('#filter_to_email').val()))
            .draw();
        oTable
            .columns(1)
            .search($.trim($('#filter_email_title').val()))
            .draw();
    });

    if ($.session.get("addedit") == 1)
    {
        if ($.session.get("id_num_search") || $.session.get("name_search") || $.session.get("city_search"))
        {
            $("#filter_from_email").val($.session.get("id_num_search"));
            $("#filter_to_email").val($.session.get("name_search"));
            $("#filter_email_title").val($.session.get("city_search"));
            $.session.set("is_table_status", 0);
            $('#show').click();
        }
        else
        {
            $.session.set("addedit", 0);
        }
    } else if ($.session.get("is_table_status") == 1)
    {
        $.session.set("is_table_status", 0);
        $('#clear').click();
    }

    $('#clear').click(function() {
        oTable.state.clear();
        $.session.remove("id_num_search");
        $.session.remove("name_search");
        $.session.remove("city_search");
        $('#filter_to_email').val('');
        $('#filter_email_title').val('');
        $('#id_num_search').val('');
        window.location.href = "<?php echo $this->config->item('admin_base_url').'emailtemplate'; ?>";
    });
    $("#selSector").change(function() {
        $("#filter_email_title").val($("#selSector option:selected").text());
    });
    $('#show').click();
    $(".firstCol").removeClass("sorting_asc");
                
});
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title">Email Template Management</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->item('admin_base_url'); ?>dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">Email Template Management</li>
        </ul>
    </div>
    <div class="main-content">

        <div id="custom_filter"> 
            <input type="text" class="form-control" style="width:22%;display:inline;" id="filter_email_title" name="filter_email_title" placeholder="Search by Email Title" value=""/>
            <input type="text" class="form-control" style="width:22%;display:inline;" id="filter_from_email" name="filter_from_email" placeholder="Search by From Email" value=""/>

            <input type="hidden" id="filter_email_title" name="filter_email_title" value=""/>
            <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
            <input type="button" class="btn btn-primary " id="clear" name="clear" style="margin-left:10px" value="Clear" />
        </div>

        <br/>
        <div class="table-responsive">

            <table class="display hover cell-border table" id="test">
                <thead>
                    <tr class="table_th_tr">
                        <th class="row-col_1 firstCol">SEQ</th>
                        <th class="row-col_2">Email Title</th>
                        <th class="row-col_2">From Email</th>
                        <th class="row-col_2">Email Subject</th>
                        <th class="row-col_1">EDIT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>

                    <?php
                    if ($aEmailTList) {
                        foreach ($aEmailTList as $aSingleRow):
                            ?>
                            <tr style="text-align:center;">
                                <td> <?php echo $i++; ?></td>
                                <td style="text-align:left;"><?php echo (empty($aSingleRow['email_name'])) ? "-" : $aSingleRow['email_name']; ?></td>
                                <td style="text-align:center;"><?php echo (empty($aSingleRow['email_from'])) ? "-" : $aSingleRow['email_from']; ?></td>
                                <td style="text-align:center;"><?php echo (empty($aSingleRow['email_subject'])) ? "-" : $aSingleRow['email_subject']; ?></td>
                                <td>
                                    <a href="<?php echo $this->config->item('admin_base_url').'emailtemplate/addedit/'.$aSingleRow['email_id']; ?>" title="Edit">
                                        <i class="fa fa-pencil-square-o fa-2x" ></i>
                                    </a>
                                </td>
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
                        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete Email Template?<br>This cannot be undone.</p>
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
