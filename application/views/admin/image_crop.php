<link rel="stylesheet" href="<?php echo $this->config->item('assets');?>stylesheets/crop/cropper.css">
            <div class="content">
                <div class="header">
                    <h1 class="page-title">Image Crop111</h1>
                </div><!-- header -->
                
                <div class="main-content addstanding">
                    <div id="test-modal" style="margin-top:50px;" class="white-popup-block mfp-hide">
                                    <div class="container-fluid eg-container" id="basic-example">
                                        <div class="eg-main">
                                            <div class="col-sm-9">
                                                <div class="eg-wrapper">
                                                    <img class="cropper" src="<?php echo $sImagePath.$sImageName; ?>" alt="Picture">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="eg-preview clearfix">
                                                <div class="preview preview-lg"></div>
                                                    <!--          <div class="preview preview-md"></div>
                                                              <div class="preview preview-sm"></div>
                                                              <div class="preview preview-xs"></div>-->
                                                </div>
                                                <form action="<?php echo base_url();?>admin/crop/process" method="post" >
                                                    <div class="eg-data">
                                                        <input class="form-control" id="imagePath" name="imageName" type="hidden" value="<?php echo $sImageName; ?>">

                                                        <input class="form-control" id="dataX" name="dataX" type="hidden" placeholder="x">

                                                        <input class="form-control" id="dataY" name="dataY" type="hidden" placeholder="y">

                                                        <input class="form-control" id="dataW" name="dataW" type="hidden" placeholder="width">

                                                        <input class="form-control" id="dataH" name="dataH" type="hidden" placeholder="height">

                                                        <input id="cropSubmit" type="submit" value="Crop Image" class="btn btn-primary" />
                                                    </div>
                                                </form>

                                            </div>

                                        </div>
                                    </div>
                                    </div>
                </div><!-- main-content -->
            </div><!-- content -->
<link href="<?php echo $this->config->item('assets');?>stylesheets/crop/magnific-popup.css" rel="stylesheet" type="text/css" />
<!--<script type="text/javascript" language="javascript" src="includes/js/jquery.js"></script>-->
<script type="text/javascript" language="javascript" src="<?php echo $this->config->item('assets');?>lib/jquery.magnific-popup.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        (function($) {
            $(window).load(function () {

                $.magnificPopup.open({
                    items: {
                        src: '#test-modal'
                    },
                    type: 'inline'
                });


            });

        })(jQuery);
    });
</script>
<style>
    .preview-lg {
    height: 99px;
    width: 200px;
}
</style>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="<?php echo $this->config->item('assets');?>lib/cropper.js"></script>
<script>
    $(window).load(function () {
        var $cropper = $(".cropper"),
        $dataX = $("#dataX"),
        $dataY = $("#dataY"),
        $dataH = $("#dataH"),
        $dataW = $("#dataW"),
        cropper;

        $cropper.cropper({
            aspectRatio: 2 / 1,
            data: {
                x: 200,
                y: 100,
                width: 200,
                height: 100
            },
            preview: ".preview",

            // autoCrop: false,
            // dragCrop: false,
            // modal: false,
            // moveable: false,
            // resizeable: false,

            // maxWidth: 480,
            // maxHeight: 270,
            // minWidth: 160,
            // minHeight: 90,

            done: function(data) {
                $dataX.val(data.x);
                $dataY.val(data.y);
                $dataH.val(data.height);
                $dataW.val(data.width);
            }
        });

        cropper = $cropper.data("cropper");

        $cropper.on({
            "build.cropper": function(e) {
                console.log(e.type);
                // e.preventDefault();
            },
            "built.cropper": function(e) {
                console.log(e.type);
                // e.preventDefault();
            },
            "render.cropper": function(e) {
                console.log(e.type);
                // e.preventDefault();
            }
        });

        $("#enable").click(function() {
            $cropper.cropper("enable");
        });

        $("#disable").click(function() {
            $cropper.cropper("disable");
        });

        $("#reset").click(function() {
            $cropper.cropper("reset");
        });

        $("#reset-deep").click(function() {
            $cropper.cropper("reset", true);
        });

        $("#release").click(function() {
            $cropper.cropper("release");
        });

        $("#destroy").click(function() {
            $cropper.cropper("destroy");
        });

        $("#setData").click(function() {
            $cropper.cropper("setData", {
                x: $dataX.val(),
                y: $dataY.val(),
                width: $dataW.val(),
                height:$dataH.val()
            });
        });

        $("#setAspectRatio").click(function() {
            $cropper.cropper("setAspectRatio", $("#aspectRatio").val());
        });

        $("#setImgSrc").click(function() {
            $cropper.cropper("setImgSrc", $("#imgSrc").val());
        });

        $("#getImgInfo").click(function() {
            $("#showInfo").val(JSON.stringify($cropper.cropper("getImgInfo")));
        });

        $("#getData").click(function() {
            $("#showData").val(JSON.stringify($cropper.cropper("getData")));
        });
    });

    $.magnificPopup.instance.close = function () {

        window.location.href="<?php echo base_url();?>admin/crop/process/closed";
        $.magnificPopup.proto.close.call(this);

    };
    
</script>