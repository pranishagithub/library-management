<?php
require_once 'config/database.php';

// Get book ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$conn = getConnection();

// Check if book exists
$check_sql = "SELECT title FROM books WHERE id = $id";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) == 0) {
    header("Location: index.php");
    exit();
}

$book = mysqli_fetch_assoc($check_result);

// Delete the book
$delete_sql = "DELETE FROM books WHERE id = $id";

if (mysqli_query($conn, $delete_sql)) {
    $message = "Book '" . htmlspecialchars($book['title']) . "' deleted successfully!";
    header("Location: index.php?message=" . urlencode($message));
} else {
    $message = "Error deleting book: " . mysqli_error($conn);
    header("Location: index.php?message=" . urlencode($message));
}

closeConnection($conn);
exit();
?>