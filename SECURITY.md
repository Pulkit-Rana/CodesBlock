# Security Policy & Vulnerability Reporting

CodesBlock takes the security of our platform and user data seriously. This document outlines our security procedures, supported versions, and how to responsibly disclose vulnerabilities.

---

## Supported Versions

Only the current active release line on the `main` branch receives active security updates and patches.

| Branch / Release | Supported          |
| ---------------- | ------------------ |
| `main`           | :white_check_mark: |
| `prod_setup`     | :white_check_mark: |
| Legacy / Tags    | :x:                |

---

## Reporting a Vulnerability

**Please DO NOT file public GitHub issues for security vulnerabilities.**

If you believe you have found a security vulnerability in CodesBlock:

1. **GitHub Security Advisory (Preferred)**:
   Navigate to the [Security Advisories](https://github.com/Pulkit-Rana/CodesBlock/security/advisories/new) tab in GitHub and click **"Report a vulnerability"**.

2. **Direct Contact**:
   Send an email to `security@codesblock.com` (or project maintainers) with the subject:
   `[SECURITY VULNERABILITY] <Component/Summary>`

### Please Include in Your Report:
- Vulnerability classification (e.g., SQL Injection, Stored/Reflected XSS, CSRF, Authentication Bypass, Path Traversal, Sensitive Data Exposure).
- Detailed, step-by-step reproduction instructions or a Proof of Concept (PoC).
- Potential impact and affected URLs or parameters.
- Any suggested remediation or patches.

---

## Response & Remediation SLA

| Phase | Target Timeline |
|---|---|
| **Acknowledgment & Triage** | Within 24–48 hours of receipt |
| **Vulnerability Assessment** | Within 3 business days |
| **Patch & Release** | Within 7–14 days for Critical / High severity |
| **Public Disclosure** | Coordinated with finder post-patch release |

---

## Mandatory Engineering Security Controls

All code deployed to production or merged into `main` must pass automated security gates:
- Context-aware output escaping (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
- Input sanitization (`sanitize_text_field`, `absint`, `sanitize_key`).
- SQL query preparation with `$wpdb->prepare()`.
- Nonce and CSRF verification on all state-changing endpoints.
- Direct execution guards (`defined('ABSPATH') || exit;`).
- Automated Secret Scanning and SAST checks on all PRs.

See [`STRICT_AI_CHECKS.md`](STRICT_AI_CHECKS.md) and [`docs/ai/AI_SECURITY_CHECKLIST.md`](docs/ai/AI_SECURITY_CHECKLIST.md) for full technical requirements.
