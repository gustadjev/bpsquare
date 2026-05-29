<?php
/**
 * Maintenance / Coming Soon page template.
 *
 * Rendered by Maintenance_Mode::render_maintenance_page() when maintenance
 * mode is active. Receives no variables — all output is self-contained.
 *
 * @package BPSquare_Core
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BPSquare LLC — Coming Soon</title>
  <meta name="robots" content="noindex, nofollow" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: #0a0f1e;
      color: #fff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      text-align: center;
    }

    .container { max-width: 640px; width: 100%; }

    .logo {
      font-size: 2rem;
      font-weight: 800;
      letter-spacing: -0.5px;
      margin-bottom: 0.25rem;
    }
    .logo span { color: #4f8ef7; }

    .divider {
      width: 60px;
      height: 3px;
      background: linear-gradient(90deg, #4f8ef7, #7c3aed);
      border-radius: 2px;
      margin: 1.5rem auto;
    }

    h1 {
      font-size: clamp(1.4rem, 4vw, 2.1rem);
      font-weight: 700;
      line-height: 1.25;
      margin-bottom: 1rem;
      color: #f0f4ff;
    }

    p {
      font-size: 1.05rem;
      color: #94a3b8;
      line-height: 1.7;
      margin-bottom: 2rem;
    }

    .badge {
      display: inline-block;
      background: rgba(79, 142, 247, 0.15);
      border: 1px solid rgba(79, 142, 247, 0.4);
      color: #4f8ef7;
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 0.4rem 1rem;
      border-radius: 100px;
      margin-bottom: 2rem;
    }

    .cta {
      display: inline-block;
      background: linear-gradient(135deg, #4f8ef7, #7c3aed);
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 0.75rem 2rem;
      border-radius: 8px;
      transition: opacity 0.2s;
    }
    .cta:hover { opacity: 0.88; }

    footer {
      margin-top: 3rem;
      font-size: 0.8rem;
      color: #475569;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">BP<span>Square</span> LLC</div>
    <div class="divider"></div>
    <div class="badge">🚀 Launching Soon</div>
    <h1>We're putting the finishing touches on something great.</h1>
    <p>
      BPSquare LLC provides practical technology solutions for small businesses,
      minority-owned enterprises, and growing organizations. We're almost ready —
      check back soon.
    </p>
    <a class="cta" href="mailto:info@bpsquarellc.com">Get in Touch Early</a>
  </div>
  <footer>
    &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> BPSquare LLC. All rights reserved.
  </footer>
</body>
</html>
