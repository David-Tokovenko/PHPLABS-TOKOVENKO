<?php
function power($base, $exponent) {
    $result = 1;
    for ($i = 1; $i <= $exponent; $i++) {
        $result *= $base;
    }
    return $result;
}

// Приклад виклику
echo "2 у степені 5 = " . power(2, 5); // Виведе 32
?>
