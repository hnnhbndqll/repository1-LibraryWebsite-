<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$book_id = "";
$book_title = "";
$authors = "";
$publisher = "";
$publication_year = "";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (!isset($_GET['book_id'])) {
        header("Location: admin_dashboard.php"); 
        exit;
    }

    $book_id = $_GET["book_id"];

    $sql = "SELECT * FROM books WHERE book_id = $book_id";  
    $result = $conn->query($sql); 

    if ($result->num_rows == 0) {
        header("Location: admin_dashboard.php"); 
    }

    $row = $result->fetch_assoc();
    $book_title = $row['title'];
    $authors = $row['authors'];
    $publisher = $row['publisher'];
    $publication_year = $row['publication_year'];

  
    if (!empty($publication_year)) {
        $publication_year = $publication_year . "-01-01";
    }

} else {
   
    $book_id = $_POST["book_id"];
    $book_title = $_POST["title"];
    $authors = $_POST["authors"];
    $publisher = $_POST["publisher"];
    $publication_year = $_POST["publication_year"];

   
    if (!empty($publication_year)) {
        $publication_year = date("Y", strtotime($publication_year));
    }

    do {
        if (empty($book_title) || empty($authors) || empty($publisher) || empty($publication_year)) {
            $error = "All fields are required.";
            break;
        }

  
        $sql = "UPDATE books 
                SET title = '$book_title', authors = '$authors', publisher = '$publisher', publication_year = '$publication_year' 
                WHERE book_id = $book_id";

        if (!$conn->query($sql)) {
            $error = "Error updating record: " . $conn->error;
            break;
        }

        $success = "Book updated successfully!";
        header("Location: admin_dashboard.php");
        exit;

    } while (false);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>Edit Book</h2>

      
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
            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($book_title); ?>" required>
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
                 <input type="text" class="form-control" id="publication_year" name="publication_year" value="<?php echo !empty($publication_year) ? $publication_year : ''; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a class="btn btn-outline-secondary" href="admin_dashboard.php" role="button">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
