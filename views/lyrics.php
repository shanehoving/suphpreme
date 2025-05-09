<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="99 Bottles of Beer on the Wall song lyrics generated with SuPHPreme.">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="99 Bottles of Beer on the Wall">
    <meta property="og:description" content="Sing along with the classic 99 Bottles song, powered by SuPHPreme.">
    <meta property="og:type" content="website">
    <link rel="icon" type="image/svg+xml" href="assets/beer-bottle.svg">
    <title>99 Bottles of Beer on the Wall</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Lyrics injected by PHP, contains <main> with verses -->
    <?php if (!empty($lyrics)): ?>
        <?php echo $lyrics; ?>
    <?php else: ?>
        <main role="main" aria-label="Song Lyrics">
            <p role="alert">Error: Lyrics could not be loaded. Please try again later.</p>
        </main>
    <?php endif; ?>
    <footer role="navigation">
        <button id="sing-along" aria-label="Start sing-along mode" class="sing-along-btn">
            Sing Along!
        </button>
        <a id="restart" aria-label="Scroll to top of song" class="restart-link">
            I Went to the Store
        </a>
    </footer>
    <script defer src="js/main.js"></script>
</body>
</html>