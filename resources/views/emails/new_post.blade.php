<!DOCTYPE html>
<html>
<head>
    <title>New Post Notification</title>
</head>
<body>
    <h2>New Post Published: {{ $title }}</h2>
    <p>{{ Str::substr($description, 0,20) }}</p>
    <p>Author: {{ $author }}</p>
</body>
</html>
