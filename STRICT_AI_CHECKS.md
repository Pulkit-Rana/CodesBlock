# 🛡️ Strict AI Engineering Checks & Verification Protocol

> **ORGANIZATIONAL MANDATE FOR ALL AI AGENTS & CONTRIBUTORS**  
> This document establishes mandatory quality, security, and verification gates for any AI agent (Antigravity, Gemini, Copilot, Claude, Codex, Cursor) or developer contributing code to the **CodesBlock** repository.
> 
> **Rule Zero:** No task may be declared complete, and no PR or commit may be generated without concrete verification evidence across all 5 gates below.

---

## 🏛️ The 5 Non-Negotiable AI Verification Gates

```
+-----------------------------------------------------------------------------------+
|                            CODESBLOCK AI QUALITY GATES                            |
+-----------------------------------------------------------------------------------+
|  [GATE 1] Syntax & Code Quality   --> php -l, clean code, no debug dumps          |
|  [GATE 2] Essential Hygiene       --> no conflict markers, no untracked junk      |
|  [GATE 3] Security Hardening      --> XSS, SQLi, CSRF nonces, ABSPATH guards       |
|  [GATE 4] Unit Testing            --> php tests/run-tests.php (0 failures)        |
|  [GATE 5] Git & PR Standards      --> Conventional Commits, PR template filled    |
+-----------------------------------------------------------------------------------+
```

---

### Gate 1: Code Quality & Syntax Checks
- **PHP Syntax Verification**: Every touched `.php` file must pass `php -l`.
- **Direct Access Guarding**: All WordPress theme templates and include files must begin with:
  ```php
  if ( ! defined( 'ABSPATH' ) ) {
      exit;
  }
  ```
- **Zero Debug Artifacts**: Remove all instances of `var_dump()`, `print_r()`, `die()`, `console.log()`, and temporary file outputs before committing.
- **Reference**: Read [`docs/ai/AI_CODE_QUALITY_CHECKLIST.md`](docs/ai/AI_CODE_QUALITY_CHECKLIST.md) for full architecture standards.

---

### Gate 2: Essential Checks & Repository Hygiene
- **Merge Conflict Free**: Verify zero git conflict markers (`<<<<<<<`, `=======`, `>>>>>>>`).
- **No Tracked Secrets or Dump Files**: Never stage or commit `.env`, `wp-config.php`, `.pem`, `.key`, `.sql`, `.bak`, `.tmp`, or `.original-backup` files.
- **Line Ending & Encoding Integrity**: Ensure UTF-8 encoding and LF line endings as defined in `.editorconfig` and `.gitattributes`.
- **Diff Hygiene**: Inspect `git diff` before committing to ensure no unintended whitespace, deleted comments, or extraneous line edits.

---

### Gate 3: Security & Vulnerability Hardening
- **Output Escaping**: Never echo raw variables. Always use context-aware escaping:
  - `esc_html()` for HTML body text
  - `esc_attr()` for HTML attributes
  - `esc_url()` for URLs and links
  - `wp_kses_post()` for allowed rich-text HTML
- **Input Sanitization**: All incoming data from `$_GET`, `$_POST`, `$_REQUEST`, or REST payloads must be sanitized (`sanitize_text_field()`, `absint()`, `sanitize_key()`).
- **SQL Injection Prevention**: Never concatenate raw input into database queries. Always use `$wpdb->prepare()`.
- **CSRF & Nonce Protection**: All state-changing requests and AJAX endpoints must verify nonces via `wp_verify_nonce()` or `check_ajax_referer()`.
- **Capability Authorization**: Verify permissions explicitly using `current_user_can()`.
- **Reference**: Read [`docs/ai/AI_SECURITY_CHECKLIST.md`](docs/ai/AI_SECURITY_CHECKLIST.md) for the complete security specification.

---

### Gate 4: Unit Testing & Verification
- **Zero Untested Code Policy**: Every bug fix must include a regression test. Every new feature or endpoint must include unit tests in `tests/unit/`.
- **Mandatory Local Test Run**: Execute the test runner before staging or reporting completion:
  ```bash
  php tests/run-tests.php
  ```
- **Passing Criterion**:
  - `Total Failures: 0`
  - All test suites report `[PASS]`
  - Assertion count accounts for all new or modified code paths.
- **Reference**: Read [`docs/ai/AI_TESTING_CHECKLIST.md`](docs/ai/AI_TESTING_CHECKLIST.md) for testing patterns.

---

### Gate 5: Git & PR Standards
- **Conventional Commits**: Every commit message must follow `<type>(<scope>): <description>`:
  - Examples: `feat(course): ...`, `fix(theme): ...`, `security(auth): ...`, `test(unit): ...`
- **Branch Naming**: Use structured branches: `feat/<slug>`, `fix/<slug>`, `security/<slug>`, `prod_setup`.
- **Pull Request Template**: When opening a PR, fill out every required section of `.github/PULL_REQUEST_TEMPLATE.md`:
  - Summary / Context
  - Type of Change
  - Security Verification Checklist
  - Testing Output / Proof
  - AI Verification Sign-Off
- **Reference**: Read [`docs/ai/AI_PR_REVIEW_CHECKLIST.md`](docs/ai/AI_PR_REVIEW_CHECKLIST.md) for full PR rules.

---

## ⚡ Quick Verification Commands for AI Agents

Run these commands locally prior to finishing:

```powershell
# Windows PowerShell local check:
powershell -File scripts/run-checks.ps1

# Linux / Bash local check:
bash scripts/run-checks.sh

# Direct PHP Unit Test Run:
php tests/run-tests.php
```

---

## 🤖 AI Execution Protocol

1. **Autonomously Plan & Execute**: Proceed decisively without waiting for intermediate approvals.
2. **Execute Local Verification**: Run the automated test runner and syntax linting before staging changes.
3. **Attach Verification Evidence**: In your final response or PR description, include the actual test runner output (tests run, assertions passed, execution time).
4. **Never Skip Gates**: If a gate fails, fix the code immediately and re-run until all 5 gates pass cleanly.
