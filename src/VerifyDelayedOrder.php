<?php

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "electric_miles");

// Check db connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// Get current time
$current_time = date("Y-m-d");

// Select orders with estimated delivery time less than current time
$sql = "SELECT * FROM order_details WHERE etd < '$current_time'";
$result = mysqli_query($conn, $sql);

// Loop through results and insert into delayed order table
while ($row = mysqli_fetch_assoc($result)) {
    $order_id = $row['order_id'];
    $estimated_time = $row['etd'];

    $selectQry = "SELECT * FROM delayed_order WHERE `order_id`= '$order_id'";
    $records = mysqli_query($conn, $selectQry);
    $count = mysqli_num_rows($records);

    if($count < 1) {
        $sql2 = "INSERT INTO delayed_order (`order_id`, `curr_time`, `etd`) VALUES ('$order_id', '$current_time','$estimated_time')";
        mysqli_query($conn, $sql2);
    }
}

// Close database connection
mysqli_close($conn);
