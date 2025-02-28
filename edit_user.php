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

if (!isset($_GET['user_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$user_id = $_GET['user_id'];
$error = "";
$success = "";

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: admin_dashboard.php");
    exit();
}

$user = $result->fetch_assoc();
$old_username = $user['username']; 
$role = $user['role'];


$sql_borrowed = "SELECT COUNT(*) AS borrowed_count FROM borrow_records WHERE username = ? AND return_date IS NULL";
$stmt_borrowed = $conn->prepare($sql_borrowed);
$stmt_borrowed->bind_param("s", $old_username);
$stmt_borrowed->execute();
$result_borrowed = $stmt_borrowed->get_result();
$borrowed_data = $result_borrowed->fetch_assoc();
$has_borrowed_books = $borrowed_data["borrowed_count"] > 0; 
$stmt_borrowed->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST["username"]);
    $new_role = $_POST["role"];

    if (empty($new_username) || empty($new_role)) {
        $error = "All fields are required!";
    } elseif ($has_borrowed_books && $new_username !== $old_username) {
        $error = "Username cannot be changed while the user has borrowed books!";
    } else {
       
        $check_sql = "SELECT id FROM users WHERE username = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $new_username, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Username already exists!";
        } else {
           
            $update_sql = "UPDATE users SET username = ?, role = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssi", $new_username, $new_role, $user_id);
            $update_stmt->execute();
            $update_stmt->close();

            $success = "User updated successfully!";
            header("Location: admin_dashboard.php");
            exit();
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Edit User</h2>

     
        <?php if (!empty($error)): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong><?php echo $error; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><?php echo $success; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

       
        <?php if ($has_borrowed_books): ?>
            <div class="alert alert-danger">
                <strong>Warning:</strong> This user has borrowed books. You cannot change their username until they return all borrowed books.
            </div>
        <?php endif; ?>

     
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($old_username); ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="user" <?php if ($role == 'user') echo 'selected'; ?>>User</option>
                    <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a class="btn btn-outline-secondary" href="admin_dashboard.php" role="button">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
