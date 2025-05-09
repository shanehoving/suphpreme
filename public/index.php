<?php
declare(strict_types=1);

// Set content type and charset
header('Content-Type: text/html; charset=UTF-8');

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Include PHP logic
require_once __DIR__ . '/../src/LyricsGenerator.php';

use App\LyricsGenerator;

// Get lyrics
$lyrics = LyricsGenerator::generateLyrics();

// Include template
require_once __DIR__ . '/../views/lyrics.php';