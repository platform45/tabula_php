<?php
function readMore($content,$link,$var,$id, $limit) {
    $content = substr($content,0,$limit);
    $content = substr($content,0,strrpos($content,' '));
    $content = $content." <a href='$link?$var=$id'>Read More...</a>";
    return $content;
}
?>
<title>Tabula: News</title>

<section class="innercontain">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 newsblockleft">
                <?php if ($news) { ?>
                    <?php foreach ($news as $item) { ?>
                        <div class="row newslist">
                            <div class="col-md-5 newslist-img">
                                <a href="<?php echo base_url(); ?>news_details/<?php echo $item['news_description_link']; ?>">
                                    <img src="<?php echo $item['news_image'] ?>" class="img-responsive">
                                </a>
                            </div>

                            <div class="col-md-7">
                                <a href="<?php echo base_url(); ?>news_details/<?php echo $item['news_description_link']; ?>">
                                    <h4><?php echo $item['news_title'] ?></h4>
                                </a>
                                <div class="date"><?php echo $item['news_date_formated']; ?></div>

                                <div>
                                    <p><?php echo $item['short_description'] ?></p>
                                    <a href="<?php echo base_url(); ?>news_details/<?php echo $item['news_description_link']; ?>"
                                       class="read_more">
                                        Read More
                                    </a>
                                </div>
                            </div>


                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="col-md-12">
                        <h4> No news available... </h4>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>