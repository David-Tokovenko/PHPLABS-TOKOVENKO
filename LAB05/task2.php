<!DOCTYPE html>
<html>
<head><title>Перевірка URL</title></head>
<body>
<h3>Перевірка валідності URL (POST)</h3>

<form method="post">
    Введіть URL: <input type="text" name="url" required><br>
    <input type="submit" value="Перевірити">
</form>

<?php
if (isset($_POST['url'])) {
    $url = $_POST['url'];

    if (filter_var($url, FILTER_VALIDATE_URL)) {
        echo "<p>✅ <strong>$url</strong> — валідний URL.</p>";
    } else {
        echo "<p>❌ <strong>$url</strong> — інвалідний URL.</p>";
    }
}
?>
</body>
</html>
