<?php
declare(strict_types=1);

namespace App;

/**
 * LyricsGenerator class for generating "99 Bottles of Beer on the Wall" song lyrics.
 * Produces HTML-formatted verses (99 to 0) with unique IDs for front-end navigation and sing-along functionality.
 * Optimized for fixed 99-verse output, integrates with JavaScript (/public/js/main.js) for dynamic highlighting.
 * Uses array-based output and caching for performance.
 */
class LyricsGenerator
{
    /**
     * Base path for assets, configurable for different environments.
     * @var string
     */
    private const ASSET_PATH = 'assets/';

    /**
     * Cached HTML output for the lyrics to avoid regeneration.
     * @var string|null
     */
    private static ?string $cachedLyrics = null;

    /**
     * Generates the lyrics for the song, returning cached output if available.
     * Produces 100 verses (99 to 0) with HTML markup, IDs (`verse-99` to `verse-0`), classes, and ARIA attributes.
     * Uses array-based output for performance and escapes text to prevent XSS.
     *
     * @return string HTML-formatted lyrics
     */
    public static function generateLyrics(): string
    {
        // Return cached lyrics if available
        if (self::$cachedLyrics !== null) {
            return self::$cachedLyrics;
        }

        // Initialize output array to optimize memory usage
        $output = ['<main role="main" aria-label="Song Lyrics">'];
        $output[] = '<h1>99 Bottles of Beer on the Wall</h1>';

        // Generate 100 verses (99 to 0)
        for ($bottles = 99; $bottles >= 0; $bottles--) {
            $output[] = self::generateVerse($bottles);
        }

        $output[] = '</main>';

        // Cache and return the combined output
        self::$cachedLyrics = implode('', $output);
        return self::$cachedLyrics;
    }

    /**
     * Generates HTML for a single verse based on the bottle count.
     * Handles singular/plural text, verse-specific logic, and ARIA attributes.
     *
     * @param int $bottles Current number of bottles (99 to 0)
     * @return string HTML for the verse
     */
    private static function generateVerse(int $bottles): string
    {
        $currentBottles = $bottles;
        $nextBottles = $bottles - 1;

        // Determine singular/plural text and counts
        $bottleText = $currentBottles === 1 ? 'bottle' : 'bottles';
        $nextBottleText = $nextBottles === 1 ? 'bottle' : 'bottles';
        $currentCount = $currentBottles > 0 ? $currentBottles : 'No more';
        $nextCount = $nextBottles > 0 ? $nextBottles : 'no more';

        // Initialize verse output array
        $verseOutput = [];

        // Start verse with ID and ARIA attributes for accessibility
        $verseOutput[] = sprintf(
            '<div id="verse-%d" class="verse" aria-label="Verse for %s bottles" aria-describedby="verse-text-%d">',
            $currentBottles,
            htmlspecialchars((string)$currentBottles, ENT_QUOTES, 'UTF-8'),
            $currentBottles
        );

        // Add beer info with icon (hidden from screen readers)
        $verseOutput[] = sprintf(
            '<div class="beer-info"><img src="%sbeer-bottle.svg" alt="" class="bottle-icon" aria-hidden="true">',
            self::ASSET_PATH
        );
        $verseOutput[] = sprintf(
            '<span class="beer-counter">%s</span></div>',
            htmlspecialchars((string)$currentCount, ENT_QUOTES, 'UTF-8')
        );

        // Add verse text with consistent paragraph structure
        $verseOutput[] = sprintf(
            '<div class="beer-verse-text" id="verse-text-%d">',
            $currentBottles
        );
        $verseOutput[] = sprintf(
            '<p>%s %s of beer on the wall,</p>',
            htmlspecialchars((string)$currentCount, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($bottleText, ENT_QUOTES, 'UTF-8')
        );
        $verseOutput[] = sprintf(
            '<p>%s %s of beer.</p>',
            htmlspecialchars((string)$currentCount, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($bottleText, ENT_QUOTES, 'UTF-8')
        );

        if ($currentBottles > 0) {
            $verseOutput[] = sprintf(
                '<p>%s</p>',
                htmlspecialchars('Take one down and pass it around,', ENT_QUOTES, 'UTF-8')
            );
            $verseOutput[] = sprintf(
                '<p>%s %s of beer on the wall.</p>',
                htmlspecialchars((string)$nextCount, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($nextBottleText, ENT_QUOTES, 'UTF-8')
            );
        } else {
            $verseOutput[] = sprintf(
                '<p>%s</p>',
                htmlspecialchars('Go to the store and buy some more,', ENT_QUOTES, 'UTF-8')
            );
            $verseOutput[] = sprintf(
                '<p>%s bottles of beer on the wall.</p>',
                htmlspecialchars('99', ENT_QUOTES, 'UTF-8')
            );
        }

        $verseOutput[] = '</div></div>';

        return implode('', $verseOutput);
    }
}
?>