<?php
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not authenticated"]);
    exit;
}

require '../config/config.php';
require '../config/database.php';

header('Content-Type: application/json');

$channelId = trim($_POST['channel_id'] ?? '');
if (!$channelId) {
    echo json_encode(['error' => 'Channel ID is required']);
    exit;
}

// 1. Get channel details & uploads playlist
$channel_url = "https://www.googleapis.com/youtube/v3/channels?part=snippet,contentDetails&id={$channelId}&key=" . YOUTUBE_API_KEY;
$channel_response = @file_get_contents($channel_url);
if(!$channel_response) exit(json_encode(['error'=>'Failed to fetch channel data']));
$channel_data = json_decode($channel_response, true);

if(empty($channel_data['items'])) exit(json_encode(['error'=>'Invalid channel ID']));

$channel = $channel_data['items'][0]['snippet'];
$uploadPlaylistId = $channel_data['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

// 2. Insert channel into DB
$stmt = $pdo->prepare("INSERT IGNORE INTO channels (channel_id, title, description, thumbnail) VALUES (?, ?, ?, ?)");
$stmt->execute([
    $channelId,
    $channel['title'],
    $channel['description'],
    $channel['thumbnails']['default']['url']
]);

// 3. Fetch up to 100 videos from uploads playlist
$videos = [];
$nextPageToken = '';
$totalFetched = 0;

do {
    $playlist_url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId={$uploadPlaylistId}&key=" . YOUTUBE_API_KEY;
    if(!empty($nextPageToken)) $playlist_url .= "&pageToken={$nextPageToken}";

    $playlist_response = @file_get_contents($playlist_url);
    if(!$playlist_response) break;

    $playlist_data = json_decode($playlist_response, true);
    foreach($playlist_data['items'] as $item){
        $snippet = $item['snippet'];
        $videos[] = [
            'video_id' => $snippet['resourceId']['videoId'],
            'title' => $snippet['title'],
            'description' => $snippet['description'],
            'thumbnail' => $snippet['thumbnails']['default']['url'],
            'published_at' => $snippet['publishedAt']
        ];
        $totalFetched++;
        if($totalFetched >= 100) break 2; // Stop once 100 videos reached
    }

    $nextPageToken = $playlist_data['nextPageToken'] ?? '';
} while(!empty($nextPageToken));

// 4. Insert videos into DB
$stmt = $pdo->prepare("INSERT IGNORE INTO videos (video_id, channel_id, title, description, thumbnail, published_at) VALUES (?, ?, ?, ?, ?, ?)");
foreach($videos as $v){
    $stmt->execute([
        $v['video_id'], $channelId, $v['title'], $v['description'], $v['thumbnail'], $v['published_at']
    ]);
}

echo json_encode([
    'success' => true,
    'message' => 'Channel synced successfully',
    'videos_count' => count($videos)
]);