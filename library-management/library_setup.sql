-- Create database
CREATE DATABASE IF NOT EXISTS library_management;
USE library_management;

-- Create books table
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    published_year YEAR NOT NULL,
    category VARCHAR(100) NOT NULL,
    status ENUM('Available', 'Issued') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO books (title, author, isbn, published_year, category, status) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', '978-0-7432-7356-5', 1925, 'Fiction', 'Available'),
('To Kill a Mockingbird', 'Harper Lee', '978-0-06-112008-4', 1960, 'Fiction', 'Available'),
('1984', 'George Orwell', '978-0-452-28423-4', 1949, 'Dystopian Fiction', 'Issued'),
('Pride and Prejudice', 'Jane Austen', '978-0-14-143951-8', 1813, 'Romance', 'Available'),
('The Catcher in the Rye', 'J.D. Salinger', '978-0-316-76948-0', 1951, 'Fiction', 'Available'),
('Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', '978-0-439-70818-8', 1997, 'Fantasy', 'Issued'),
('The Lord of the Rings', 'J.R.R. Tolkien', '978-0-544-00341-5', 1954, 'Fantasy', 'Available'),
('Dune', 'Frank Herbert', '978-0-441-17271-9', 1965, 'Science Fiction', 'Available');