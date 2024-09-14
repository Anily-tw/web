<?php
// Load configuration
$config = include '../config.php';

$credentials = $config['credentials'];
$permissions = $config['permissions'];
$uploadDir = $config['uploadDir'];

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
    <title>Map Update</title>
    <link rel='stylesheet' href='../styles.css'>
</head>
<body>
    <div class='container'>
";

// Check if file and inputs are provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['map'])) {
    $file = $_FILES['map']['tmp_name'];
    $category = $_POST['category'];

    $allowed_categories = $permissions[$_SERVER['PHP_AUTH_USER']];

    if (!in_array($category, $allowed_categories)) {
        echo $style . "<h2>You don't have permission to update in this category.</h2><div class='debug'>Allowed categories: "; foreach($allowed_categories as $cat) { echo $cat . " "; }; echo "</div>";
    }
    // Validate file
    else if (is_uploaded_file($file)) {
        // Move the uploaded file to a directory accessible by the script
        $uploadFile = $uploadDir . $category . '/' . basename($_FILES['map']['name']);
        if (move_uploaded_file($file, $uploadFile)) {
            $filename = pathinfo($uploadFile)['filename'];
            echo $style . "<h2>Map $filename was updated</h2>";
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
