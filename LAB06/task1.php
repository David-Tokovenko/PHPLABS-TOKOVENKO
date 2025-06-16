<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "BookStore";

// Створення з'єднання
$conn = new mysqli($servername, $username, $password);

// Перевірка з'єднання
if ($conn->connect_error) {
    die("Помилка з'єднання: " . $conn->connect_error);
}
// Створення бази даних
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Базу даних створено або вона вже існує<br>";
} else {
    die("Помилка створення бази даних: " . $conn->error);
}

// Вибір бази даних
$conn->select_db($dbname);

// Створення таблиці
$sql = "CREATE TABLE IF NOT EXISTS Books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    author VARCHAR(255),
    price FLOAT,
    publication_year INT
)";
if ($conn->query($sql) === TRUE) {
    echo "Таблицю Books створено<br>";
} else {
    die("Помилка створення таблиці: " . $conn->error);
}

// Додавання записів
$sql = "INSERT INTO Books (title, author, price, publication_year) VALUES
    -- Українські книги
    ('Тіні забутих предків', 'Михайло Коцюбинський', 120.50, 1911),
    ('Кайдашева сімʼя', 'Іван Нечуй-Левицький', 99.90, 1879),
    ('Зачарована Десна', 'Олександр Довженко', 109.00, 1942),
    ('Лісова пісня', 'Леся Українка', 115.25, 1911),
    ('Маруся Чурай', 'Ліна Костенко', 135.00, 1979),

    -- Зарубіжні книги
    ('1984', 'George Orwell', 180.00, 1949),
    ('To Kill a Mockingbird', 'Harper Lee', 170.00, 1960),
    ('The Great Gatsby', 'F. Scott Fitzgerald', 160.00, 1925),
    ('Pride and Prejudice', 'Jane Austen', 155.00, 1813),
    ('The Catcher in the Rye', 'J.D. Salinger', 145.00, 1951)
";

if ($conn->query($sql) === TRUE) {
    echo "Дані успішно додані";
} else {
    echo "Помилка додавання даних: " . $conn->error;
}

$conn->close();
?>
