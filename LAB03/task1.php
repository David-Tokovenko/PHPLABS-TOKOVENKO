<!DOCTYPE html>
<html>
<head><title>Перевірка на просте число</title></head>
<body>
<form method="post">
    Введіть число: <input type="number" name="num" required>
    <input type="submit" value="Перевірити">
</form>

<?php
if (isset($_POST['num'])) {
    $num = (int)$_POST['num'];
    $isPrime = true;

    if ($num <= 1) {
        $isPrime = false;
    } else {
        for ($i = 2; $i <= sqrt($num); $i++) {
            if ($num % $i == 0) {
                $isPrime = false;
                break;
            }
        }
    }

    echo "<p>Число $num — " . ($isPrime ? "<b>Просте</b>" : "<b>Не просте</b>") . "</p>";
}
?>
</body>
</html>
