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

$username = "";
$password = "";
$role = "user"; 
$error = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["cancel"])) {
        header("Location: admin_dashboard.php");
        exit();
    }

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    if (empty($username) || empty($password)) {
        $error = "Username and password are required!";
    } else {
       
        $check_sql = "SELECT id FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
           
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

         
            $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sss", $username, $hashed_password, $role);
                if ($stmt->execute()) {
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $error = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
        $check_stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Add User</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-control" id="role" name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
            <button type="submit" class="btn btn-secondary" name="cancel">Cancel</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
