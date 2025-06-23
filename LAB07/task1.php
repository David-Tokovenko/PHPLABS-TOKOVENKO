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
        .error { border-color: #dc3545; background-color: #f8d7da; color: #721c24; border-radius: 3px; }
        .container { width: 80%; margin: 0 auto; }
        .actions-form { display: inline-block; margin-right: 10px; }
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

        // Підключення до бази даних
        $conn = new mysqli($servername, $username, $password);

        if ($conn->connect_error) {
            die("<div class='error'>Помилка з'єднання з базою даних: " . $conn->connect_error . "</div>");
        }

        // Створення бази даних та таблиць (якщо не існують)
        $sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $conn->query($sql_create_db);
        $conn->select_db($dbname);
        $sql_create_students = "CREATE TABLE IF NOT EXISTS students (student_id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) UNIQUE, enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $conn->query($sql_create_students);
        $sql_create_courses = "CREATE TABLE IF NOT EXISTS courses (course_id INT AUTO_INCREMENT PRIMARY KEY, course_name VARCHAR(255) UNIQUE NOT NULL, description TEXT) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $conn->query($sql_create_courses);
        $sql_create_grades = "CREATE TABLE IF NOT EXISTS grades (grade_id INT AUTO_INCREMENT PRIMARY KEY, student_id INT NOT NULL, course_id INT NOT NULL, grade DECIMAL(3, 1) NOT NULL, assignment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (student_id) REFERENCES students(student_id), FOREIGN KEY (course_id) REFERENCES courses(course_id), UNIQUE KEY student_course (student_id, course_id)) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $conn->query($sql_create_grades);

        $message = "";

        // Обробка додавання студента
        if (isset($_POST['add_student'])) {
            $name = $conn->real_escape_string($_POST['name']);
            $email = $conn->real_escape_string($_POST['email']);
            $sql = "INSERT INTO students (name, email) VALUES ('$name', '$email')";
            if ($conn->query($sql) === TRUE) $message = "<div class='message'>Студента '{$name}' додано успішно.</div>";
            else $message = "<div class='error'>Помилка додавання студента: " . $conn->error . "</div>";
        }

        // Обробка додавання курсу
        if (isset($_POST['add_course'])) {
            $course_name = $conn->real_escape_string($_POST['course_name']);
            $description = $conn->real_escape_string($_POST['description']);
            $sql = "INSERT INTO courses (course_name, description) VALUES ('$course_name', '$description')";
            if ($conn->query($sql) === TRUE) $message = "<div class='message'>Курс '{$course_name}' додано успішно.</div>";
            else $message = "<div class='error'>Помилка додавання курсу: " . $conn->error . "</div>";
        }

        // Обробка додавання/оновлення оцінки
        if (isset($_POST['add_grade'])) {
            $student_id = intval($_POST['student_id']);
            $course_id = intval($_POST['course_id']);
            $grade = floatval($_POST['grade']);
            $sql = "INSERT INTO grades (student_id, course_id, grade) VALUES ($student_id, $course_id, $grade) ON DUPLICATE KEY UPDATE grade = $grade";
            if ($conn->query($sql) === TRUE) $message = "<div class='message'>Оцінку додано/оновлено успішно.</div>";
            else $message = "<div class='error'>Помилка додавання/оновлення оцінки: " . $conn->error . "</div>";
        }

        // Обробка видалення студента
        if (isset($_POST['delete_student'])) {
            $student_id = intval($_POST['student_id']);

            // Спочатку видаляємо оцінки студента
            $sql_delete_grades = "DELETE FROM grades WHERE student_id = $student_id";
            if ($conn->query($sql_delete_grades) === TRUE) {
                // Тепер видаляємо самого студента
                $sql_delete_student = "DELETE FROM students WHERE student_id = $student_id";
                if ($conn->query($sql_delete_student) === TRUE) {
                    $message = "<div class='message'>Студента з ID {$student_id} та всі його оцінки видалено успішно.</div>";
                } else {
                    $message = "<div class='error'>Помилка видалення студента: " . $conn->error . "</div>";
                }
            } else {
                $message = "<div class='error'>Помилка видалення оцінок студента: " . $conn->error . "</div>";
            }
            echo $message;
        }

        // Обробка оновлення студента
        if (isset($_POST['update_student'])) {
            $student_id = intval($_POST['student_id']);
            $name = $conn->real_escape_string($_POST['name']);
            $email = $conn->real_escape_string($_POST['email']);
            $sql = "UPDATE students SET name='$name', email='$email' WHERE student_id=$student_id";
            if ($conn->query($sql) === TRUE) $message = "<div class='message'>Дані студента з ID {$student_id} оновлено успішно.</div>";
            else $message = "<div class='error'>Помилка оновлення даних студента: " . $conn->error . "</div>";
        }

        // Обробка видалення курсу
        if (isset($_POST['delete_course'])) {
            $course_id = intval($_POST['course_id']);

            // Спочатку видаляємо оцінки, пов'язані з курсом
            $sql_delete_grades = "DELETE FROM grades WHERE course_id = $course_id";
            if ($conn->query($sql_delete_grades) === TRUE) {
                // Тепер видаляємо сам курс
                $sql_delete_course = "DELETE FROM courses WHERE course_id = $course_id";
                if ($conn->query($sql_delete_course) === TRUE) {
                    $message = "<div class='message'>Курс з ID {$course_id} та всі пов'язані з ним оцінки видалено успішно.</div>";
                } else {
                    $message = "<div class='error'>Помилка видалення курсу: " . $conn->error . "</div>";
                }
            } else {
                $message = "<div class='error'>Помилка видалення оцінок курсу: " . $conn->error . "</div>";
            }
            echo $message;
        }

        // Обробка оновлення курсу
        if (isset($_POST['update_course'])) {
            $course_id = intval($_POST['course_id']);
            $course_name = $conn->real_escape_string($_POST['course_name']);
            $description = $conn->real_escape_string($_POST['description']);
            $sql = "UPDATE courses SET course_name='$course_name', description='$description' WHERE course_id=$course_id";
            if ($conn->query($sql) === TRUE) $message = "<div class='message'>Дані курсу з ID {$course_id} оновлено успішно.</div>";
            else $message = "<div class='error'>Помилка оновлення даних курсу: " . $conn->error . "</div>";
        }

        // Обробка видалення оцінки
        if (isset($_POST['delete_grade'])) {
            $grade_id = intval($_POST['grade_id']);
            $sql = "DELETE FROM grades WHERE grade_id = $grade_id";
            if ($conn->query($sql) === TRUE) $message = "<div class='message'>Оцінку з ID {$grade_id} видалено успішно.</div>";
            else $message = "<div class='error'>Помилка видалення оцінки: " . $conn->error . "</div>";
        }

        // Обробка оновлення оцінки (пряме оновлення існуючої оцінки обробляється формою додавання)

        // Початкові дані (залишаємо як є)
        $sql_check_students = "SELECT COUNT(*) FROM students";
        $students_count = $conn->query($sql_check_students)->fetch_row()[0];
        if ($students_count == 0) {
            $conn->query("INSERT INTO students (name, email) VALUES ('Іван Петренко', 'ivan.petrenko@example.com'), ('Марія Коваленко', 'maria.kovalenko@example.com'), ('Олег Сидоров', 'oleg.sydorov@example.com')");
            echo "<div class='message'>Початкові дані студентів додано.</div>";
        }
        $sql_check_courses = "SELECT COUNT(*) FROM courses";
        $courses_count = $conn->query($sql_check_courses)->fetch_row()[0];
        if ($courses_count == 0) {
            $conn->query("INSERT INTO courses (course_name, description) VALUES ('Українська мова', 'Курс з вивчення української мови'), ('Українська література', 'Курс з української літератури'), ('Математика', 'Базовий курс з математики')");
            echo "<div class='message'>Початкові дані курсів додано.</div>";
        }
        $sql_check_grades = "SELECT COUNT(*) FROM grades";
        $grades_count = $conn->query($sql_check_grades)->fetch_row()[0];
        if ($grades_count == 0) {
            $conn->query("INSERT INTO grades (student_id, course_id, grade) VALUES (1, 1, 95.0), (1, 2, 88.5), (2, 1, 92.0), (2, 3, 79.0), (3, 2, 98.0), (3, 3, 85.5)");
            echo "<div class='message'>Початкові дані оцінок додано.</div>";
        }

        echo $message;
        ?>

        <h2>Додати нового студента</h2>
        <form method="post">
            <input type="hidden" name="add_student" value="1">
            <div><label for="name">Ім'я студента:</label><input type="text" id="name" name="name" required></div>
            <div><label for="email">Email студента:</label><input type="email" id="email" name="email"></div>
            <input type="submit" value="Додати студента">
        </form>

        <h2>Редагувати студента</h2>
        <form method="post">
            <input type="hidden" name="update_student" value="1">
            <div><label for="student_id">ID студента для редагування:</label>
                <select id="student_id" name="student_id" required>
                    <option value="">-- Виберіть ID студента --</option>
                    <?php
                    $result = $conn->query("SELECT student_id, name FROM students");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['student_id']) . '">' . htmlspecialchars($row['student_id']) . ' - ' . htmlspecialchars($row['name']) . '</option>';
                    }
                    $result->free();
                    ?>
                </select>
            </div>
            <div><label for="name">Нове ім'я студента:</label><input type="text" id="name" name="name"></div>
            <div><label for="email">Новий email студента:</label><input type="email" id="email" name="email"></div>
            <input type="submit" value="Редагувати студента">
        </form>

        <h2>Видалити студента</h2>
        <form method="post">
            <input type="hidden" name="delete_student" value="1">
            <div><label for="student_id">ID студента для видалення:</label>
                <select id="student_id" name="student_id" required>
                    <option value="">-- Виберіть ID студента --</option>
                    <?php
                    $result = $conn->query("SELECT student_id, name FROM students");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['student_id']) . '">' . htmlspecialchars($row['student_id']) . ' - ' . htmlspecialchars($row['name']) . '</option>';
                    }
                    $result->free();
                    ?>
                </select>
            </div>
            <input type="submit" value="Видалити студента">
        </form>

        <h2>Додати новий курс</h2>
        <form method="post">
            <input type="hidden" name="add_course" value="1">
            <div><label for="course_name">Назва курсу:</label><input type="text" id="course_name" name="course_name" required></div>
            <div><label for="description">Опис курсу:</label><input type="text" id="description" name="description"></div>
            <input type="submit" value="Додати курс">
        </form>

        <h2>Редагувати курс</h2>
        <form method="post">
            <input type="hidden" name="update_course" value="1">
            <div><label for="course_id">ID курсу для редагування:</label>
                <select id="course_id" name="course_id" required>
                    <option value="">-- Виберіть ID курсу --</option>
                    <?php
                    $result = $conn->query("SELECT course_id, course_name FROM courses");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['course_id']) . '">' . htmlspecialchars($row['course_id']) . ' - ' . htmlspecialchars($row['course_name']) . '</option>';
                    }
                    $result->free();
                    ?>
                </select>
            </div>
            <div><label for="course_name">Нова назва курсу:</label><input type="text" id="course_name" name="course_name"></div>
            <div><label for="description">Новий опис курсу:</label><input type="text" id="description" name="description"></div>
            <input type="submit" value="Редагувати курс">
        </form>

        <h2>Видалити курс</h2>
        <form method="post">
            <input type="hidden" name="delete_course" value="1">
            <div><label for="course_id">ID курсу для видалення:</label>
                <select id="course_id" name="course_id" required>
                    <option value="">-- Виберіть ID курсу --</option>
                    <?php
                    $result = $conn->query("SELECT course_id, course_name FROM courses");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['course_id']) . '">' . htmlspecialchars($row['course_id']) . ' - ' . htmlspecialchars($row['course_name']) . '</option>';
                    }
                    $result->free();
                    ?>
                </select>
            </div>
            <input type="submit" value="Видалити курс">
        </form>

        <h2>Ввести/Редагувати оцінку студента</h2>
        <form method="post">
            <input type="hidden" name="add_grade" value="1">
            <div><label for="student_id">Студент:</label>
                <select id="student_id" name="student_id" required>
                    <option value="">-- Виберіть студента --</option>
                    <?php
                    $result = $conn->query("SELECT student_id, name FROM students");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['student_id']) . '">' . htmlspecialchars($row['name']) . '</option>';
                    }
                    $result->free();
                    ?>
                </select>
            </div>
            <div><label for="course_id">Курс:</label>
                <select id="course_id" name="course_id" required>
                    <option value="">-- Виберіть курс --</option>
                    <?php
                    $result = $conn->query("SELECT course_id, course_name FROM courses");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['course_id']) . '">' . htmlspecialchars($row['course_name']) . '</option>';
                    }
                    $result->free();
                    ?>
                </select>
            </div>
            <div><label for="grade">Оцінка (0-100):</label><input type="number" step="0.1" min="0" max="100" id="grade" name="grade" required></div>
            <input type="submit" value="Зберегти оцінку">
        </form>

        <h2>Видалити оцінку</h2>
        <form method="post">
            <input type="hidden" name="delete_grade" value="1">
            <div><label for="grade_id">ID оцінки для видалення:</label>
                <select id="grade_id" name="grade_id" required>
                    <option value="">-- Виберіть ID оцінки --</option>
                    <?php
                    $result = $conn->query("SELECT grade_id, s.name AS student_name, c.course_name AS course_name, grade FROM grades g JOIN students s ON g.student_id = s.student_id JOIN courses c ON g.course_id = c.course_id");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['grade_id']) . '">' . htmlspecialchars($row['grade_id']) . ' - ' . htmlspecialchars($row['student_name']) . ' - ' . htmlspecialchars($row['course_name']) . ' - ' . htmlspecialchars($row['grade']) . '</option>';
                    }
                    $result->free();
                    ?>
                </select>
            </div>
            <input type="submit" value="Видалити оцінку">
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