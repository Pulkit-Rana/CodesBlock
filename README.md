# CodesBlock WordPress Theme

This repository contains the CodesBlock theme. Hostinger deploys its files to `public_html/wp-content/themes/codesblock` from the `main` branch.

## Where site work belongs

- **WordPress Admin:** write and edit articles, courses, and lessons; manage users, menus, media, and pages. Course pages use the title, excerpt, metadata, syllabus, thumbnail, and lesson records saved there.
- **WordPress plugins:** manage memberships, payments, email, social sign-in, SEO, and course data entry.
- **This repository:** change the theme's layout, styles, templates, and front-end behavior.

The repository does not contain WordPress core, the site database, posts, courses, lessons, users, uploads, plugin source, plugin settings, or secrets. Theme templates read WordPress content and use public plugin interfaces where needed.

## Setup and migration

For a fresh WordPress install or an existing-site migration, follow [DEPLOYMENT.md](DEPLOYMENT.md). GitHub deploys the theme files only. Existing posts, courses, lessons, accounts, uploads, and plugin settings must be restored separately from a WordPress backup or configured in WordPress Admin.

For everyday publishing and theme changes, see [CODEBLOCK-MAINTENANCE.md](CODEBLOCK-MAINTENANCE.md).

## Theme changes

Edit the PHP templates and files under `assets/`, review the diff, then push to `main`. Hostinger's GitHub integration deploys the tracked theme files. Content edits do not require a Git commit.

The local WordPress installation under `app/` is intentionally excluded from Git. Never add database exports, `wp-config.php`, credentials, or plugin folders to this repository.

## Development checks

PHP 8.1+ and Git are sufficient for theme work. The repository also includes optional local checks and tests:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/run-checks.ps1
```

See [SECURITY.md](SECURITY.md) before reporting or handling a security issue.
