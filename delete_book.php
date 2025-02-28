<?php

if (isset($_GET["book_id"])) {
    $book_id = $_GET["book_id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library_db";


    $conn = new mysqli($servername, $username, $password, $dbname);

    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $sql = "DELETE FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
}

header("Location: admin_dashboard.php");
exit;

?>
