<script type="text/javascript" src="<?php echo $this->config->item('assets_admin'); ?>js/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets_admin'); ?>js/extra_method.js" ></script>
<script type="text/javascript" src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
<script type="text/javascript" >
  $(document).ready(function() {
    <?php $arr = $this->session->userdata('menu');
    ?>
    $(".sidebar-nav #menu<?php echo $arr['Subscriber Management'][1]; ?>").addClass("act");


    $('#import_form').validate({ 
      rules:{
        userfile:{
          required:true,
          extension: "csv"

        }
      },
      messages:{

        userfile:{
          required:"Please select a file to upload.",
          extension:"Please select valid CSV file"  
        },
        errorPlacement: function(error, element) {
          error.appendTo($('#' + element.attr('id')).parent());
          if (element.attr("type") == "file")
            error.insertAfter('#userfile');


        }
      }
    });



    var oTable = $('#test').DataTable({
      "columns":[
      { "bsearchable": false, "bSortable": true },
      { "bsearchable": false, "bSortable": false },
      { "bsearchable": false, "bSortable": true },
      { "bsearchable": false, "bSortable": false },
      { "bsearchable": false, "bSortable": false }		
      ],

      "dom" : '<tlip>',
      "iDisplayLength":25,
      "lengthMenu": [ 25, 50, 75, 100 ]
    });






    $("#test").on("click",".delete_button",function() {
      var id = this.id;

            // Assign the id to a custom attribute called data-id and language id
            $("#myModal").attr("data-id", id);
            $("#myModal").attr("aria-hidden",false);

          });


    $("#btn-danger1").click(function(){

      var str = "<?php echo $this->config->item('admin_base_url') . 'subscriber/delete_subscriber/'; ?>";
      var teststr = str.concat($("#myModal").attr("data-id"));

      window.location.href=teststr;

    }); 

    $('#test').on("click",".status",function(){
      var id = this.id;
      $("#btn-status").attr("data-id", id);
      $("#btn-status").attr("data-status",$(this).attr("data-status"));
    });


    $('#test').on("click",".confirm",function(){

      if($(this).attr("href") == "javascript:void(0);")
      {
        $().toastmessage('showErrorToast', "Confirmed suscription cannot be disabled.");
      }
      else
      {
        var id = $(this).data("id");
        $("#btn-confirm").attr("data-id", id);
        $("#btn-confirm").attr("data-status",$(this).attr("data-status"));
      }
    });

    $("#btn-status").click(function(){
      var spanid= $(this).attr("data-id");
      var changeStatus = $(this).attr("data-status");
      $.ajax({
        url:'<?php echo $this->config->item('admin_base_url'); ?>subscriber/update_status',
        type:"POST",
        data:{"sub_id": $(this).attr("data-id"),"changeStatus":changeStatus },
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


    $("#btn-confirm").click(function(){
      var spanid= $(this).attr("data-id");
      var changeStatus = $(this).attr("data-status");

      $.ajax({
        url:'<?php echo$this->config->item('admin_base_url'); ?>subscriber/update_confirm_status',
        type:"POST",
        data:{"sub_id": $(this).attr("data-id"),"changeStatus":changeStatus },
        success:function(data){
          if(data == 1)
          {
            $("#confirm"+spanid+" i.fa-2x").removeClass("fa-ban");
            $("#confirm"+spanid+" i.fa-2x").addClass("fa-check");
            $("#confirm_status_"+spanid).attr("data-status",0);
            $("#confirm_status_"+spanid).attr("href","javascript:void(0);");

            $().toastmessage('showSuccessToast', "Status changed successfully.");
          }
          else if(data == 2)
          {
            $().toastmessage('showErrorToast', "Confirmed suscription cannot be disabled.");
          }

        }
      });
    }); 

    $("#filter_subscriber").keypress(function(e){
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode==13)
      {
        if($('#filter_subscriber').val() != '')
        {
          $.session.set("subscriber_search",$('#filter_subscriber').val());
          oTable
          .columns( 1 )
          .search( $('#filter_subscriber').val() )
          .draw();
        }
      }
    });



    $('#show').click(function(){
      if($('#filter_subscriber').val() != '')
      {
        $.session.set("subscriber_search",$('#filter_subscriber').val());

        oTable
        .columns( 1 )
        .search( $('#filter_subscriber').val() )
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
      if($.session.get("subscriber_search"))
      {
        $("#filter_subscriber").val($.session.get("subscriber_search"));
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
      $.session.remove("subscriber_search");
      $('#filter_subscriber').val('');
      location.reload();
    });

  });
</script>
<div class="content">
  <div class="header">
    <h1 class="page-title">Subscriber Management</h1>
    <ul class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
      <li class="active">Subscriber</li>
    </ul>
  </div>
  <div class="main-content">

    <div id="custom_filter"> 
      <input type="text" class="form-control" style="width:17%;display:inline;" id="filter_subscriber" name="filter_subscriber" placeholder="Subscriber search" value=""/>
      <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
      <input type="button" class="btn btn-primary " id="clear" name="clear" style="margin-left:10px" value="Clear" />
      <div>

        <div>
          <a style="margin-left:2px" href="<?php echo base_url(); ?>admin/subscriber/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Subscriber </a>

          <a href="<?php echo $this->config->item('admin_base_url'); ?>subscriber/ExportCSV" class="btn btn-primary pull-right"> Export Subscriber </a>
          <form method="post" id="import_form" action="<?php echo base_url(); ?>admin/subscriber/importcsv" enctype="multipart/form-data">
           <input type="submit" name="submit" id="submitbutton1" value="Import" class="btn btn-primary pull-right" style="margin-right: 2px;">
           <input type="file" name="userfile" id="userfile" class=" pull-right">
           <a href="<?php echo $this->config->item('admin_base_url'); ?>subscriber/downloadSample" class="pull-right" style="margin-right:5px;"> Sample file </a>
           <div class="pull-right"><p>
           <a href="#info" data-toggle="modal" class="glyphicon glyphicon-info-sign" style="margin-right:8px;"></a></p>
          </div>
          <label id="userfile-error" class="error" style="margin-left:492px;" for="userfile"></label>

        </form>
        <!--  <a href="<?php echo $this->config->item('admin_base_url'); ?>subscriber/" class=""> Sample file </a> -->
      </div>
    </div>
  </div>

  <br/>
  <div class="table-responsive">
    <table class="display hover cell-border table" id="test">
      <thead>
        <tr class="table_th_tr">
          <th class="row-col_1">SEQUENCE</th>
          <th class="row-col_2">SUBSCRIBER EMAIL</th>
          <th class="row-col_1">SUBSCRIPTION STATUS</th>
          <th class="row-col_1">STATUS</th>
          <th class="row-col_1" >DELETE</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>

        <?php
        if ($subscriberData) {
          foreach ($subscriberData as $rec):
            ?>
          <tr style="text-align:center;">
            <td> <?php echo $i++; ?></td>
            <td style="text-align:left;"> <?php echo $rec['sub_email']; ?></td>
            <td> <span style="color: #840f2e"><?php if ($rec['sub_unsub_status'] == 0): ?><i class="fa fa-ban fa-2x" ></i><?php else: ?><i class="fa fa-check fa-2x" ></i><?php endif ?></span></td>
            <td> <a href="#myStatus" title="Change Status" role="button" data-toggle="modal" id="<?php echo $rec['sub_id']; ?>" class="status" data-status="<?php echo $rec['sub_status']; ?>"><span id="status<?php echo $rec['sub_id']; ?>" class="status_icon"><?php if ($rec['sub_status'] == 0): ?><i class="fa fa-ban fa-2x" ></i><?php else: ?><i class="fa fa-check fa-2x" ></i><?php endif ?></span></a></td>
            <td><a href="#myModal" class="delete_button" id="<?php echo $rec['sub_id']; ?>" role="button" data-toggle="modal" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a></td>
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
          <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete this subscriber? <br/> Note: This cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
          <button class="btn btn-primary" id="btn-danger1" data-dismiss="modal">Confirm</button>
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

  <div class="modal small fade" id="confirmStatus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
          <h3 id="myModalLabel">Confirm subscriber</h3>
        </div>
        <div class="modal-body">
          <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to confirm the subscriber?<br/>This cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
          <button class="btn btn-primary" id="btn-confirm" data-dismiss="modal">Confirm</button>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

 <div class="modal small fade" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
          <h3 id="myModalLabel">Import Info.</h3>
        </div>
        <div class="modal-body">
         <p><b>Please follow the below steps to import the multiple email addresses:</b><br>
              1. Please download sample file<br>
              2. Add the multiple email addresses<br>
              3. Browse the updated file<br>
              4. Click on Import button<br>
          That's it and you will get the success message. </p><br><br>

          <p><b>Below are the reasons of invalid records : </b><br>
          - Email field is blank <br>
          - Invalid email format<br>
          - Duplicate email address <br>
          - CSV format is not proper, please check sample csv.</p><br>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" id="btn-confirm" data-dismiss="modal">Ok</button>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
