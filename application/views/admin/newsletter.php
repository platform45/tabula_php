<script type="text/javascript" >
    $(document).ready(function() {
<?php $arr = $this->session->userdata('menu');
?>
        $(".sidebar-nav #menu<?php echo $arr['Newsletter Management'][1]; ?>").addClass("act");

        /*
         * Programmer Name: AD
         * Purpose: DataTable initialisation.
         * Date: 27 Oct 2014
         * Dependency: admin.php
         */
        var oTable = $('#test').DataTable({
            "columns":[
                { "bsearchable": false, "bSortable": true },
                { "bsearchable": false, "bSortable": false },
                { "bsearchable": false, "bSortable": true },
                { "bsearchable": false, "bSortable": true },
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
                    
            // Assign the id to a custom attribute called data-id and language id
            $("#myModal").attr("data-id", id);
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

            var str = "<?php echo base_url() . 'admin/newsletter/delete_newsletter/'; ?>";
            var teststr = str.concat($("#myModal").attr("data-id"));

            window.location.href=teststr;
        }); 

                
                
        $("#filter").keypress(function(e){
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode==13)
            {
                if($('#filter').val() != '')
                {
                    $.session.set("search",$('#filter').val());
                    oTable
                    .columns( 1 )
                    .search( $('#filter').val() )
                    .draw();
                }
            }
        });



        $('#show').click(function(){
            if($('#filter').val() != '')
            {
                $.session.set("search",$('#filter').val());

                oTable
                .columns( 1 )
                .search( $('#filter').val() )
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
            if($.session.get("search"))
            {
                $("#filter").val($.session.get("search"));
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
            $.session.remove("search");
            $('#filter').val('');
            location.reload();
        });
    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title">Newsletter Management</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">Newsletter</li>
        </ul>
    </div>
    <div class="main-content">


        <div id="custom_filter"> 
            <input type="text" class="form-control" style="width:17%;display:inline;" id="filter" name="filter" placeholder="Search newsletter" value=""/>
            <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
            <input type="button" class="btn btn-primary " id="clear" name="clear" style="margin-left:10px" value="Clear" />
            <a href="<?php echo base_url(); ?>admin/newsletter/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Create Newsletter </a>
        </div>

        <br/>
        <div class="table-responsive">
            <table class="display hover cell-border table" id="test">
                <thead>
                    <tr class="table_th_tr">
                        <th class="row-col_1">SEQUENCE</th>
                        <th class="row-col_2">NEWSLETTER TITLE</th>
                        <th class="row-col_3">PUBLISHED DATE</th>
                        <th class="row-col_3">SENT DATE</th>
                        <th class="row-col_1">EDIT</th>
                        <th class="row-col_1" >DELETE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>

                    <?php
                    if ($newsletterData) {
                        foreach ($newsletterData as $rec):
                            ?>
                            <tr style="text-align:center;">
                                <td> <?php echo $i++; ?></td>
                                <td style="text-align:left;"> <?php echo $rec['newsletter_title']; ?></td>
                                <td> <?php echo date("d F Y", strtotime($rec['newsletter_submitted_date'])); ?></td>
                                <td> <?php echo ($rec['newsletter_send_date'] != "" && $rec['newsletter_send_date'] != "0000-00-00") ? date("d F Y", strtotime($rec['newsletter_send_date'])) : "-"; ?></td>
                                <td><a href="<?php echo base_url() . 'admin/newsletter/addedit/' . $rec['newsletter_id']; ?>" title="Edit"><i class="fa fa-pencil-square-o fa-2x" ></i></a></td>
                                <td><a href="#myModal" class="delete_button" id="<?php echo $rec['newsletter_id']; ?>" role="button" data-toggle="modal" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a></td>
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
                        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete newletter?<br/>This cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
                        <button class="btn btn-primary" id="btn-danger1" data-dismiss="modal">Delete</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>