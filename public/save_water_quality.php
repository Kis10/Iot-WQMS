<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water_quality";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$device_id = isset($_POST['device_id']) ? $_POST['device_id'] : 'Unknown';
$turbidity = isset($_POST['turbidity']) ? floatval($_POST['turbidity']) : 0;
$tds = isset($_POST['tds']) ? floatval($_POST['tds']) : 0;
$ph = isset($_POST['ph']) ? floatval($_POST['ph']) : (isset($_POST['ph']) ? floatval($_POST['ph']) : 0);
$temperature = isset($_POST['temperature']) ? floatval($_POST['temperature']) : 0;
$humidity = isset($_POST['humidity']) ? floatval($_POST['humidity']) : 0;

// Insert data into database
$sql = "INSERT INTO water_readings (device_id, turbidity, tds, ph, temperature, humidity, created_at, updated_at) 
        VALUES ('$device_id', $turbidity, $tds, $ph, $temperature, $humidity, NOW(), NOW())";

if ($conn->query($sql) === TRUE) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Water quality data saved successfully',
        'data' => [
            'device_id' => $device_id,
            'turbidity' => $turbidity,
            'tds' => $tds,
            'ph' => $ph,
            'temperature' => $temperature,
            'humidity' => $humidity
        ]
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error saving data',
        'error' => $conn->error
    ]);
}

$conn->close();
?>