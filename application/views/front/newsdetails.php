<title>Tabula: News</title>

<section class="innercontain newsdetail">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 newsblockleft">
                <div class="row">
                    <div class="col-md-12 detaildescription">

                        <h4><?php echo $news_details[0]['news_title']; ?></h4>
                        <div class="date"><?php echo $news_details[0]['news_date']; ?> </div>

                        <?php if (strlen($news_details[0]['news_image']) > 0) { ?>
                            <a href="#">
                                <img src="<?php echo $news_details[0]['news_image']; ?>" class="img-responsive">
                            </a>
                        <?php } ?>

                        <p>
                            <?php echo $news_details[0]['news_desc']; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
