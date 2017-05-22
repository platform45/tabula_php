<footer>
    <div class="container">
        <div class="footerlogo">
            <a href="<?php echo base_url(); ?>home">
                <img src="<?php echo $this->config->item('front_assets'); ?>images/footer-logo.png"
                     class="img-responsive">
            </a>
        </div>
        <div class="social-icons">
            <a href="#">
                <div class="icon">
                    <i class="fa fa-facebook" aria-hidden="true"></i>
                </div>
            </a>
            <a href="#">
                <div class="icon">
                    <i class="fa fa-twitter" aria-hidden="true"></i>
                </div>
            </a>
            <a href="#">
                <div class="icon">
                    <i class="fa fa-instagram" aria-hidden="true"></i>
                </div>
            </a>
        </div>
        <div class="bottom-link">
            <ul class="list-inline">
                <?php $titles = $this->contentmodel->get_content_page_title(); 
                ?>
                <li>
                    <a href="<?php echo base_url(); ?><?php echo $titles[4]['title'] ?>"><?php echo $titles[4]['menu_name'] ?></a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?><?php echo $titles[3]['title'] ?>"><?php echo $titles[3]['menu_name'] ?></a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?><?php echo $titles[0]['title'] ?>"><?php echo $titles[0]['menu_name'] ?></a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?><?php echo $titles[1]['title'] ?>"><?php echo $titles[1]['menu_name'] ?></a>
                </li>
                <li>
                    <a href="<?php echo base_url(); ?><?php echo $titles[2]['title'] ?>"><?php echo $titles[2]['menu_name'] ?></a>
                </li>
            </ul>
        </div>
        <div class="copyright">© 2016 Tabula.com. All Rights Reserved.</div>
    </div>
</footer>
<?php $this->load->view("front/login"); ?>
<?php

if ($this->session->userdata('toast_message')) {

    $success = $this->session->userdata('toast_message');
    $this->session->unset_userdata('toast_message');
}

if ($this->session->userdata('toast_error_message')) {
    $error = $this->session->userdata('toast_error_message');
    $this->session->unset_userdata('toast_error_message');
}
if ($this->session->userdata('open_popup_login')) {
    $open = $this->session->userdata('open_popup_login');
    $this->session->unset_userdata('open_popup_login');
}
?>

<input type="hidden" id="success_msg" name="success_msg" value="<?php echo isset($success) ? $success : ''; ?>"/>
<input type="hidden" id="open_modal" name="open_modal" value="<?php echo isset($open) ? $open : ''; ?>"/>
<input type="hidden" id="error_msg" name="error_msg" value="<?php echo isset($error) ? $error : ''; ?>"/>

<!-- Bootstrap core JavaScript
   ================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
<!--[if lt IE 9]>
<script src="js/ie8-responsive-file-warning.js"></script><![endif]-->
<script src="<?php echo $this->config->item('front_assets'); ?>js/ie-emulation-modes-warning.js"></script>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->


<script src="<?php echo $this->config->item('front_assets'); ?>js/jquery.min.js"></script>
<script src="<?php echo $this->config->item('front_assets'); ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/cookie.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js"></script>

<script src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui-1.8.7.custom.min.js"></script>


<script type="text/javascript" src="<?php echo $this->config->item('front_assets'); ?>js/slick.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('front_assets'); ?>js/moment.js"></script>


<!--<script src="<?php echo $this->config->item('assets'); ?>lib/datetimpicker.js"></script> -->

<script type="text/javascript" src="<?php echo $this->config->item('front_assets'); ?>js/bootstrap-datetimepicker.js"></script>


<script src="<?php echo $this->config->item('front_assets'); ?>js/login.js"></script>
<script type="text/javascript"
        src="<?php echo $this->config->item('front_assets'); ?>js/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript"
        src="<?php echo $this->config->item('front_assets'); ?>js/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
<script type="text/javascript"
        src="<?php echo $this->config->item('front_assets'); ?>js/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
		
		<script src="<?php echo $this->config->item('front_assets'); ?>js/bootstrap-select.min.js"></script>
		
		
<script src="<?php echo $this->config->item('assets'); ?>lib/toastmessage.js"></script>

<script type="text/javascript">var switchTo5x = true;</script>
<script type="text/javascript" id="st_insights_js"
        src="http://w.sharethis.com/button/buttons.js?publisher=28de5b94-b61e-4c31-8ae1-a171d25fbf21"></script>
<script type="text/javascript">stLight.options({
        publisher: "28de5b94-b61e-4c31-8ae1-a171d25fbf21",
        doNotHash: false,
        doNotCopy: false,
        hashAddressBar: false
    });</script>

	<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script> -->
	<script type="text/javascript" src="<?php echo $this->config->item('front_assets'); ?>js/bootstrap-multiselect.js"></script>

<script type="text/javascript" src="<?php echo $this->config->item('front_assets'); ?>js/bootstrap-tabcollapse.js"></script>

<!--<script src="--><?php //echo $this->config->item('front_assets'); ?><!--js/bootstrap-tabcollapse.js"></script>-->
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->

<script src="<?php echo $this->config->item('front_assets'); ?>js/comman.js"></script>
<script src="<?php echo $this->config->item('front_assets'); ?>js/jquery.jqote2.js"></script>
<script src="<?php echo $this->config->item('front_assets'); ?>js/restaurants-filter-listing.js"></script>
<script src="<?php echo $this->config->item('front_assets'); ?>js/profile_screen.js"></script>
<script src="<?php echo $this->config->item('front_assets'); ?>js/jquery.loading.min.js"></script>

<script type="text/javascript" src="<?php echo $this->config->item('front_assets'); ?>js/bootstrap-slider.js"></script>


<script type="text/html" id="cuisine_template">
    <![CDATA[
    <div class="col-xs-6 col-sm-4 col-md-3 col">
        <label class="control control--radio">
            <%= this.cuisine_name %>
            <input type="checkbox" name="radio_cuisine" value="<%= this.cuisine_id %>">
            <div class="control__indicator"></div>
        </label>
    </div>
    ]]>
</script>

<script type="text/html" id="dietary_preference_template">
    <![CDATA[
    <div class="col-xs-6 col-sm-4 col-md-3 col">
        <label class="control control--radio">
            <%= this.diet_preference %>
            <input type="checkbox" name="radio_dietary_preference" value="<%= this.diet_id %>">
            <div class="control__indicator"></div>
        </label>
    </div>
    ]]>
</script>

<script type="text/html" id="ambience_template">
    <![CDATA[
    <div class="col-xs-6 col-sm-4 col-md-3 col">
        <label class="control control--radio">
            <%= this.ambience_name %>
            <input type="checkbox" name="radio_ambience" value="<%= this.ambience_id %>">
            <div class="control__indicator"></div>
        </label>
    </div>
    ]]>
</script>

<script type="text/html" id="restaurant_template">
    <![CDATA[
    <div class="col-xs-6 col-sm-6 col-md-4 col filterlistbox">
        <div class="filterlisting-box">

            <div class="social-icons" id="social_icons_filter_search_<%= this.restaurant_id %>" hidden>
                <a href="javascript:void(0)" id="close_social_icons" onclick="close_social_icons(<%= this.restaurant_id %>)"><span class="uic-close"></span></a>
                <a href="https://plus.google.com/share?url=<%= this.resturantDetailsPath %>"  target="_blank" title="Click to share on google plus">
                    <div class="icon">
                        <i aria-hidden="true" class="fa fa-google-plus"></i>
                    </div>
                </a>
                <a href="http://www.facebook.com/sharer.php?u=<%= this.resturantDetailsPath %>" target="_blank" title="Click to share on facebook">
                    <div class="icon">
                        <i aria-hidden="true" class="fa fa-facebook"></i>
                    </div>
                </a>
                <a href="http://twitter.com/share?text=<%= this.restaurant_name %>&url=<%= this.resturantDetailsPath %>" target="_blank" title="Click to share on Twitter">
                    <div class="icon">
                        <i aria-hidden="true" class="fa fa-twitter"></i>
                    </div>
                </a>
            </div>

            <% if ( this.wish ) { %>
            <a href="#" class="favourite active" id="filter_search_<%= this.restaurant_id %>"
               onclick="removeFromWishlist(<%= this.restaurant_id %>,0)"></a>
            <% } else { %>
            <a href="#" class="favourite " id="filter_search_<%= this.restaurant_id %>"
               onclick="addToWishlist(<%= this.restaurant_id %>,0)"></a>
            <% } %>

            <a href="<%= this.resturantDetailsPath %>" class="imgbox">
                <img src="<%= this.restaurant_image %>" class="img-responsive" alt="<%= this.restaurant_name %>">
            </a>
            <div class="con-box">
                <h3><a href="#"><%= this.restaurant_name %></a></h3>
                <h4><%= this.address %></h4>
                <p><%= this.region_name %>, <%= this.cou_name %></p>
                <div class="topicons">
                    <div class="ricon">
                        <% for(counter = 0; counter < this.average_spend; counter++) { %>
                        <span>R</span>
                        <% } %>
                    </div>
                    <div class="ratting">
                        <span class="uic-review-star"></span>
                        <%= this.average_rating %>
                    </div>
                </div>
                <div class="bottomicons">
                    <div class="timetag">
                        <% if ( this.no_of_active_table == 1 && this.time_slots.status == 1) { %>
                            <% for(counter = 0; counter < this.time_slots.time_slot.length; counter++) { %>
                            <a href="javascript:void(0)" onclick="redirect_restaurant_details_with_booking_details(<%= this.time_slots.time_slot[counter].slot_id %>,<%= this.restaurant_id %>)" id="<%= this.time_slots.time_slot[counter].slot_id %>"><%=
                                this.time_slots.time_slot[counter].time_slot %></a>
                            <% } %>
                        <% } else { %>
                        <a href="#" class="a_link_disabled">No slots available for this restaurant.</a>
                        <% } %>
                    </div>
                    <a href="javascript:void(0)" class="share" id="share_icon_filter_search_<%= this.restaurant_id %>" onclick="show_social_icons(<%= this.restaurant_id %>)"><span class="uic-share"></span></a>
                </div>
            </div>
        </div>
    </div>
    ]]>
</script>

<script type="text/html" id="news_template">
    <![CDATA[
    <div class="col-xs-6 col-sm-6 col-md-4 col filterlistbox">
        <div class="filterlisting-box">

            <a href="#" class="imgbox">
                <img src="<%= this.news_image %>" class="img-responsive" alt="<%= this.news_title %>">
            </a>
            <div class="con-box">
                <h3><a href="#"><%= this.news_title %></a></h3>
                <h4><%= this.news_date %></h4>
            </div>
        </div>
    </div>
    ]]>
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $(".menu_icon").click(function () {
            $(".navbar_content").slideToggle();
            $(this).toggleClass('collapsed');
        });

        <!--$('select').selectpicker(); -->
		
	
        $.ajaxSetup({
            data: {
                csrf_test_name: $.cookie('csrf_cookie_name')
            }
        });

        if ($.trim($('#success_msg').val()) != '') {
            $().toastmessage('showSuccessToast', $('#success_msg').val());
        }
        if ($.trim($('#error_msg').val()) != '') {
            $().toastmessage('showErrorToast', $('#error_msg').val());
        }	
		
		
	
		
		
    });

    function check_review_input() {
        var review = $("#user_review").val();
        $(".review_error_placement").val('');
        if (review.length < 4) {
            $(".review_error_placement").html("Please enter atleast four character");
        }
        else if (review.length > 160) {
            $(".review_error_placement").html("You have crossed the maximum character limit.");
        }
    }
	
	
	
</script>
</body>
</html>