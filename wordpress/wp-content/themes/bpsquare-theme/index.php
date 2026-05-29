<?php
/**
 * BPSquare Theme — index.php
 *
 * Headless fallback template. The Angular SPA handles all public-facing rendering.
 * This page is only seen if someone accesses WordPress directly (not via Angular).
 *
 * @package BPSquare_Theme
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<title><?php bloginfo( 'name' ); ?></title>
	<style>
		*{margin:0;padding:0;box-sizing:border-box}
		body{font-family:system-ui,sans-serif;background:#0a1628;color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;text-align:center;padding:2rem}
		.logo{font-size:2rem;font-weight:700;color:#4f9cf9;margin-bottom:1rem}
		p{color:#a0aec0;margin-bottom:1.5rem;max-width:480px}
		a{display:inline-block;padding:.75rem 1.75rem;background:#4f9cf9;color:#fff;text-decoration:none;border-radius:6px;font-weight:600}
		a:hover{background:#3b82f6}
	</style>
</head>
<body>
	<div class="logo">BPSquare LLC</div>
	<p>
		The BPSquare website is powered by Angular. If you're seeing this page,
		please access the site through the correct domain.
	</p>
	<a href="<?php echo esc_url( get_admin_url() ); ?>">WordPress Admin</a>
	<?php wp_footer(); ?>
</body>
</html>
