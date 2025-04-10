<?php
$numbers = [3, 8, 5, 12, 7, 6, 1, 10, 9, 2];

$even = []; // парні
$odd = [];  // непарні

foreach ($numbers as $num) {
    if ($num % 2 == 0) {
        $even[] = $num;
    } else {
        $odd[] = $num;
    }
}
echo "Масив чисел: 3, 8, 5, 12, 7, 6, 1, 10, 9, 2 ;<br>";
echo "Парні числа: " . implode(", ", $even) . "<br>";
echo "Непарні числа: " . implode(", ", $odd);
?>
