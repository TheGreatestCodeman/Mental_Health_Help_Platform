<?php
$mysqli = require __DIR__ . '/../../config/database.php';

$query = "
SELECT 
    p.post_id,
    p.post_from_past,
    u.name,
    u.user_id
FROM post p
JOIN can_create c ON p.post_id = c.post_id
JOIN users u ON c.user_id = u.user_id
ORDER BY p.post_id DESC
";

$result = $mysqli->query($query);

$post = [];

while ($row = $result->fetch_assoc()) {
    $post[] = $row;
}

header('Content-Type: application/json');
echo json_encode($post);
