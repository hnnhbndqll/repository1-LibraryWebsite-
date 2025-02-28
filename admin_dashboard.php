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


$sql_books = "SELECT * FROM books";
$result_books = $conn->query($sql_books);


$sql_users = "SELECT * FROM users";
$result_users = $conn->query($sql_users);


$sql_borrowed = "SELECT borrow_records.id, users.username, books.title, books.authors, borrow_records.borrow_date, borrow_records.return_date 
                 FROM borrow_records 
                 JOIN books ON borrow_records.book_id = books.book_id 
                 JOIN users ON borrow_records.username = users.username";
$result_borrowed = $conn->query($sql_borrowed);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Admin Dashboard</h2>
        <div class="d-flex justify-content-between mb-3">
            <span>Welcome, <strong><?php echo $_SESSION["username"]; ?></strong>!</span>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <h4>Manage Library Books</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Authors</th>
                    <th>Publisher</th>
                    <th>Publication Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = $result_books->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $book['book_id']; ?></td>
                        <td><?php echo $book['title']; ?></td>
                        <td><?php echo $book['authors']; ?></td>
                        <td><?php echo $book['publisher']; ?></td>
                        <td><?php echo $book['publication_year']; ?></td>
                        <td>
                            <a href="edit_book.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_book.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="create_book.php" class="btn btn-primary">Add New Book</a>

   
        <h4 class="mt-5">Manage Users</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result_users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <a href="edit_user.php?user_id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_user.php?user_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="create_user.php" class="btn btn-primary">Add New User</a>

     
        <h4 class="mt-5">Borrowed Books</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Book Title</th>
                    <th>Authors</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($borrowed = $result_borrowed->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $borrowed["username"]; ?></td>
                        <td><?php echo $borrowed["title"]; ?></td>
                        <td><?php echo $borrowed["authors"]; ?></td>
                        <td><?php echo date("m/d/Y", strtotime($borrowed["borrow_date"])); ?></td>
                        <td><?php echo date("m/d/Y", strtotime($borrowed["return_date"])); ?></td>
                        <td>
                            <a href="returned_book.php?id=<?php echo $borrowed['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Mark this book as returned?')">Return</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
