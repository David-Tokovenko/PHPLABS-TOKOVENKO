function factorial($n) {
if ($n <= 1) {
return 1;
}
return $n * factorial($n - 1);
}
for ($i = 1; $i <= 5; $i++) {
echo "Факторіал $i: " . factorial($i) . "<br>";
}