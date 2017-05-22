<table class="display hover cell-border table table-bordered" id="restaurant_table_booking">

    <thead>
        <tr class="table_th_tr">
            <?php
            if ($time_slots) {
                echo "<th>Table($total_capacity)/Time Slots</th>";
                foreach ($time_slots as $time) {
                    echo "<th>" . $time['time_slot'] . "</th>";
                }
            }
            ?>
        </tr>
    </thead>

    <tbody>
        <?php
        if ($restaurant_tables) {
            foreach ($restaurant_tables as $table) {
                echo "<tr>";
                echo "<td>" . $table['table_name'] . "(" . $table['table_capacity'] . ")</td>";

                foreach ($time_slots as $time) {
                    $class = ( $booked_tables[$table['table_name'] . "-" . $time['time_slot']] == 1 ) ? 'class="reserverd"' : '';
                    echo "<td " . $class . ">
                    <a class='booking_link' href='#booking_status' role='button' data-toggle='modal' data-time='" . $time['slot_id'] . "' data-table='" . $table['table_id'] . "'></a>
                  </td>";
                }

                echo "</tr>";
            }
        }
        ?>
    </tbody>

</table>