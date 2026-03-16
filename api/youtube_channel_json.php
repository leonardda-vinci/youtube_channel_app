<?php
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not authenticated"]);
    exit;
}

require '../config/database.php';

$channel_id = $_GET['channel_id'] ?? '';
$page = intval($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT * FROM channels WHERE channel_id=?");
$stmt->execute([$channel_id]);
$channel = $stmt->fetch();

if (!$channel) {
  echo json_encode(['error'=>'Channel not found']);
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM videos WHERE channel_id = ? ORDER BY published_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$channel_id, $limit, $offset]);
$videos = $stmt->fetchAll();

$total_videos = $pdo->query("SELECT COUNT(*) FROM videos WHERE channel_id='$channel_id'")->fetchColumn();

echo json_encode([
  'channel'=>$channel,
  'videos'=>$videos,
  'total_videos'=>$total_videos,
  'page'=>$page,
  'total_pages'=>ceil($total_videos/$limit)
]);