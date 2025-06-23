<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система управління студентськими записами</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1, h2 { text-align: center; }
        form { margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"], input[type="number"], select { width: calc(100% - 12px); padding: 6px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; }
        input[type="submit"] { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 3px; cursor: pointer; }
        input[type="submit"]:hover { background-color: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .message { margin-top: 10px; padding: 10px; border: 1px solid #28a745; background-color: #d4edda; color: #155724; border-radius: 3px; }
        .error { border-color: #dc3545; background-color: #f8d7da; color: #721c24; }
        .container { width: 80%; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Система управління студентськими записами</h1>

        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "StudentManagement";

        $conn = new mysqli($servername, $username, $password);

        if ($conn->connect_error) {
            die("<div class='error'>Помилка з'єднання з базою даних: " . $conn->connect_error . "</div>");
        }

        // Створення бази даних
        $sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($conn->query($sql_create_db) === TRUE) {
            echo "<div class='message'>Базу даних '{$dbname}' створено або вона вже існує.</div>";
        } else {
            echo "<div class='error'>Помилка створення бази даних: " . $conn->error . "</div>";
        }

        $conn->select_db($dbname);

        // Створення таблиці студентів
        $sql_create_students = "CREATE TABLE IF NOT EXISTS students (
            student_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE,
            enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($conn->query($sql_create_students) === TRUE) {
            echo "<div class='message'>Таблицю 'students' створено або вона вже існує.</div>";
        } else {
            echo "<div class='error'>Помилка створення таблиці 'students': " . $conn->error . "</div>";
        }

        // Створення таблиці курсів
        $sql_create_courses = "CREATE TABLE IF NOT EXISTS courses (
            course_id INT AUTO_INCREMENT PRIMARY KEY,
            course_name VARCHAR(255) UNIQUE NOT NULL,
            description TEXT
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($conn->query($sql_create_courses) === TRUE) {
            echo "<div class='message'>Таблицю 'courses' створено або вона вже існує.</div>";
        } else {
            echo "<div class='error'>Помилка створення таблиці 'courses': " . $conn->error . "</div>";
        }

        // Створення таблиці оцінок
        $sql_create_grades = "CREATE TABLE IF NOT EXISTS grades (
            grade_id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            course_id INT NOT NULL,
            grade DECIMAL(3, 1) NOT NULL,
            assignment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES students(student_id),
            FOREIGN KEY (course_id) REFERENCES courses(course_id),
            UNIQUE KEY student_course (student_id, course_id)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($conn->query($sql_create_grades) === TRUE) {
            echo "<div class='message'>Таблицю 'grades' створено або вона вже існує.</div>";
        } else {
            echo "<div class='error'>Помилка створення таблиці 'grades': " . $conn->error . "</div>";
        }

        $message = "";

        // Обробка додавання студента
        if (isset($_POST['add_student'])) {
            $name = $conn->real_escape_string($_POST['name']);
            $email = $conn->real_escape_string($_POST['email']);
            $sql_add_student = "INSERT INTO students (name, email) VALUES ('$name', '$email')";
            if ($conn->query($sql_add_student) === TRUE) {
                $message = "<div class='message'>Студента '{$name}' додано успішно.</div>";
            } else {
                $message = "<div class='error'>Помилка додавання студента: " . $conn->error . "</div>";
            }
            echo $message;
        }

        // Обробка додавання курсу
        if (isset($_POST['add_course'])) {
            $course_name = $conn->real_escape_string($_POST['course_name']);
            $description = $conn->real_escape_string($_POST['description']);
            $sql_add_course = "INSERT INTO courses (course_name, description) VALUES ('$course_name', '$description')";
            if ($conn->query($sql_add_course) === TRUE) {
                $message = "<div class='message'>Курс '{$course_name}' додано успішно.</div>";
            } else {
                $message = "<div class='error'>Помилка додавання курсу: " . $conn->error . "</div>";
            }
            echo $message;
        }

        // Обробка додавання оцінки
        if (isset($_POST['add_grade'])) {
            $student_id = intval($_POST['student_id']);
            $course_id = intval($_POST['course_id']);
            $grade = floatval($_POST['grade']);
            $sql_add_grade = "INSERT INTO grades (student_id, course_id, grade) VALUES ($student_id, $course_id, $grade) ON DUPLICATE KEY UPDATE grade = $grade";
            if ($conn->query($sql_add_grade) === TRUE) {
                $message = "<div class='message'>Оцінку додано/оновлено успішно.</div>";
            } else {
                $message = "<div class='error'>Помилка додавання/оновлення оцінки: " . $conn->error . "</div>";
            }
            echo $message;
        }

        // Заповнення БД початковими даними
        $sql_check_students = "SELECT COUNT(*) FROM students";
        $result_check_students = $conn->query($sql_check_students);
        $students_count = $result_check_students->fetch_row()[0];
        if ($students_count == 0) {
            $sql_insert_students = "INSERT INTO students (name, email) VALUES
                ('Іван Петренко', 'ivan.petrenko@example.com'),
                ('Марія Коваленко', 'maria.kovalenko@example.com'),
                ('Олег Сидоров', 'oleg.sydorov@example.com')";
            $conn->query($sql_insert_students);
            echo "<div class='message'>Початкові дані студентів додано.</div>";
        }

        $sql_check_courses = "SELECT COUNT(*) FROM courses";
        $result_check_courses = $conn->query($sql_check_courses);
        $courses_count = $result_check_courses->fetch_row()[0];
        if ($courses_count == 0) {
            $sql_insert_courses = "INSERT INTO courses (course_name, description) VALUES
                ('Українська мова', 'Курс з вивчення української мови'),
                ('Українська література', 'Курс з української літератури'),
                ('Математика', 'Базовий курс з математики')";
            $conn->query($sql_insert_courses);
            echo "<div class='message'>Початкові дані курсів додано.</div>";
        }

        $sql_check_grades = "SELECT COUNT(*) FROM grades";
        $result_check_grades = $conn->query($sql_check_grades);
        $grades_count = $result_check_grades->fetch_row()[0];
        if ($grades_count == 0) {
            $sql_insert_grades = "INSERT INTO grades (student_id, course_id, grade) VALUES
                (1, 1, 95.0),
                (1, 2, 88.5),
                (2, 1, 92.0),
                (2, 3, 79.0),
                (3, 2, 98.0),
                (3, 3, 85.5)";
            $conn->query($sql_insert_grades);
            echo "<div class='message'>Початкові дані оцінок додано.</div>";
        }
        ?>

        <h2>Додати нового студента</h2>
        <form method="post">
            <input type="hidden" name="add_student" value="1">
            <div>
                <label for="name">Ім'я студента:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="email">Email студента:</label>
                <input type="email" id="email" name="email">
            </div>
            <input type="submit" value="Додати студента">
        </form>

        <h2>Додати новий курс</h2>
        <form method="post">
            <input type="hidden" name="add_course" value="1">
            <div>
                <label for="course_name">Назва курсу:</label>
                <input type="text" id="course_name" name="course_name" required>
            </div>
            <div>
                <label for="description">Опис курсу:</label>
                <input type="text" id="description" name="description">
            </div>
            <input type="submit" value="Додати курс">
        </form>

        <h2>Ввести оцінку студента</h2>
        <form method="post">
            <input type="hidden" name="add_grade" value="1">
            <div>
                <label for="student_id">Студент:</label>
                <select id="student_id" name="student_id" required>
                    <option value="">-- Виберіть студента --</option>
                    <?php
                    $sql_students = "SELECT student_id, name FROM students";
                    $result_students = $conn->query($sql_students);
                    if ($result_students->num_rows > 0) {
                        while ($row = $result_students->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['student_id']) . '">' . htmlspecialchars($row['name']) . '</option>';
                        }
                    }
                    $result_students->free();
                    ?>
                </select>
            </div>
            <div>
                <label for="course_id">Курс:</label>
                <select id="course_id" name="course_id" required>
                    <option value="">-- Виберіть курс --</option>
                    <?php
                    $sql_courses = "SELECT course_id, course_name FROM courses";
                    $result_courses = $conn->query($sql_courses);
                    if ($result_courses->num_rows > 0) {
                        while ($row = $result_courses->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['course_id']) . '">' . htmlspecialchars($row['course_name']) . '</option>';
                        }
                    }
                    $result_courses->free();
                    ?>
                </select>
            </div>
            <div>
                <label for="grade">Оцінка (0-100):</label>
                <input type="number" step="0.1" min="0" max="100" id="grade" name="grade" required>
            </div>
            <input type="submit" value="Зберегти оцінку">
        </form>

        <h2>Звіт про середній бал студентів по кожному курсу</h2>
        <?php
        $sql_average_grades = "SELECT c.course_name, AVG(g.grade) AS average_grade
                               FROM grades g
                               JOIN courses c ON g.course_id = c.course_id
                               GROUP BY c.course_name";
        $result_average_grades = $conn->query($sql_average_grades);

        if ($result_average_grades->num_rows > 0) {
            echo "<table>";
            echo "<thead><tr><th>Назва курсу</th><th>Середній бал</th></tr></thead>";
            echo "<tbody>";
            while ($row = $result_average_grades->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['course_name']) . "</td><td>" . number_format($row['average_grade'], 2) . "</td></tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>Немає даних про оцінки для формування звіту.</p>";
        }
        $result_average_grades->free();

        $conn->close();
        ?>
    </div>
</body>
</html>