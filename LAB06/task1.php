<?php
$servername = "localhost"; // Зазвичай "localhost"
$username = "your_database_username"; // Замініть на ваше ім'я користувача бази даних
$password = "your_database_password"; // Замініть на ваш пароль бази даних

// Створюємо підключення до сервера MySQL
$conn = new mysqli($servername, $username, $password, null, 3306); // Замініть 3306 на ваш порт, якщо він відрізняється

// Перевіряємо підключення
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

// SQL запит для створення бази даних "BookStore", якщо її не існує
$sql_create_db = "CREATE DATABASE IF NOT EXISTS BookStore";
if ($conn->query($sql_create_db) === TRUE) {
    echo "Базу даних 'BookStore' створено успішно або вона вже існує.<br>";
} else {
    echo "Помилка при створенні бази даних: " . $conn->error . "<br>";
}

// Обираємо базу даних "BookStore"
$conn->select_db("BookStore");

// SQL запит для створення таблиці "Books", якщо її не існує
$sql_create_table = "CREATE TABLE IF NOT EXISTS Books (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    publication_year INT(4)
)";

if ($conn->query($sql_create_table) === TRUE) {
    echo "Таблицю 'Books' створено успішно або вона вже існує.<br>";
} else {
    echo "Помилка при створенні таблиці 'Books': " . $conn->error . "<br>";
}

// Закриваємо з'єднання
$conn->close();

echo "<br>Скрипт виконано.";
?>