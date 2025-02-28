<?php
session_start();


if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET["id"])) {
    $borrow_id = $_GET["id"];

  
    $sql = "DELETE FROM borrow_records WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $borrow_id);
    
    if ($stmt->execute()) {
        $_SESSION["message"] = "Book returned successfully!";
    } else {
        $_SESSION["message"] = "Error returning book!";
    }

    $stmt->close();
}


header("Location: admin_dashboard.php");
exit();
?>
