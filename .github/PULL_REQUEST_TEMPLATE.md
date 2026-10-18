## Description / Context
<!-- Provide a clear, concise summary of the change and motivation. Link to relevant issues if applicable. -->
Fixes / Relates to: #

## Type of Change
- [ ] 🐛 Bug fix (non-breaking change which fixes an issue)
- [ ] ✨ New feature (non-breaking change which adds functionality)
- [ ] 🔒 Security fix / hardening
- [ ] ♻️ Refactoring / clean-up
- [ ] ⚡ Performance improvement
- [ ] 🧪 Tests / CI-CD pipeline updates
- [ ] 📝 Documentation update

## Security Checklist
<!-- Every change in CodesBlock must be verified against security standards -->
- [ ] **Direct Access Guarded**: PHP files contain `defined('ABSPATH') || exit;` where required.
- [ ] **Input Sanitization**: User inputs (`$_GET`, `$_POST`, `$_REQUEST`, REST body) sanitized via `sanitize_text_field()`, `intval()`, or custom validators.
- [ ] **Output Escaping**: Outputs properly escaped (`esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`).
- [ ] **SQL Injection Prevention**: Any dynamic database queries use `$wpdb->prepare()`.
- [ ] **CSRF Protection**: State-changing AJAX / POST actions verify nonces via `wp_verify_nonce()` or `check_ajax_referer()`.
- [ ] **Access Control**: Authenticated endpoints enforce permissions via `current_user_can()`.
- [ ] **No Secrets Exposed**: Verified no API keys, private keys, passwords, or `.env` files are committed.

## Testing & Verification Checklist
<!-- Describe how this was tested and attach proof or output -->
- [ ] **Unit Tests Passed**: `php tests/run-tests.php` executed cleanly locally.
- [ ] **PHP Syntax Check Passed**: `php -l` passed across all modified PHP files.
- [ ] **Regression / New Tests Added**: New test cases added to `tests/unit/` or `tests/security/` for new features or bug fixes.
- [ ] **Manual Cross-Device / Browser Check**: Verified on desktop and mobile viewports if UI changes were made.

### Test Execution Output / Evidence
```bash
# Paste test output or paste screenshot evidence here
```

## AI Agent Verification (if AI-assisted)
- [ ] Verified compliance with `STRICT_AI_CHECKS.md`.
- [ ] Ran automated tests and verified 0 failures before opening PR.
- [ ] Self-reviewed diff against repository coding conventions.
