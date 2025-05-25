<?php
$host = 'localhost';
$user = 'root';        // Ім'я користувача БД
$password = '';        // Пароль (залиш порожнім для XAMPP/OpenServer)

// Створення підключення до MySQL (без вибору бази)
$conn = new mysqli($host, $user, $password);

// Перевірка з'єднання
if ($conn->connect_error) {
    die("❌ Помилка з'єднання: " . $conn->connect_error);
}

// Створення бази даних BookStore
$sql = "CREATE DATABASE IF NOT EXISTS BookStore";
if ($conn->query($sql) === TRUE) {
    echo "✅ Базу даних 'BookStore' створено або вже існує.<br>";
} else {
    die("❌ Помилка створення БД: " . $conn->error);
}

// Вибір бази даних
$conn->select_db("BookStore");

// Створення таблиці Books
$sql = "CREATE TABLE IF NOT EXISTS Books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    publication_year INT NOT NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "✅ Таблицю 'Books' створено або вона вже існує.";
} else {
    echo "❌ Помилка створення таблиці: " . $conn->error;
}

// Закриття з'єднання
$conn->close();
?>
