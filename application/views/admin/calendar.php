<style>
    #calendar {
        background-color: white;
    }
    .fc-time {
        display: none;
    }
</style>

<script type="text/javascript">
    var defaultCSS = document.getElementById('bootstrap-css');
    function changeCSS(css) {
        if (css) $('head > link').filter(':first').replaceWith('<link rel="stylesheet" href="' + css + '" type="text/css" />');
        else $('head > link').filter(':first').replaceWith(defaultCSS);
    }
    $(document).ready(function () {
        var iframe_height = parseInt($('html').height());
        window.parent.postMessage(iframe_height, 'http://bootsnipp.com');
    });
</script>
<!-- put this within head tag, this location creates a validation error -->

<link rel='stylesheet' href='http://fullcalendar.io/js/fullcalendar-2.2.3/fullcalendar.css'/>

<div class="content">
    <div class="header">
        <h1 class="page-title">Calendar View</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">Calendar</li>
        </ul>
    </div>
<div class="row">
    <div class="col-lg-12 calendar_div">
        <div id='calendar'></div>
    </div>
</div>
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script src='http://fullcalendar.io/js/fullcalendar-2.2.3/lib/moment.min.js'></script>
<script src='http://fullcalendar.io/js/fullcalendar-2.2.3/fullcalendar.min.js'></script>

<script type="text/javascript">
    $(document).ready(function () {
        $.session.set("currentDate", '');
        $('#calendar').fullCalendar({
            header: {
                left: '',
                center: 'title',
                right: 'prev , today,  next'
            },
            height: 650,
            aspectRatio: 2,
            events: function (start, end, callback) {
                var todayDate = $('#calendar').fullCalendar('getDate');
                $.ajax({
                    url: "<?php echo $this->config->item("admin_url") . 'calendar/get_monthly_booking_daywise_count'; ?>",
                    dataType: 'JSON',
                    data: {
                        todayDate: todayDate.unix()
                    },
                    type: 'POST',
                    success: function (data) {
                        if (data.success) {
                            var booking_data = data.booking_data;
                            $.each(data.data, function (key, value) {
                                var event = {
                                    title: 'Booking Count:  ' + value.count,
                                    start: value.date,
                                    backgroundColor: '#3b1401'
                                };
                                $('#calendar').fullCalendar('renderEvent', event);
                            });
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function () {
                        console.log('there was an error while fetching events!');
                    }
                });
            }

        });

        $(".fc-today-button").text("Today");

    });


</script>
