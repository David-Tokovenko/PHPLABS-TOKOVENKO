<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "BookStore";

// Створення з'єднання
$conn = new mysqli($servername, $username, $password);

// Перевірка з'єднання
if ($conn->connect_error) {
    die("Помилка з'єднання з базою даних: " . $conn->connect_error);
}

// Створення бази даних
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Базу даних створено або вона вже існує<br>";
} else {
    die("Помилка створення бази даних: " . $conn->error);
}

$message = "";

// Обробка відправки форми
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Вибір бази даних перед виконанням запиту UPDATE
    $conn->select_db($dbname);

    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $publication_year = $_POST['publication_year'];

    $sql = "UPDATE Books SET title='$title', author='$author', price='$price', publication_year='$publication_year' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $message = "Запис про книгу з ID " . htmlspecialchars($id) . " успішно оновлено";
    } else {
        $message = "Помилка оновлення запису: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оновлення даних про книгу</title>
</head>
<body>
    <h1>Оновлення даних про книгу</h1>

    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <div>
            <label for="id">ID книги для оновлення:</label>
            <input type="number" id="id" name="id" required>
        </div>
        <br>
        <div>
            <label for="title">Назва книги:</label>
            <input type="text" id="title" name="title">
        </div>
        <br>
        <div>
            <label for="author">Автор:</label>
            <input type="text" id="author" name="author">
        </div>
        <br>
        <div>
            <label for="price">Ціна:</label>
            <input type="number" step="0.01" id="price" name="price">
        </div>
        <br>
        <div>
            <label for="publication_year">Рік публікації:</label>
            <input type="number" id="publication_year" name="publication_year">
        </div>
        <br>
        <input type="submit" value="Оновити книгу">
    </form>
</body>
</html>