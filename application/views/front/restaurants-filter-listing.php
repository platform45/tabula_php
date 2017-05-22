<title>Tabula: Restaurants filter search</title>

<div class="restaurants-listing">
      <div class="banner_image header-banner">
         <div class="banner_caption">
         </div>
      </div>
      <!-- Banner Content -->
      <!-- Search button box -->
      <div class="modal fade filterpopup" id="myModal" tabindex="-1" role="dialog" >
         <div class="popup_header">
            <button type="button" class="close" id="filterclose" data-dismiss="modal" aria-label="Close"><span class="uic-close"></button>
            <a href="javascript:void(0)" class="filterlogo">
               <img src="<?php echo $this->config->item('front_assets');?>images/tabula_grey.png" class="img-responsive">
            </a>
         </div>
         <div class="filterheading">
            <span class="uic-filter2"></span> Filters
         </div>

         <div class="modal-dialog modal-lg" role="document">
            <div class="modal-body">
               <div class="form-outer">
                  <form class="form-horizontal" id="restaurants-filter-listing-form">
                     <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Cuisines:</label>
                        <div class="col-sm-10">
                           <div class="row row2" id="cuisines_options">
                           </div>
                        </div>
                     </div>

                     <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Dietary Preference:</label>
                        <div class="col-sm-10">
                           <div class="row row2" id="dietary_preference_options">
                           </div>
                        </div>
                     </div>

                     <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Amenities:</label>
                        <div class="col-sm-10">
                           <div class="row row2" id="ambience_options">
                           </div>
                        </div>
                     </div>

                     <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label top-margin-10">Location:</label>
                        <div class="col-sm-6">
                           <input type="text" placeholder="Enter Location" class="form-control" id="input_restaurants_filter_location">
                        </div>
                     </div>
                     <div class="form-group bottom-border-0">
                        <div class="col-sm-6">
                           <a href="javascript:void(0)" id="applyfilter-button" onclick="restaurant_apply_filter_search()" class="applyfilter-button applyfilter-btn">
                              Apply Filters
                           </a>
                           <a href="javascript:void(0)" class="applyfilter-button reset-btn" id="reset-filter">
                              RESET Filters
                           </a>
                        </div>
                     </div>
                  </form>
               </div>
               


            </div>
         </div>
      </div>
      <!-- Search button box -->
      <!-- Featured Restaurant -->
      <section class="main_content featured" id="reastaurant_filtered_featured_container">

         <div class="modal fade bs-example-modal-lg search-poup keyword_search" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
           <div class="modal-dialog modal-lg" role="document">
             <div class="">
               <div class="modal-body">
                  <div class="row">
                     <div class="col-sm-8 searchfieldouter">
                        <div class="searchfield">
                           <span class="uic-search"></span>
                           <input type="text" id="search_by_restaurant_name_txt" class="form-control" placeholder="Enter Restaurant Name">
                           <a href="javascript:void(0)" class="closefield" data-dismiss="modal" aria-label="Close" id="serchpopup"><span class="uic-close"></span></a>
                        </div>
                     </div>
                     <div class="col-sm-4 searchbuttonin">
                        <button type="button" id="search_by_restaurant_name_sbt_btn" class="search-button">Search</button>
                     </div>
                  </div>
               </div>
             </div>
           </div>
         </div>
         <div class="searchbutton-box" id="searchbutton-box">
            <div class="container">
               <div class="row">
                  <div class="col-sm-6">
                     <a href="javascript:void(0)" class="search-button filter-btn" id="filter" data-toggle="modal" data-target="#myModal">
                        <span class="uic-filter2"></span> Filter
                     </a>
                     <a href="javascript:void(0)" class="search-button search-btn" id="searchbutton" data-toggle="modal" data-target=".keyword_search">
                        <span class="uic-search"></span> Search
                     </a>
                  </div>
                  <div class="col-sm-6 rightheading">
                     <h3>
                        Choose your Favourite
                        <span>Restaurant</span>
                     </h3>
                  </div>
               </div>
            </div>
         </div>

         <div class="container">
            <div class="row" id="reastaurant_filtered_list">
                <div class="col-sm-6 rightheading">
                    <h3>
                        Please select above filter option to search.
                    </h3>
                </div>
            </div>
         </div>
      </section>
      <input type="text" value="0" id="restaurant_offset" hidden></input>

	   <!-- Featured Restaurant -->
      <!-- Banner Content -->
      <div class="banner_image searchnow_banner">
         <div class="banner_caption">
            <h1>Search Top 10 Restaurants</h1>
            <a href="<?php echo base_url();?>top_ten_restaurant" class="search_btn">Search Now</a>
<!--            <a href="javascript:void(0)" class="search_btn">Search Now</a>-->
         </div>
      </div>
</div>
      <!-- Banner Content -->
