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

if (isset($_GET["user_id"])) {
    $user_id = $_GET["user_id"];

    $sql_delete_borrows = "DELETE FROM borrow_records WHERE username = (SELECT username FROM users WHERE id = ?)";
    $stmt_delete_borrows = $conn->prepare($sql_delete_borrows);
    $stmt_delete_borrows->bind_param("i", $user_id);
    $stmt_delete_borrows->execute();
    $stmt_delete_borrows->close();


    $sql_delete_user = "DELETE FROM users WHERE id = ?";
    $stmt_delete_user = $conn->prepare($sql_delete_user);
    $stmt_delete_user->bind_param("i", $user_id);

    if ($stmt_delete_user->execute()) {
        echo "<script>alert('User and their borrow records deleted successfully.'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting user.'); window.location.href='admin_dashboard.php';</script>";
    }

    $stmt_delete_user->close();
}

$conn->close();
?>
