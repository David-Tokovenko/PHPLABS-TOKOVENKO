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

// Вибір бази даних
$conn->select_db($dbname);

$message = "";
$searchResults = [];

// Обробка відправки форми оновлення
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_book'])) {
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

// Обробка відправки форми пошуку
if (isset($_GET["search_title"])) {
    $searchTitle = trim($_GET["search_title"]);
    if (!empty($searchTitle)) {
        $searchTitle = $conn->real_escape_string($searchTitle);
        $sql = "SELECT id, title, author, price, publication_year FROM Books WHERE title LIKE '%$searchTitle%'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $searchResults[] = $row;
            }
        } else {
            $message = "Книги з назвою, що містить '" . htmlspecialchars($searchTitle) . "', не знайдено.";
        }
        $result->free();
    } else {
        $message = "Будь ласка, введіть назву книги для пошуку.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Керування книгами</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Керування книгами</h1>

    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <h2>Оновлення даних про книгу</h2>
    <form method="post">
        <input type="hidden" name="update_book" value="1">
        <div>
            <label for="id">ID книги для оновлення:</label>
            <input type="number" id="id" name="id" required>
        </div>
        <br>
        <div>
            <label for="title">Нова назва:</label>
            <input type="text" id="title" name="title">
        </div>
        <br>
        <div>
            <label for="author">Новий автор:</label>
            <input type="text" id="author" name="author">
        </div>
        <br>
        <div>
            <label for="price">Нова ціна:</label>
            <input type="number" step="0.01" id="price" name="price">
        </div>
        <br>
        <div>
            <label for="publication_year">Новий рік публікації:</label>
            <input type="number" id="publication_year" name="publication_year">
        </div>
        <br>
        <input type="submit" value="Оновити книгу">
    </form>

    <hr>

    <h2>Пошук книг за назвою</h2>
    <form method="get">
        <div>
            <label for="search_title">Введіть назву книги:</label>
            <input type="text" id="search_title" name="search_title">
        </div>
        <br>
        <input type="submit" value="Пошук">
    </form>

    <?php if (!empty($searchResults)): ?>
        <h2>Результати пошуку</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Автор</th>
                    <th>Ціна</th>
                    <th>Рік публікації</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($searchResults as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['id']); ?></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['price']); ?></td>
                        <td><?php echo htmlspecialchars($book['publication_year']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>