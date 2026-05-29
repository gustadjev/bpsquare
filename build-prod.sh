#!/usr/bin/env bash
# =============================================================================
# BPSquare LLC — Bluehost Production Build Script
# =============================================================================
# Run this locally before uploading to Bluehost.
# Output will be in: angular/bpsquare-frontend/dist/bpsquare-frontend/
# =============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ANGULAR_DIR="$SCRIPT_DIR/angular/bpsquare-frontend"
DIST_DIR="$ANGULAR_DIR/dist/bpsquare-frontend/browser"

echo ""
echo "================================================"
echo "  BPSquare LLC — Production Build"
echo "================================================"
echo ""

# ── Angular production build ──────────────────────────────────────────────────
echo "→ Building Angular (production)..."
cd "$ANGULAR_DIR"
NG_CLI_ANALYTICS=false npx ng build --configuration=production --no-progress

echo ""
echo "✅ Angular build complete."
echo "   Output: $DIST_DIR"
echo ""

# ── Summary ────────────────────────────────────────────────────────────────────
echo "================================================"
echo "  Upload instructions"
echo "================================================"
echo ""
echo "ANGULAR FILES → upload ALL contents of:"
echo "  $DIST_DIR/"
echo "  to the ROOT of public_html/ on Bluehost"
echo ""
echo "WORDPRESS THEME → upload folder:"
echo "  wordpress/wp-content/themes/bpsquare-theme/"
echo "  to wp-content/themes/ on Bluehost"
echo ""
echo "WORDPRESS PLUGIN → upload folder:"
echo "  wordpress/wp-content/plugins/bpsquare-core/"
echo "  to wp-content/plugins/ on Bluehost"
echo ""
echo "The .htaccess file is already included in the dist/ output."
echo "It will REPLACE the default WordPress .htaccess — that is correct."
echo ""
