<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] != "user") {
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


$sql_books = "SELECT * FROM books WHERE book_id NOT IN (
                SELECT book_id FROM borrow_records WHERE return_date IS NULL OR return_date > NOW()
             )";
$result_books = $conn->query($sql_books);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["borrow_book"])) {
    $book_id = $_POST["book_id"];
    $borrow_date = $_POST["borrow_date"];
    $return_date = $_POST["return_date"];
    $username = $_SESSION["username"];

    
    $borrow_date = date("Y-m-d", strtotime($borrow_date));
    $return_date = date("Y-m-d", strtotime($return_date));


    $sql_borrow = "INSERT INTO borrow_records (username, book_id, borrow_date, return_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_borrow);

    if ($stmt) {
        $stmt->bind_param("siss", $username, $book_id, $borrow_date, $return_date);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: user_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>User Dashboard</h2>
        <div class="d-flex justify-content-between mb-3">
            <span>Welcome, <?php echo $_SESSION["username"]; ?>!</span>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <h4>Borrow a Book</h4>
        <form method="post" class="mb-4">
            <input type="hidden" name="borrow_book">
            <div class="mb-3">
                <label for="book_id" class="form-label">Select Book:</label>
                <select name="book_id" class="form-control" required>
                    <option value="">Choose a book...</option>
                    <?php while ($book = $result_books->fetch_assoc()): ?>
                        <option value="<?php echo $book['book_id']; ?>">
                            <?php echo $book['title'] . " by " . $book['authors']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="borrow_date" class="form-label">Date Borrowed (MM/DD/YYYY):</label>
                <input type="text" name="borrow_date" class="form-control" placeholder="MM/DD/YYYY" required>
            </div>
            <div class="mb-3">
                <label for="return_date" class="form-label">Return Date (MM/DD/YYYY):</label>
                <input type="text" name="return_date" class="form-control" placeholder="MM/DD/YYYY" required>
            </div>
            <button type="submit" class="btn btn-primary">Borrow Book</button>
        </form>

      
        <h4>Your Borrowed Books</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Authors</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_user_books = "SELECT borrow_records.id, books.title, books.authors, borrow_records.borrow_date, borrow_records.return_date 
                                   FROM borrow_records 
                                   JOIN books ON borrow_records.book_id = books.book_id 
                                   WHERE borrow_records.username = ?";
                $stmt_user_books = $conn->prepare($sql_user_books);

                if ($stmt_user_books) {
                    $stmt_user_books->bind_param("s", $_SESSION["username"]);
                    $stmt_user_books->execute();
                    $result_user_books = $stmt_user_books->get_result();

                    while ($row = $result_user_books->fetch_assoc()):
                ?>
                        <tr>
                            <td><?php echo $row["title"]; ?></td>
                            <td><?php echo $row["authors"]; ?></td>
                            <td><?php echo date("m/d/Y", strtotime($row["borrow_date"])); ?></td>
                            <td><?php echo date("m/d/Y", strtotime($row["return_date"])); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $row["id"]; ?>" data-borrow="<?php echo date("m/d/Y", strtotime($row["borrow_date"])); ?>" data-return="<?php echo date("m/d/Y", strtotime($row["return_date"])); ?>">Edit</button>
                            </td>
                        </tr>
                <?php 
                    endwhile;
                    $stmt_user_books->close();
                }
                ?>
            </tbody>
        </table>
    </div>

  
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Borrowed Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <input type="hidden" name="edit_book">
                        <input type="hidden" name="record_id" id="editRecordId">
                        <div class="mb-3">
                            <label for="editBorrowDate" class="form-label">Borrow Date:</label>
                            <input type="text" name="edit_borrow_date" id="editBorrowDate" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editReturnDate" class="form-label">Return Date:</label>
                            <input type="text" name="edit_return_date" id="editReturnDate" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll(".edit-btn").forEach(button => {
            button.addEventListener("click", function() {
                document.getElementById("editRecordId").value = this.dataset.id;
                document.getElementById("editBorrowDate").value = this.dataset.borrow;
                document.getElementById("editReturnDate").value = this.dataset.return;
                new bootstrap.Modal(document.getElementById("editModal")).show();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
