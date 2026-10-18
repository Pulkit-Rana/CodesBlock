# Contributing to CodesBlock

Thank you for your interest in contributing to CodesBlock! We operate with enterprise engineering standards to maintain code quality, security, and performance.

---

## 🛠️ Local Development & Requirements

- **PHP**: 8.1, 8.2, or 8.3
- **Node.js**: 20+ (for asset builds)
- **Git**: 2.30+
- **LocalWP / Docker / Nginx**: Local development web server

---

## 🚀 Branching Strategy

All development occurs on short-lived feature or bugfix branches created from `main`:

```bash
# Feature branch
git checkout -b feat/course-quiz-modal

# Bug fix branch
git checkout -b fix/mobile-navigation-toggle

# Security fix
git checkout -b security/sanitize-user-input
```

---

## 🧪 Local Verification Before Pushing

Before opening a pull request or committing changes, run the automated verification suite:

```powershell
# Windows PowerShell:
powershell -ExecutionPolicy Bypass -File scripts/run-checks.ps1

# Linux / macOS / Bash:
bash scripts/run-checks.sh

# Direct unit tests:
php tests/run-tests.php
```

Ensure that:
1. **Repository Hygiene**: Zero merge conflict markers and zero private files tracked.
2. **Syntax**: `php -l` passes across all PHP files.
3. **Security**: SAST checks pass with 0 violations.
4. **Unit Tests**: All unit and functional tests pass with **0 failures**.

---

## 📝 Commit Conventions

All commits must follow the [Conventional Commits](https://www.conventionalcommits.org/) format:

```
<type>(<optional scope>): <imperative summary>
```

### Allowed Types:
- `feat`: New feature or user capability
- `fix`: Bug fix
- `security`: Security patch or hardening
- `refactor`: Internal refactoring without functional changes
- `test`: Adding or updating tests
- `ci`: GitHub Actions or automation updates
- `docs`: Documentation updates
- `perf`: Performance optimizations
- `chore`: Dependency updates, tooling, or maintenance

---

## 📋 Pull Request Process

1. **Keep PRs Focused**: Address one feature or bugfix per PR.
2. **Follow PR Template**: Fill out all mandatory sections in `.github/PULL_REQUEST_TEMPLATE.md`.
3. **Attach Proof**: Include test execution output in the PR description.
4. **CI Passes**: All GitHub Actions workflows (`Essential Checks`, `Code Checks`, `Security Checks`, `Unit Tests`) must pass green before merging.
5. **Review**: Code review requires at least one approval from a designated code owner defined in `.github/CODEOWNERS`.

---

## 🤖 AI-Assisted Development Standards

If you are developing using AI tools or autonomous agents:
- You must comply with [`STRICT_AI_CHECKS.md`](STRICT_AI_CHECKS.md).
- AI code must never skip security sanitization, escaping, nonces, or unit tests.
- Every new function or fix requires automated unit test coverage.
