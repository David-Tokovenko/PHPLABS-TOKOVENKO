<!DOCTYPE html>
<html>
<head><title>Піднесення до степеня</title></head>
<body>
<form method="post">
    Основа: <input type="number" name="base" required><br>
    Степінь: <input type="number" name="exponent" required><br>
    <input type="submit" value="Обчислити">
</form>

<?php
function power($base, $exponent) {
    return pow($base, $exponent); // або: return $base ** $exponent;
}

if (isset($_POST['base']) && isset($_POST['exponent'])) {
    $base = (float)$_POST['base'];
    $exp = (int)$_POST['exponent'];

    $result = power($base, $exp);
    echo "<p>$base у степені $exp = <strong>$result</strong></p>";
}
?>
</body>
</html>
