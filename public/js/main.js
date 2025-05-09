/**
 * Main JavaScript file for the song sing-along feature.
 * Handles verse visibility animations, sing-along mode with timed verse highlighting,
 * play/pause/stop functionality, scroll-to-top, and verse count validation.
 * Assumes PHP renders verses with IDs `verse-99` to `verse-0` (100 verses expected).
 */

/**
 * Sets up IntersectionObserver to animate verses into view as they enter the viewport.
 * Enhances user experience by applying a fade-in effect when scrolling.
 * @see https://developer.mozilla.org/en-US/docs/Web/API/IntersectionObserver
 */
const verses = document.querySelectorAll('.verse');
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        // Add 'visible' class to trigger CSS animation (defined in styles.css)
        entry.target.classList.add('visible');
        // Unobserve after visibility to optimize performance
        observer.unobserve(entry.target);
      }
    });
  },
  {
    threshold: 0.3, // Trigger when 30% of the verse is visible
  }
);

// Observe each verse element for visibility
verses.forEach((verse) => observer.observe(verse));

/**
 * Validates the presence and count of PHP-generated verses on page load.
 * Logs warnings for missing or incomplete verses and disables sing-along if none are found.
 * Expects 100 verses with IDs `verse-99` to `verse-0`.
 * @returns {boolean} True if verses are present, false if none are found
 */
function validateVerses() {
  const verseCount = verses.length;
  const expectedCount = 100; // Matches PHP-generated verse range (99 to 0)

  if (verseCount === 0) {
    console.error('No verses found. Sing-along mode disabled.');
    singButton.disabled = true;
    singButton.textContent = 'No Verses Available';
    singButton.setAttribute('aria-label', 'Sing-along disabled due to missing verses');
    singButton.setAttribute('aria-disabled', 'true');
    return false;
  }

  if (verseCount < expectedCount) {
    console.warn(`Expected ${expectedCount} verses, but found ${verseCount}. Some verses may be missing.`);
  }

  // Check for critical verses (first and last)
  if (!document.getElementById('verse-99')) {
    console.warn('First verse (verse-99) is missing. Sing-along may start incorrectly.');
  }
  if (!document.getElementById('verse-0')) {
    console.warn('Last verse (verse-0) is missing. Sing-along may end prematurely.');
  }

  return true;
}

/**
 * Manages sing-along mode, allowing users to toggle play, pause, and stop.
 * Uses a recursive setTimeout to progress through verses every 5 seconds when playing.
 * Pause preserves the current verse; stop resets to the beginning.
 * Validates verse count on load and disables if none are found.
 * Integrates accessibility features (ARIA attributes, screen reader alerts).
 */
const singButton = document.getElementById('sing-along');
let isSinging = false; // Tracks if sing-along mode is active
let isPaused = false; // Tracks if sing-along is paused
let timeout; // Stores setTimeout ID for cleanup
let currentVerseIndex = 99; // Starting index, assumes PHP renders verses 99 to 0

// Validate verses before enabling sing-along
if (validateVerses()) {
  /**
   * Toggles sing-along mode states (play, pause, stop) on button click.
   * Updates button text, ARIA label, and manages verse highlighting sequence.
   * Play starts/resumes highlighting; pause halts progression; stop resets.
   */
  singButton.addEventListener('click', () => {
    if (!isSinging) {
      // Start sing-along from the beginning
      isSinging = true;
      isPaused = false;
      currentVerseIndex = 99; // Reset to first verse (matches PHP-generated IDs)
      singButton.textContent = 'Pause Singing';
      singButton.setAttribute('aria-label', 'Pause sing-along mode');
      highlightVerse(currentVerseIndex);
    } else if (isPaused) {
      // Resume from the current verse
      isPaused = false;
      singButton.textContent = 'Pause Singing';
      singButton.setAttribute('aria-label', 'Pause sing-along mode');
      highlightVerse(currentVerseIndex);
    } else {
      // Pause or stop based on current state
      isPaused = true;
      singButton.textContent = 'Resume Singing';
      singButton.setAttribute('aria-label', 'Resume sing-along mode');
      clearTimeout(timeout); // Halt progression
    }
  });
}

/**
 * Stops sing-along mode, resetting to initial state.
 * Clears highlights, resets index, and updates button.
 * Used when the last verse is reached or manually stopped.
 */
function stopSingAlong() {
  isSinging = false;
  isPaused = false;
  clearTimeout(timeout);
  resetVerses();
  singButton.textContent = 'Sing Along!';
  singButton.setAttribute('aria-label', 'Start sing-along mode');
}

/**
 * Highlights a specific verse by index and schedules the next verse.
 * Adds visual highlight, scrolls to the verse, and sets ARIA alert for screen readers.
 * Respects pause state by not scheduling new timeouts when paused.
 * @param {number} index - The verse index (e.g., 99 to 0)
 */
function highlightVerse(index) {
  // Clear previous highlights to ensure only one verse is highlighted
  resetVerses();

  // Find verse element by ID (assumes PHP generates IDs like `verse-99`)
  const verse = document.getElementById(`verse-${index}`);
  if (verse) {
    // Apply highlight class (defined in styles.css)
    verse.classList.add('highlight');
    // Set ARIA role to announce verse change to screen readers
    verse.setAttribute('role', 'alert');
    // Smoothly scroll to the verse, centered in the viewport
    verse.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // Schedule the next verse after 5 seconds, unless paused
    if (!isPaused) {
      timeout = setTimeout(() => {
        if (isSinging && !isPaused) {
          currentVerseIndex--;
          if (currentVerseIndex >= 0) {
            // Continue to the next verse
            highlightVerse(currentVerseIndex);
          } else {
            // End sing-along mode when no verses remain
            stopSingAlong();
          }
        }
      }, 5000); // 5-second delay matches typical song pacing
    }
  } else {
    // Log error if verse is missing (e.g., due to PHP rendering issue)
    console.warn(`Verse with ID verse-${index} not found.`);
    // Stop sing-along to prevent infinite loop
    stopSingAlong();
  }
}

/**
 * Removes highlight class from all verses to reset visual state.
 * Called before highlighting a new verse, when pausing, or stopping sing-along mode.
 */
function resetVerses() {
  const verses = document.querySelectorAll('.verse');
  verses.forEach((verse) => {
    verse.classList.remove('highlight');
    // Remove ARIA role to prevent screen reader clutter
    verse.removeAttribute('role');
  });
}

/**
 * Handles the restart button to scroll back to the top of the page.
 * Useful for resetting the view after navigating through verses.
 */
const restartButton = document.getElementById('restart');
restartButton.addEventListener('click', () => {
  // Smooth scroll to top for better UX
  window.scrollTo({ top: 0, behavior: 'smooth' });
});