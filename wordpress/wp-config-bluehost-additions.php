<?php
/**
 * BPSquare LLC — Bluehost wp-config.php additions
 *
 * Copy these lines into your Bluehost wp-config.php
 * JUST BEFORE the "That's all, stop editing!" line.
 *
 * Replace YOUR_STRONG_SECRET with a real random string (50+ chars).
 */

// Angular frontend origin — must match your live domain exactly (no trailing slash)
define( 'BPSQUARE_ALLOWED_ORIGIN', 'https://bpsquarellc.com' );

// WordPress address — where WP files live
define( 'WP_SITEURL', 'https://bpsquarellc.com' );

// Public site address — same as above for root install
define( 'WP_HOME', 'https://bpsquarellc.com' );

// Force SSL in admin
define( 'FORCE_SSL_ADMIN', true );

// Disable file editing from WP admin (security)
define( 'DISALLOW_FILE_EDIT', true );

// Disable automatic plugin/theme updates (deploy manually)
define( 'AUTOMATIC_UPDATER_DISABLED', false );

// Limit post revisions to keep the database clean
define( 'WP_POST_REVISIONS', 5 );

// Set memory limit
define( 'WP_MEMORY_LIMIT', '256M' );
