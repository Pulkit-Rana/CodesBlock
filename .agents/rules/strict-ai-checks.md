# Strict AI Checks Rule for CodesBlock

All AI agents working within the CodesBlock repository MUST strictly adhere to the standards outlined in `STRICT_AI_CHECKS.md`.

## Mandatory Gates Before Completion:
1. **Syntax & Linting**: Run `php -l` on touched PHP files; verify zero syntax errors.
2. **Security**: Ensure direct access guards (`defined('ABSPATH') || exit;`), output escaping (`esc_html`, `esc_attr`, `esc_url`), input sanitization, and CSRF nonces are used.
3. **Unit Tests**: Always run `php tests/run-tests.php`. Confirm 0 failures and report the output.
4. **Git Discipline**: Use Conventional Commits (`feat:`, `fix:`, `security:`, `chore:`, `ci:`, `docs:`, `test:`).
5. **Hygiene**: No merge conflict markers, no committed `.env`/`.sql`/`.bak`/`.tmp` files.
