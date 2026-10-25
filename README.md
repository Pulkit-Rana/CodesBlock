# CodesBlock 🚀

[![Essential Checks](https://github.com/Pulkit-Rana/CodesBlock/actions/workflows/essential-checks.yml/badge.svg?branch=prod_setup)](https://github.com/Pulkit-Rana/CodesBlock/actions/workflows/essential-checks.yml)
[![Code Checks](https://github.com/Pulkit-Rana/CodesBlock/actions/workflows/code-checks.yml/badge.svg?branch=prod_setup)](https://github.com/Pulkit-Rana/CodesBlock/actions/workflows/code-checks.yml)
[![Security Checks](https://github.com/Pulkit-Rana/CodesBlock/actions/workflows/security-checks.yml/badge.svg?branch=prod_setup)](https://github.com/Pulkit-Rana/CodesBlock/actions/workflows/security-checks.yml)
[![Unit Tests](https://github.com/Pulkit-Rana/CodesBlock/actions/workflows/unit-tests.yml/badge.svg?branch=prod_setup)](https://github.com/Pulkit-Rana/CodesBlock/actions/workflows/unit-tests.yml)
[![PHP Version](https://img.shields.io/badge/php-8.1%20%7C%208.2%20%7C%208.3-blue.svg)](https://www.php.net/)
[![Security Standards](https://img.shields.io/badge/security-OWASP%20Hardened-green.svg)](SECURITY.md)

Production engineering and AI education platform codebase for developers, system design engineers, and technical leaders.

---

## 🏛️ Repository Architecture

- **Theme Root (`/`)**: Custom WordPress production theme (`functions.php`, `header.php`, `footer.php`, `front-page.php`, `single-course.php`, etc.).
- **Template Parts (`template-parts/`)**: Modular UI components (`course-map.php`, `paywall.php`, `breadcrumbs.php`).
- **Assets (`assets/`)**: Production styles (`css/`), modular client-side logic (`js/`), and optimized media (`images/`).
- **Tests (`tests/`)**: Zero-dependency test harness (`bootstrap.php`), standalone test runner (`run-tests.php`), and automated suites (`tests/unit/`, `tests/security/`).
- **Automation & CI/CD (`.github/workflows/`)**: Continuous integration, SAST scanning, PR triage, and unit tests.
- **AI Rulebook (`docs/ai/`, `STRICT_AI_CHECKS.md`)**: Strict quality, security, and verification guardrails for autonomous AI coding agents.

---

## ⚡ Quickstart & Local Development

### 1. Requirements
- PHP 8.1+
- Node.js 20+
- Git 2.30+

### 2. Configure Local Git Hooks
Enable automated pre-commit verification so syntax and hygiene issues are caught before committing:

```powershell
# Windows PowerShell:
powershell -File scripts/setup-hooks.ps1

# Linux / macOS / Bash:
bash scripts/setup-hooks.sh
```

### 3. Run Checks Locally
Replicate GitHub Actions CI pipeline locally with one command:

```powershell
# Windows:
powershell -ExecutionPolicy Bypass -File scripts/run-checks.ps1

# Linux / macOS:
bash scripts/run-checks.sh

# Run unit tests directly:
php tests/run-tests.php
```

---

## 🛡️ Company CI/CD & Quality Gates

Every pull request and push to `main` and `prod_setup` must pass 5 automated gates:

```
+-----------------------------------------------------------------------------------+
|                            CODESBLOCK CI/CD PIPELINE                              |
+-----------------------------------------------------------------------------------+
|  1. Essential Checks   --> Conventional commit PR title, merge conflict detector  |
|  2. Code Checks        --> php -l linting on PHP 8.1/8.2/8.3, JS syntax check     |
|  3. Security Checks    --> Gitleaks secret scan, SAST OWASP scanner, npm/composer |
|  4. Unit Tests         --> php tests/run-tests.php (20 suites, 148 assertions)    |
|  5. PR Review Triage   --> Auto-sizing, checklist verification, label triage      |
+-----------------------------------------------------------------------------------+
```

---

## 🤖 Strict AI Engineering Standards

This repository is optimized for autonomous AI pair programming and agentic workflows. All AI agents (Antigravity, Gemini, Copilot, Claude) must follow the non-negotiable standards defined in:

- 📖 [**`STRICT_AI_CHECKS.md`**](STRICT_AI_CHECKS.md)
- 🔒 [**AI Security Checklist**](docs/ai/AI_SECURITY_CHECKLIST.md)
- 🧹 [**AI Code Quality Standards**](docs/ai/AI_CODE_QUALITY_CHECKLIST.md)
- 🧪 [**AI Testing Standards**](docs/ai/AI_TESTING_CHECKLIST.md)
- 📋 [**AI PR Review Standards**](docs/ai/AI_PR_REVIEW_CHECKLIST.md)

---

## 🤝 Contributing & Security

- See [**`CONTRIBUTING.md`**](CONTRIBUTING.md) for branch naming conventions, PR templates, and workflow guidelines.
- See [**`SECURITY.md`**](SECURITY.md) for vulnerability disclosure policies and contact instructions.
