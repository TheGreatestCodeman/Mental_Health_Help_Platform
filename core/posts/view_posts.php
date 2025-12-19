<?php
require_once __DIR__ . '/../../config/db.php';

$stmt = $pdo->query(
    "SELECT id, user_id, content, is_anonymous, created_at
     FROM posts
     ORDER BY created_at DESC"
);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
