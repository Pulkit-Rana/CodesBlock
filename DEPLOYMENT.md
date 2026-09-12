# CodesBlock production deployment

This repository contains the WordPress **CodesBlock theme**.

Hostinger deploys `Pulkit-Rana/CodesBlock`, branch `main`, into:

`public_html/wp-content/themes/codesblock`

Never set this repository's deployment directory to `public_html`. Its `index.php`
is a theme template, not the WordPress front controller, and Hostinger replaces
the target directory during deployment.

Pushes and merges to `main` are deployed by Hostinger's existing GitHub
integration. Check Advanced > Git > Deployments in hPanel for the result, then
check the homepage, `/articles/`, `/courses/`, and `/wp-json/`.

Automatic deployment enabled on September 12, 2026. The follow-up documentation
commit is used to verify a real push-triggered deployment without changing UI.

The September 12 baseline preserves the theme recovered from the September 10
production backup. Uncommitted LocalWP edits remain in the original local
checkout and must be reviewed and committed deliberately.

WordPress core, uploads, database, `wp-config.php`, and the first-party plugins
`codesblock-core` and `codesblock-commerce` live outside this repository. They
are maintained separately and are not replaced by a theme deployment.

Use Git to revert a bad theme commit and push the revert to `main`. Keep backups
outside the document root. Never commit credentials, database exports, or full
site archives. Never disable malware scanning to keep an infected file online.
