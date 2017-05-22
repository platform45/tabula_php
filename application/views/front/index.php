<!-- Banner Content -->
<div class="banner_image">
    <div class="banner_caption">
        <h1>Book Your <br/>
            Favourite Restaurants
        </h1>
        <a href="<?php echo base_url(); ?>search" class="search_btn">Search</a>
    </div>
</div>
<!-- Banner Content -->

<?php if($content) { ?>
    <title>Tabula: <?php echo ucfirst(end($this->uri->segment_array()));?></title>
<section class="main_content cms featured">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php echo $content;?>
            </div>

        </div>
    </div>
</section>

<?php } else { ?>

    <title>Tabula</title>
<!-- Featured Restaurant -->
<section class="main_content featured">
    <div class="container">
        <div class="row">
            <h5 class="heading">Featured Restaurants</h5>
            <?php if ($restaurant_details):
                foreach ($restaurant_details as $ads): ?>
                    <div class="col-xs-6 col-sm-6 col-md-4 col">
                        <a href="<?php echo $ads['resturantDetailsPath']; ?>"
                           class="rest_content">
                            <?php if($ads['restaurant_image']) {?>
                            <img src="<?php echo $ads['restaurant_image']; ?>" class="img-responsive">
                            <?php }else { ?>
                                <img src="<?php echo base_url() . "assets/images/restaurent_no_image_available.png"; ?>" class="img-responsive">
                            <?php } ?>


                            <div class="rest_details">
                                <h4><?php echo $ads['restaurant_name']; ?></h4>
                                <?php $cuisine = explode(",", $ads['cuisine']);
                                $cuisine = implode(" / ",$cuisine);
                                ?>
                                <span><?php echo ($cuisine) ? $cuisine : 'No Cuisine'; ?></span>
                            </div>
                        </a>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
    </div>
</section>
<!-- Featured Restaurant -->

<!-- Guest User -->
<section class="main_content guestuser">
    <div class="container">
        <div class="row">
            <h5 class="heading">Guest User</h5>
            <div class="col-sm-4">
                <div class="user_block">
                    <div class="icon"><span class="uic-search"></span></div>
                    <h3>Search Restaurants</h3>
                    <p>Search and discover your city’s finest restaurants</p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="user_block">
                    <div class="icon"><span class="uic-calender"></span></div>
                    <h3>Make Bookings</h3>
                    <p>Make instant, manageable bookings on the app or online</p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="user_block">
                    <div class="icon"><span class="uic-review-star"></span></div>
                    <h3>Add Reviews</h3>
                    <p>Give feedback about your dining experience </p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Guest User -->

<section class="main_content restaurantuser">
    <div class="container">
        <div class="row">
            <h5 class="heading">Restaurant User</h5>
            <div class="col-sm-4">
                <div class="user_block">
                    <div class="icon"><span class="uic-edit"></span></div>
                    <h3>Sign Up</h3>
                    <p>Fill in the membership form to register your restaurant </p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="user_block">
                    <div class="icon"><span class="uic-booking"></span></div>
                    <h3>Monitor Bookings</h3>
                    <p>Monitor all bookings made online through an easy-to-use system</p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="user_block">
                    <div class="icon"><span class="uic-review-check"></span></div>
                    <h3>Check Reviews</h3>
                    <p>Gain feedback from your diners about their experience</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="main_content appscreens">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">

                <div class="decice">
                    <div class="mobileslider">
                        <div><img src="<?php echo $this->config->item('front_assets'); ?>images/tabulaloginscreen.jpg"
                                  class="img-responsive"></div>
                        <div><img src="<?php echo $this->config->item('front_assets'); ?>images/tabulaloginscreen2.jpg"
                                  class="img-responsive"></div>
                        <div><img src="<?php echo $this->config->item('front_assets'); ?>images/tabulaloginscreen.jpg"
                                  class="img-responsive"></div>
                        <div><img src="<?php echo $this->config->item('front_assets'); ?>images/tabulaloginscreen2.jpg"
                                  class="img-responsive"></div>
                    </div>
                </div>

            </div>
            <div class="col-sm-6">
                <div class="appbuttons">
                    <h3>Restaurant Bookings Made Easy</h3>
                    <h4>Download THE app today</h4>
                    <div class="buttonbox">
                        <a href="#"><img
                                    src="<?php echo $this->config->item('front_assets'); ?>images/googleplay_button.png"
                                    class="img-responsive"></a>
                        <a href="#"><img
                                    src="<?php echo $this->config->item('front_assets'); ?>images/appstore_button.png"
                                    class="img-responsive"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php } ?>




