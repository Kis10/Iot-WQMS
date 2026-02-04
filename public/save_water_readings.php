<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water_quality";

$ablyKey = null;
$ablyChannel = "water-readings";
$envPath = __DIR__ . "/../.env";
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if ($envContent !== false) {
        if (preg_match('/^ABLY_API_KEY\\s*=\\s*(.+)$/m', $envContent, $matches)) {
            $ablyKey = trim($matches[1], " \t\n\r\0\x0B\"");
        }
        if (preg_match('/^ABLY_CHANNEL\\s*=\\s*(.+)$/m', $envContent, $matches)) {
            $ablyChannel = trim($matches[1], " \t\n\r\0\x0B\"");
        }
    }
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "error" => $conn->connect_error
    ]));
}

/* ✅ Check required POST values */
if (
    isset($_POST['device_id']) &&
    isset($_POST['ph']) &&
    isset($_POST['turbidity']) &&
    isset($_POST['tds']) &&
    isset($_POST['temperature']) &&
    isset($_POST['humidity'])
) {

    $device_id   = $conn->real_escape_string($_POST['device_id']);
    $ph          = floatval($_POST['ph']);
    $turbidity   = floatval($_POST['turbidity']);
    $tds         = floatval($_POST['tds']);
    $temperature = floatval($_POST['temperature']);
    $humidity    = floatval($_POST['humidity']);

    /* ✅ Insert into table */
    $sql = "INSERT INTO water_readings
            (device_id, turbidity, tds, ph, temperature, humidity, created_at, updated_at)
            VALUES
            ('$device_id', $turbidity, $tds, $ph, $temperature, $humidity, NOW(), NOW())";

    if ($conn->query($sql) === TRUE) {
        if ($ablyKey) {
            $payload = json_encode([
                "name" => "reading",
                "data" => [
                    "device_id" => $device_id,
                    "turbidity" => (float) $turbidity,
                    "tds" => (float) $tds,
                    "ph" => (float) $ph,
                    "temperature" => (float) $temperature,
                    "humidity" => (float) $humidity,
                    "created_at" => date(DATE_ATOM),
                ]
            ]);

            if ($payload !== false) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://rest.ably.io/channels/" . urlencode($ablyChannel) . "/messages?key=" . urlencode($ablyKey));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
            }
        }
        echo json_encode([
            "success" => true,
            "message" => "Data inserted successfully"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Insert failed",
            "error" => $conn->error
        ]);
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Missing POST data",
        "received" => $_POST
    ]);
}

$conn->close();
?>
