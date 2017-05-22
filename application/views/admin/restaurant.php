<script type="text/javascript" >

  $(document).ready(function() {

    <?php $arr = $this->session->userdata('menu');

    ?>

    $(".sidebar-nav #menu<?php echo $arr['Restaurant'][1]; ?>").addClass("act");



        /*

         * Programmer Name: Akash Deshmukh

         * Purpose: DataTable initialisation.

         * Date: 02 Sept 2016

         * Dependency: members.php

         */

         var oTable = $('#test').DataTable({

          "columns":[

          null,         

          { "bsearchable": false, "bSortable": false },

          { "bsearchable": false, "bSortable": false },

          { "bsearchable": false, "bSortable": false },

          { "bsearchable": false, "bSortable": false },

          { "bsearchable": false, "bSortable": false },

          { "bsearchable": false, "bSortable": false },

          { "bsearchable": false, "bSortable": false },

          { "bsearchable": false, "bSortable": false }

          //{ "bsearchable": false, "bSortable": false }		

          ],



          "dom" : '<tlip>',

          "iDisplayLength":25,

          "lengthMenu": [ 25, 50, 75, 100 ]

        });

         

         $("#test").on("click",".delete_button",function() {

          var id = this.id;

          var lang_id = $(".delete_button").attr("lang_id");

          $("#myModal").attr("data-id", id);

          $("#myModal").attr("lang_id", lang_id);

          $("#myModal").attr("aria-hidden",false);

        });



         $("#btn-danger1").click(function(){



          var str = "<?php echo base_url() . 'admin/restaurant/delete_members/'; ?>";

          var teststr = str.concat($("#myModal").attr("data-id"));



          window.location.href=teststr;

        }); 



         $('.errorDelete').click(function(){

           

          $().toastmessage('showErrorToast', "Restaurant cannot be deleted because it is in the top 10 list.");

          setTimeout(function(){ location.reload(true); }, 400);

          

        });



         $(document).on("click",".status", function(){

          var id = this.id;

          $("#btn-status").attr("data-id", id);

          $("#btn-status").attr("data-status",$(this).attr("data-status"));

        });



         $( "#btn-status" ).bind( "click", function() {

          var spanid= $(this).attr("data-id");

          var changeStatus = $(this).attr("data-status");

          $.ajax({

            url:'<?php echo base_url(); ?>admin/restaurant/update_members_status',

            type:"POST",

            data:{"user_id": $(this).attr("data-id"),"changeStatus":changeStatus },

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



         $(document).on("click",".add", function(){

          var id = this.id;

          var status = $(this).attr("data-add_status");  

          $("#btn-add").attr("data-id", id);

          $("#btn-add").attr("data-id", id);

          $("#btn-add").attr("data-add_status",status);

        });



         $( "#btn-add" ).bind( "click", function() {

          var spanid= $(this).attr("data-id");            

          var changeAdd = $(this).attr("data-add_status");

          

          if(changeAdd == 1)

          {

           

            $.ajax({

              url:'<?php echo base_url(); ?>admin/restaurant/insert_add',

              type:"POST",

              data:{"user_id": $(this).attr("data-id") },

              success:function(){

               

                

                $("#check_"+spanid).removeClass("fa-ban");

                $("#check_"+spanid).addClass("fa-check");

                $("#"+spanid).attr("data-add_status",0);                    

                $().toastmessage('showSuccessToast', "Add added successfully.");

              }

            }); 

          }

          else

          {

           

            $.ajax({

              url:'<?php echo base_url(); ?>admin/restaurant/delete_add',

              type:"POST",

              data:{"user_id": $(this).attr("data-id"),"changeAdd":changeAdd },

              success:function()

              {

                $("#check_"+spanid).removeClass("fa-check");

                $("#check_"+spanid).addClass("fa-ban");

                $("#"+spanid).attr("data-add_status",1);                   

                $().toastmessage('showSuccessToast', "Add removed successfully.");

              }

            }); 

          }

          

        }); 



$("#test").on("click",".top10",function (){

  var id = this.id;

  var status = $(this).attr("data-top_10"); 

  $("#btn-top10").attr("data-id", id);

  $("#btn-top10").attr("data-id", id);

  $("#btn-top10").attr("data-top_10",status);

});



$("#btn-top10").click(function(){ 

  var spanid= $("#btn-top10").attr("data-id");

  var changetop10 = $("#btn-top10").attr("data-top_10");

  $.ajax({

    url:'<?php echo base_url(); ?>admin/restaurant/toggle_top_10',

    data:{"user_id": spanid, 'flag' : changetop10 },

    type:"POST",

    success:function(data){

      if( data == 0 && changetop10 == 1 )

      {

        $().toastmessage('showErrorToast', "You can not mark more than 10 records.");

        setTimeout(function(){ location.reload(true); }, 400);

      }

      else if( data == 1 )

      {

        if( changetop10 == 0 )

        {   

          $("#check_"+spanid).removeClass("glyphicon-thumbs-up");

          $("#check_"+spanid).addClass("glyphicon-thumbs-down");

          $("#"+spanid).attr("data-top_10",1);                   

          $().toastmessage('showSuccessToast', "Removed from top 10 list successfully.");

          setTimeout(function(){ location.reload(true); }, 400);

        }

        else if( changetop10 == 1 )

        {

          $("#check_"+spanid).removeClass("glyphicon-thumbs-down");

          $("#check_"+spanid).addClass("glyphicon-thumbs-up");

          $("#"+spanid).attr("data-top_10",0);                    

          $().toastmessage('showSuccessToast', "Added to top 10 list successfully.");

          setTimeout(function(){ location.reload(true); }, 400);

        }

      }     

      

    }





  });

});







$("#filter_role").change(function(e){

  

  if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')

  {

    $.session.set("members_email",$('#filter_email').val());

    $.session.set("members_search",$('#filter_members').val());

    $.session.set("members_role",$('#filter_role').val());

    oTable

    .columns( 1)

    .search( $('#filter_members').val() )

    .draw();

    oTable

    .columns( 2 )

    .search( $('#filter_email').val() )

    .draw();

    oTable

    .columns( 3 )

    .search( $('#filter_role').val() )

    .draw();

  }

  else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')

  {

    $.session.set("members_search",$('#filter_members').val());

    $.session.set("role_search",$('#filter_role').val());



    oTable

    .columns( 1 )

    .search( $('#filter_members').val() )

    .draw();

    oTable

    .columns( 3 )

    .search( $('#filter_role').val() )

    .draw();

  }

  

  else if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')

  {                     

    ;                                            $.session.set("members_search",$('#filter_members').val());

    $.session.set("members_role",$('#filter_role').val());



    oTable

    .columns( 1 )

    .search( $('#filter_members').val() )

    .draw();

    oTable

    .columns( 2 )

    .search( $('#filter_email').val() )

    .draw();

  }

  

  else if($('#filter_email').val() != '' && $('#filter_members').val() == '' )

  {                      

    $.session.set("members_email",$('#filter_email').val());



    oTable

    .columns( 2 )

    .search( $('#filter_email').val() )

    .draw();

  }

  else if($('#filter_email').val() == '' && $('#filter_members').val() != '' )

  {                     

    $.session.set("members_email",$('#filter_email').val());



    oTable

    .columns( 1 )

    .search( $('#filter_members').val() )

    .draw();

  }

  else if($('#filter_email').val() == '' && $('#filter_members').val() == '' )

  {                      

    $.session.set("members_role");



    

  }

  else if($('#filter_email').val() != '' && $('#filter_members').val() == '' )

  {                     

    $.session.set("members_role",$('#filter_role').val());                                            

    $.session.set("members_email",$('#filter_email').val());

    

    oTable

    .columns( 2 )

    .search( $('#filter_email').val() )

    .draw();



    

  }

  else

  {

    $("#clear").click();

  }              

});



$("#filter_email").keypress(function(e){

  var keycode = (e.keyCode ? e.keyCode : e.which);

  if(keycode==13)

  {

    if($('#filter_email').val() != '' && $('#filter_members').val() != '' )

    {

      $.session.set("members_email",$('#filter_email').val());

      $.session.set("members_search",$('#filter_members').val());

      $.session.set("members_role",$('#filter_role').val());

      oTable

      .columns( 1)

      .search( $('#filter_members').val() )

      .draw();

      oTable

      .columns( 2 )

      .search( $('#filter_email').val() )

      .draw();

      

    }

    else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')

    {

      $.session.set("members_search",$('#filter_members').val());

      $.session.set("role_search",$('#filter_role').val());



      oTable

      .columns( 1 )

      .search( $('#filter_members').val() )

      .draw();

      

    }

    

    else if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')

    {                  

      ;                                            $.session.set("members_search",$('#filter_members').val());

      



      oTable

      .columns( 1 )

      .search( $('#filter_members').val() )

      .draw();

      oTable

      .columns( 2 )

      .search( $('#filter_email').val() )

      .draw();

    }

    

    else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() == '')

    {                    

      $.session.set("members_email",$('#filter_email').val());



      oTable

      .columns( 2 )

      .search( $('#filter_email').val() )

      .draw();

    }

    else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')

    {                   

      $.session.set("members_email",$('#filter_email').val());



      oTable

      .columns( 1 )

      .search( $('#filter_members').val() )

      .draw();

    }

    else if($('#filter_email').val() == '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')

    {                     

      $.session.set("members_role",$('#filter_role').val());

      

    }

    else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')

    {                                                              

      $.session.set("members_email",$('#filter_email').val());

      

      oTable

      .columns( 2 )

      .search( $('#filter_email').val() )

      .draw();



    }

    else

    {

      $("#clear").click();

    }

  }

});



$("#filter_members").keypress(function(e){

  var keycode = (e.keyCode ? e.keyCode : e.which);

  if(keycode==13)

  {

    if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')

    {

      $.session.set("members_email",$('#filter_email').val());

      $.session.set("members_search",$('#filter_members').val());

      $.session.set("members_role",$('#filter_role').val());

      oTable

      .columns( 1)

      .search( $('#filter_members').val() )

      .draw();

      oTable

      .columns( 2 )

      .search( $('#filter_email').val() )

      .draw();

      oTable

      .columns( 3 )

      .search( $('#filter_role').val() )

      .draw();

    }

    else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')

    {

      $.session.set("members_search",$('#filter_members').val());

      $.session.set("role_search",$('#filter_role').val());



      oTable

      .columns( 1 )

      .search( $('#filter_members').val() )

      .draw();

      oTable

      .columns( 3 )

      .search( $('#filter_role').val() )

      .draw();

    }

    

    else if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')

    {                  

      ;                                            $.session.set("members_search",$('#filter_members').val());

      $.session.set("members_role",$('#filter_role').val());



      oTable

      .columns( 1 )

      .search( $('#filter_members').val() )

      .draw();

      oTable

      .columns( 2 )

      .search( $('#filter_email').val() )

      .draw();

    }

    

    else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() == '')

    {                    

      $.session.set("members_email",$('#filter_email').val());



      oTable

      .columns( 2 )

      .search( $('#filter_email').val() )

      .draw();



    }

    else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() == '')

    {                  

      $.session.set("members_email",$('#filter_email').val());



      oTable

      .columns( 1 )

      .search( $('#filter_members').val() )

      .draw();

    }

    

    else if($('#filter_email').val() == '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')

    {                   

      $.session.set("members_role",$('#filter_role').val());



      oTable

      .columns( 3 )

      .search( $('#filter_role').val() )

      .draw();



    }

    else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() != '')

    {                  

      $.session.set("members_role",$('#filter_role').val());                                            

      $.session.set("members_email",$('#filter_email').val());

      

      oTable

      .columns( 2 )

      .search( $('#filter_email').val() )

      .draw();



      oTable

      .columns( 3 )

      .search( $('#filter_role').val() )

      .draw();

    }

    else

    {

      $("#clear").click();

    }

  }

});



$('#show').click(function(){

  

  if($('#filter_email').val() != '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')

  {

    $.session.set("members_email",$('#filter_email').val());

    $.session.set("members_search",$('#filter_members').val());

    $.session.set("members_role",$('#filter_role').val());

    oTable

    .columns( 1)

    .search( $('#filter_members').val() )

    .draw();

    oTable

    .columns( 2 )

    .search( $('#filter_email').val() )

    .draw();

    

  }

  

  else if($('#filter_email').val() == '' && $('#filter_members').val() != '' && $('#filter_role').val() != '')

  {

    $.session.set("members_search",$('#filter_members').val());

    $.session.set("role_search",$('#filter_role').val());



    oTable

    .columns( 1 )

    .search( $('#filter_members').val() )

    .draw();

    

  }

  

  else if($('#filter_email').val() != '' && $('#filter_members').val() != '' )

  {

   

    ;     $.session.set("members_search",$('#filter_members').val());

    $.session.set("members_role");



    oTable

    .columns( 1 )

    .search( $('#filter_members').val() )

    .draw();

    oTable

    .columns( 2 )

    .search( $('#filter_email').val() )

    .draw();

  }

  

  else if($('#filter_email').val() != '' && $('#filter_members').val() == '' && $('#filter_role').val() == '')

  {                        

    $.session.set("members_email",$('#filter_email').val());



    oTable

    .columns( 2 )

    .search( $('#filter_email').val() )

    .draw();

  }

  else if($('#filter_email').val() == '' && $('#filter_members').val() != '' )

  {                       

    $.session.set("members_email",$('#filter_email').val());



    oTable

    .columns( 1 )

    .search( $('#filter_members').val() )

    .draw();



  }

  else if($('#filter_email').val() == '' && $('#filter_members').val() == '' )

  {                      

    $.session.set("members_role",$('#filter_role').val());



    oTable

    .columns( 3 )

    .search( $('#filter_role').val() )

    .draw();

  }

  else if($('#filter_email').val() != '' && $('#filter_members').val() == '' )

  {

   

    $.session.set("members_email",$('#filter_email').val());

    

    oTable

    .columns( 2 )

    .search( $('#filter_email').val() )

    .draw();



  }

  else

  {

    $("#clear").click();

  }

});



if($.session.get("addedit")==1)

{

  if($.session.get("members_email") || $.session.get("members_search"))

  {

    $("#filter_email").val($.session.get("members_email"));

    $("#filter_members").val($.session.get("members_search"));

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

  $.session.remove("members_email");

  $.session.remove("members_search");

  $('#filter').val('');

  location.reload();

});

});



function search()

{                     

}

</script>

<div class="content">

  <div class="header">

    <h1 class="page-title">Restaurant</h1>

    <ul class="breadcrumb">

      <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>

      <li class="active">Restaurant</li>

    </ul>

  </div>

  <div class="main-content">

    <div id="custom_filter"> 

      <input type="text" class="form-control" style="width:17%;display:inline;" id="filter_members" name="filter_members" placeholder="Search Restaurant" value=""/>

      <input type="text" class="form-control " style="width:17%;display:inline;margin-left:10px;" id="filter_email" name="filter_email" placeholder="Search email" value=""/>

      <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>

      <input type="button" class="btn btn-primary " id="clear" name="clear" style="margin-left:10px" value="Clear" />

      <a href="<?php echo base_url(); ?>admin/restaurant/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Restaurant</a>

    </div>

    <br/>

    <div class="table-responsive">

      <table class="display hover cell-border table" id="test">

        <thead>

          <tr class="table_th_tr">

            <th class="row-col_1">SEQ. NO.</th>                    

            <th class="row-col_2">RESTAURANT NAME</th>

            <th class="row-col_2">EMAIL</th>

<!--            <th class="row-col_1">BOOKINGS</th> -->

            <th class="row-col_1">MENU CARD</th>

            <th class="row-col_1">Make a Ad</th>

            <th class="row-col_1">TOP 10</th>

            <th class="row-col_1">STATUS</th>

            <th class="row-col_1">EDIT</th>

            <th class="row-col_1">DELETE</th>

          </tr>

        </thead>                        

        <tbody>

          <?php $i = 1; ?>

          <?php

          if ($members) {

            foreach ($members as $rec):

              ?>

            <?php

            $is_add = check_add($rec['user_id']);

            $is_top10 = check_top10($rec['user_id']);

            ?>

            <tr style="text-align:center;">

              <td> <?php echo $i++; ?></td>

              <td style="text-align:center;"> <?php echo $rec['user_first_name']; ?></td>

              <td style="text-align:center;"> <?php echo $rec['user_email']; ?></td>

              <!-- <td><a href="#"><span class="glyphicon glyphicon-list-alt"></span></a></td> -->

              <td> <a href="<?php echo base_url() . 'admin/menucard_view/menu_view/' . $rec['user_id']; ?>"><i class="fa fa-picture-o" aria-hidden="true"></i></a></td>

              <td> <a title="Change add" href="#myAdd" role="button" <?php if ($is_add == "0") { ?> data-add_status="1" <?php } else { ?> data-add_status="0" <?php } ?>  data-toggle="modal" id="add_<?php echo $rec['user_id']; ?>" class="add" data-status="<?php echo $rec['add_id']; ?>"><span class="status_icon"><?php if ($is_add == "0") { ?><i  id="check_add_<?php echo $rec['user_id']; ?>" class="fa fa-ban fa-2x" ></i><?php } else { ?><i id="check_add_<?php echo $rec['user_id']; ?>" class="fa fa-check fa-2x" ></i><?php } ?></span></a></td>

              <td> <a title="Add to top 10 list" href="#myTop10" role="button" <?php if ($is_top10 == "0") { ?> data-top_10="1" <?php } else { ?> data-top_10="0" <?php } ?>  data-toggle="modal" id="top10_<?php echo $rec['user_id']; ?>" class="top10" data-status="<?php echo $rec['top10_id']; ?>"><span class=""><?php if ($is_top10 == "0") { ?><i  id="check_top10_<?php echo $rec['user_id']; ?>" class="glyphicon glyphicon-thumbs-down" ></i><?php } else { ?><i id="check_top10_<?php echo $rec['user_id']; ?>" class="glyphicon glyphicon-thumbs-up" ></i><?php } ?></span></a></td>

              <td> <a title="Change status" href="#myStatus" role="button" data-toggle="modal" id="<?php echo $rec['user_id']; ?>"  class="status" data-status="<?php echo $rec['user_status']; ?>"><span id="status<?php echo $rec['user_id']; ?>" class="status_icon"><?php if ($rec['user_status'] == 0): ?><i class="fa fa-ban fa-2x" ></i><?php else: ?><i class="fa fa-check fa-2x" ></i><?php endif ?></span></a></td>

              <td><a href="<?php echo base_url() . 'admin/restaurant/addedit/' . $rec['user_id']; ?>" title="Edit user"><i class="fa fa-pencil-square-o fa-2x" ></i></a></td>

              <td><a <?php if ($rec['top10_count'] > 0) { ?>          class="errorDelete grey" 

                <?php } else { ?>

                data-toggle="modal" class="delete_button"

                <?php } ?> href="#myModal" id="<?php echo $rec['user_id']; ?>" faq="button" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a></td>

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

      <div class="modal small fade" id="myAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

        <div class="modal-dialog">

          <div class="modal-content">

            <div class="modal-header">

              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>

              <h3 id="myModalLabel">Change Add</h3>

            </div>

            <div class="modal-body">

              <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to change the status of add?</p>

            </div>

            <div class="modal-footer">

              <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>

              <button class="btn btn-primary" data-add_status="" id="btn-add" data-dismiss="modal">Change</button>

            </div>

          </div>

        </div>

      </div>

      <div class="modal small fade" id="myTop10" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

        <div class="modal-dialog">

          <div class="modal-content">

            <div class="modal-header">

              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>

              <h3 id="myModalLabel">Top 10</h3>

            </div>

            <div class="modal-body">

              <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to change the status of Top 10 list?</p>

            </div>

            <div class="modal-footer">

              <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>

              <button class="btn btn-primary" data-add_status="" id="btn-top10" data-dismiss="modal">Change</button>

            </div>

          </div>

        </div>

      </div>



    </div>

  </div>