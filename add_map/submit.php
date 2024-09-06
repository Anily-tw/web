<?php
// Load configuration
$config = include '../config.php';

$credentials = $config['credentials'];
$uploadDir = $config['uploadDir'];
$scriptDir = $config['scriptDir'];

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    exit('Unauthorized');
} else {
    $success = false;
    foreach ($credentials as $key => $value) {
        if ($key === $_SERVER['PHP_AUTH_USER'] && $value === $_SERVER['PHP_AUTH_PW']) {
            $success = true;
            break;
        }
    }
    if (!$success) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        exit('Unauthorized');
    }
}

// Define the style block
$style = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Map Upload</title>
    <link rel='stylesheet' href='../styles.css'>
</head>
<body>
    <div class='container'>
";

// Check if file and inputs are provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_FILES['map']) && isset($_POST['category']) && isset($_POST['mapname']) &&
    isset($_POST['mapper']) && isset($_POST['points']) && isset($_POST['stars']) && isset($_POST['timestamp'])) {

    $file = $_FILES['map']['tmp_name'];
    $category = escapeshellarg($_POST['category']);
    $mapname = escapeshellarg($_POST['mapname']);
    $mapper = escapeshellarg($_POST['mapper']);
    $points = (int)$_POST['points'];
    $stars = (int)$_POST['stars'];
    $timestamp = date('Y-m-d H:i:s', strtotime($_POST['timestamp']));

    // Validate file
    if (is_uploaded_file($file)) {
        // Move the uploaded file to a directory accessible by the script
        $uploadFile = $uploadDir . basename($_FILES['map']['name']);
        if (move_uploaded_file($file, $uploadFile)) {
            // Execute the script
            $filename = pathinfo($uploadFile)['filename'];

            $command = escapeshellcmd("{$scriptDir}add_map.py $mapname '$filename' $category $mapper $points $stars '$timestamp'");
            $output = shell_exec($command);

            echo $style . "<h2>$output</h2><div class='debug'>Command: $command</div>";
        } else {
            echo $style . "<h2>File upload failed.</h2>";
        }
    } else {
        echo $style . "<h2>No file uploaded.</h2>";
    }
} else {
    echo $style . "<h2>Invalid request.</h2>";
}

echo "
        <button class='back-button' onclick='history.back()'>Back</button>
    </div>
</body>
</html>";
?>
