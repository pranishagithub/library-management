<?php
require_once 'config/database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getConnection();
    
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
    
    // Check if ISBN already exists
    if (empty($errors)) {
        $isbn_escaped = mysqli_real_escape_string($conn, $isbn);
        $check_sql = "SELECT id FROM books WHERE isbn = '$isbn_escaped'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "ISBN already exists";
        }
    }
    
    // Insert book if no errors
    if (empty($errors)) {
        $title_escaped = mysqli_real_escape_string($conn, $title);
        $author_escaped = mysqli_real_escape_string($conn, $author);
        $category_escaped = mysqli_real_escape_string($conn, $category);
        $status_escaped = mysqli_real_escape_string($conn, $status);
        
        $sql = "INSERT INTO books (title, author, isbn, published_year, category, status) 
                VALUES ('$title_escaped', '$author_escaped', '$isbn_escaped', '$published_year', '$category_escaped', '$status_escaped')";
        
        if (mysqli_query($conn, $sql)) {
            $success = true;
            header("Location: index.php?message=Book added successfully!");
            exit();
        } else {
            $errors[] = "Error adding book: " . mysqli_error($conn);
        }
    }
    
    closeConnection($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book - Library Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📚 Add New Book</h1>
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
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="author">Author *</label>
                <input type="text" id="author" name="author" required 
                       value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="isbn">ISBN *</label>
                <input type="text" id="isbn" name="isbn" required 
                       value="<?php echo isset($_POST['isbn']) ? htmlspecialchars($_POST['isbn']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="published_year">Published Year *</label>
                <input type="number" id="published_year" name="published_year" required min="1000" max="<?php echo date('Y'); ?>"
                       value="<?php echo isset($_POST['published_year']) ? htmlspecialchars($_POST['published_year']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <input type="text" id="category" name="category" required 
                       value="<?php echo isset($_POST['category']) ? htmlspecialchars($_POST['category']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Available" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                    <option value="Issued" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Issued') ? 'selected' : ''; ?>>Issued</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Book</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>