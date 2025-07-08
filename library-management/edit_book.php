<?php
require_once 'config/database.php';

$conn = getConnection();
$errors = [];
$book = null;

// Get book ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch book details
$sql = "SELECT * FROM books WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$book = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $published_year = trim($_POST['published_year']);
    $category = trim($_POST['category']);
    $status = $_POST['status'];
    
    // Validation
    if (empty($title)) $errors[] = "Title is required";
    if (empty($author)) $errors[] = "Author is required";
    if (empty($isbn)) $errors[] = "ISBN is required";
    if (empty($published_year) || !is_numeric($published_year)) $errors[] = "Valid published year is required";
    if (empty($category)) $errors[] = "Category is required";
    
    // Check if ISBN already exists (excluding current book)
    if (empty($errors)) {
        $isbn_escaped = mysqli_real_escape_string($conn, $isbn);
        $check_sql = "SELECT id FROM books WHERE isbn = '$isbn_escaped' AND id != $id";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "ISBN already exists";
        }
    }
    
    // Update book if no errors
    if (empty($errors)) {
        $title_escaped = mysqli_real_escape_string($conn, $title);
        $author_escaped = mysqli_real_escape_string($conn, $author);
        $category_escaped = mysqli_real_escape_string($conn, $category);
        $status_escaped = mysqli_real_escape_string($conn, $status);
        
        $sql = "UPDATE books SET 
                title = '$title_escaped', 
                author = '$author_escaped', 
                isbn = '$isbn_escaped', 
                published_year = '$published_year', 
                category = '$category_escaped', 
                status = '$status_escaped'
                WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?message=Book updated successfully!");
            exit();
        } else {
            $errors[] = "Error updating book: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Library Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📚 Edit Book</h1>
            <a href="index.php" class="btn btn-outline">← Back to Library</a>
        </header>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="book-form">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required 
                       value="<?php echo htmlspecialchars(isset($_POST['title']) ? $_POST['title'] : $book['title']); ?>">
            </div>

            <div class="form-group">
                <label for="author">Author *</label>
                <input type="text" id="author" name="author" required 
                       value="<?php echo htmlspecialchars(isset($_POST['author']) ? $_POST['author'] : $book['author']); ?>">
            </div>

            <div class="form-group">
                <label for="isbn">ISBN *</label>
                <input type="text" id="isbn" name="isbn" required 
                       value="<?php echo htmlspecialchars(isset($_POST['isbn']) ? $_POST['isbn'] : $book['isbn']); ?>">
            </div>

            <div class="form-group">
                <label for="published_year">Published Year *</label>
                <input type="number" id="published_year" name="published_year" required min="1000" max="<?php echo date('Y'); ?>"
                       value="<?php echo htmlspecialchars(isset($_POST['published_year']) ? $_POST['published_year'] : $book['published_year']); ?>">
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <input type="text" id="category" name="category" required 
                       value="<?php echo htmlspecialchars(isset($_POST['category']) ? $_POST['category'] : $book['category']); ?>">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <?php 
                    $current_status = isset($_POST['status']) ? $_POST['status'] : $book['status'];
                    ?>
                    <option value="Available" <?php echo ($current_status == 'Available') ? 'selected' : ''; ?>>Available</option>
                    <option value="Issued" <?php echo ($current_status == 'Issued') ? 'selected' : ''; ?>>Issued</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Book</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

<?php
closeConnection($conn);
?>