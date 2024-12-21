<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="body-wrapper">
        <div class="container">
            <h1>404 Not Found</h1>
            <p>Sorry, the page you are looking for does not exist.</p>
            <a href="<?php echo getenv('APP_URL'); ?>">Go to Home</a>
        </div>
    </div>
</body>
</html>
