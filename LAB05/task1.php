<!DOCTYPE html>
<html>
<head><title>Множення чисел</title></head>
<body>
<h3>Множення двох чисел (GET)</h3>

<form method="get">
    Число 1: <input type="number" name="num1" required><br>
    Число 2: <input type="number" name="num2" required><br>
    <input type="submit" value="Множити">
</form>

<?php
if (isset($_GET['num1']) && isset($_GET['num2'])) {
    $a = (float)$_GET['num1'];
    $b = (float)$_GET['num2'];
    $result = $a * $b;

    echo "<p>Результат: $a × $b = <strong>$result</strong></p>";
}
?>
</body>
</html>
