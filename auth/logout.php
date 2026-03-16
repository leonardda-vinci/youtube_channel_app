<?php
session_start();

$_SESSION = [];
session_unset();
session_destroy();

header("Location: ../show_youtube_channel.html");
exit;