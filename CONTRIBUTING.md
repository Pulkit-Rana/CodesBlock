# Contributing to the CodesBlock theme

This repository is for the CodesBlock WordPress theme. Use WordPress Admin for articles, courses, lessons, users, media, and plugin settings. Submit code changes here when the theme's layout, styling, or front-end behavior needs to change.

## Scope

- Keep theme templates connected to WordPress posts, courses, and lessons.
- Use public interfaces from separately installed plugins; do not copy plugin source into this repository.
- Never commit WordPress core, the local `app/` install, database exports, uploads, credentials, or secrets.
- Keep content and plugin configuration in WordPress, not hardcoded in templates.

## Change process

1. Make a focused change to the theme.
2. Review the complete diff and check for secrets, content snapshots, and unrelated files.
3. Run the local verification command when available:

   ```powershell
   powershell -ExecutionPolicy Bypass -File scripts/run-checks.ps1
   ```

4. Commit with a short Conventional Commit message. Push reviewed changes to `main` when authorized; Hostinger deploys that branch to the theme directory.

For security issues, follow [SECURITY.md](SECURITY.md).
