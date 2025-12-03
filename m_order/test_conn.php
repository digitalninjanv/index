<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing connection...<br>";

if (file_exists("../../config.php")) {
    echo "config.php found.<br>";
    include("../../config.php");
    
    if (isset($conn)) {
        echo "Connection variable set.<br>";
        if ($conn instanceof PDO) {
            echo "Connection is PDO object.<br>";
            echo "Connection status: " . $conn->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "<br>";
        } else {
            echo "Connection is NOT PDO object.<br>";
        }
    } else {
        echo "Connection variable NOT set.<br>";
    }
} else {
    echo "config.php NOT found.<br>";
}
?>
