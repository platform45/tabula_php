<title>Tabula: Top 10 restaurants</title>

<style>
    .share {
        position: relative;
        display: inline-block;
    }
    .tooltiptext ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }
    .tooltiptext li a {
        display: block;
        padding: 8px;
    }
    .tooltiptext li {
        display: inline;
        float: left;
    }
    .share .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: #290302;

        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }
    .share:hover .tooltiptext {
        visibility: visible;
    }
</style>

<section class="main_content featured">
    <div class="container">
        <div class="row">
            <?php if($top_ten_restaurants) {?>
            <?php foreach ($top_ten_restaurants as $top_ten_restaurant) { ?>

                <div class="col-xs-6 col-sm-6 col-md-4 col filterlistbox">
                    <div class="filterlisting-box">

                        <div class="social-icons" id="social_icons_filter_search_<?php echo $top_ten_restaurant['restaurant_id'] ?>" hidden>
                            <a href="javascript:void(0)" id="close_social_icons" onclick="close_social_icons(<?php echo $top_ten_restaurant['restaurant_id'] ?>)"><span class="uic-close"></span></a>
                            <a href="https://plus.google.com/share?url=<?php echo $top_ten_restaurant['resturantDetailsPath'] ?>"  target="_blank" title="Click to share on google plus">
                                <div class="icon">
                                    <i aria-hidden="true" class="fa fa-google-plus"></i>
                                </div>
                            </a>
                            <a href="http://www.facebook.com/sharer.php?u=<?php echo $top_ten_restaurant['resturantDetailsPath'] ?>" target="_blank" title="Click to share on facebook">
                                <div class="icon">
                                    <i aria-hidden="true" class="fa fa-facebook"></i>
                                </div>
                            </a>
                            <a href="http://twitter.com/share?text=<?php echo $top_ten_restaurant['restaurant_name'] ?>&url=<?php echo $top_ten_restaurant['resturantDetailsPath'] ?>" target="_blank" title="Click to share on Twitter">
                                <div class="icon">
                                    <i aria-hidden="true" class="fa fa-twitter"></i>
                                </div>
                            </a>
                        </div>



                        <?php if ($top_ten_restaurant['is_fav']) { ?>
                            <a href="javascript:void(0)" id="top_ten_<?php echo $top_ten_restaurant['restaurant_id'] ?>"
                               class="favourite active"
                               onclick="removeFromWishlist(<?php echo $top_ten_restaurant['restaurant_id'] ?>,2)"></a>
                        <?php } else { ?>
                            <a href="javascript:void(0)" id="top_ten_<?php echo $top_ten_restaurant['restaurant_id'] ?>"
                               class="favourite"
                               onclick="addToWishlist(<?php echo $top_ten_restaurant['restaurant_id'] ?>,2)"></a>
                        <?php } ?>
                        <a href="<?php echo $top_ten_restaurant['resturantDetailsPath']; ?>" class="imgbox">
                            <img src="<?php echo $top_ten_restaurant['restaurant_image']; ?>" class="img-responsive">
                        </a>
                        <div class="con-box">
                            <h3><a href="javascript:void(0)"><?php echo $top_ten_restaurant['restaurant_name']; ?></a></h3>
                            <h4><?php echo $top_ten_restaurant['address']; ?></h4>
                            <p><?php echo $top_ten_restaurant['region_name']; ?>
                                , <?php echo $top_ten_restaurant['cou_name']; ?></p>
                            <div class="topicons">
                                <div class="ricon">
                                    <?php for ($r_count = 0; $r_count < $top_ten_restaurant['average_spend']; $r_count++) { ?>
                                        <span>R</span>
                                    <?php } ?>

                                </div>
                                <div class="ratting">
                                    <span class="uic-review-star"></span>
                                    <?php echo $top_ten_restaurant['average_rating']; ?>
                                </div>
                            </div>
                            <div class="bottomicons">
                                <?php if ($top_ten_restaurant['no_of_active_table'] && $top_ten_restaurant['time_slots']['status']) { ?>
                                    <div class="timetag">
                                        <?php foreach ($top_ten_restaurant['time_slots']['time_slot'] as $key => $value) { ?>
                                            <a href="javascript:void(0)" onclick="redirect_restaurant_details_with_booking_details(<?php echo $value['slot_id']; ?>, <?php echo $top_ten_restaurant['restaurant_id'] ?>)"
                                               id="<?php echo $value['slot_id']; ?>"><?php echo $value['time_slot']; ?></a>
                                        <?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="timetag">
                                        <a href="javascript:void(0)" class="a_link_disabled">No slots available for this restaurant.</a>
                                    </div>
                                <?php } ?>

                                <a href="javascript:void(0)" class="share" id="share_icon_filter_search_" onclick="show_social_icons(<?php echo $top_ten_restaurant['restaurant_id'] ?>)"><span class="uic-share"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php } else {?>
                <h3>No restaurants found</h3>
            <?php }?>
        </div>
    </div>
</section>