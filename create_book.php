<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

$title = "";
$authors = "";
$publisher = "";
$publication_year = "";

$error = "";
$success = "";


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";
$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $authors = $_POST["authors"];
    $publisher = $_POST["publisher"];
    $publication_year = $_POST["publication_year"];

    if (empty($title) || empty($authors) || empty($publisher) || empty($publication_year)) {
        $error = "All fields are required!";
    } elseif (!preg_match('/^\d{4}$/', $publication_year)) {
        $error = "Publication Year must be a valid four-digit year (YYYY)!";
    } else {
        
        $sql = "INSERT INTO books (title, authors, publisher, publication_year) 
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $title, $authors, $publisher, $publication_year);
            if ($stmt->execute()) {
                $success = "Book added successfully!";
            
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

  
    $title = "";
    $authors = "";
    $publisher = "";
    $publication_year = "";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Add Book</h2>

      
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

        
        <form method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="mb-3">
                <label for="authors" class="form-label">Authors</label>
                <input type="text" class="form-control" id="authors" name="authors" value="<?php echo htmlspecialchars($authors); ?>" required>
            </div>
            <div class="mb-3">
                <label for="publisher" class="form-label">Publisher</label>
                <input type="text" class="form-control" id="publisher" name="publisher" value="<?php echo htmlspecialchars($publisher); ?>" required>
            </div>
            <div class="mb-3">
                <label for="publication_year" class="form-label">Publication Year</label>
                <input type="number" class="form-control" id="publication_year" name="publication_year" 
                    value="<?php echo !empty($publication_year) ? htmlspecialchars($publication_year) : ''; ?>" 
                    min="1000" max="9999" step="1" pattern="\d{4}" title="Enter a valid four-digit year" required>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
            <a class="btn btn-outline-secondary" href="admin_dashboard.php" role="button">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php

$conn->close();
?>
