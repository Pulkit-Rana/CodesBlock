# Interview Session Implementation Plan

> **For agentic workers:** Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox syntax for tracking.

**Goal:** Add a login-gated interview setup/workspace with one server-enforced free session and paid access through existing CodesBlock course memberships.

**Architecture:** Add theme-owned route/template, REST create-session endpoint, and private session records. Use the commerce plugin's existing paid-access and checkout helpers. A small frontend script handles setup submission and the honest AI-pending workspace state.

**Tech Stack:** WordPress PHP, REST API, custom post type, vanilla JavaScript, theme CSS, existing PHP test harness.

**Spec:** `docs/superpowers/specs/2026-10-26-interview-session-design.md`

## Global Constraints

- Paid access comes from `cbcommerce_user_has_paid_access()` and the existing membership checkout modal.
- Free users can create only one session; the server reserves the allowance atomically.
- Anonymous users cannot create or view a session.
- No AI capability may be represented as live before a provider is integrated.
- Do not modify the nested commerce plugin checkout, which contains pre-existing local deletions.

## Review Focus

- Anonymous create requests are rejected before a session is created.
- Two concurrent free-session requests cannot both consume the single allowance.
- Paid members can create subsequent sessions using the same course membership entitlement.
- A logged-in user cannot read another user's private session.
- Unknown track or difficulty values are rejected or normalized to safe defaults.

---

### Task 1: Session access rules

**Files:**
- Modify: `tests/unit/test-interview-access.php`
- Modify: `tests/run-tests.php`
- Modify: `functions.php`

- [x] Add tests for anonymous, first-free, exhausted-free, and paid access states and verify they fail.
- [x] Implement access-state helper, setup validation, ownership check, and unique per-account free-session reservation.
- [x] Run the full PHP test suite.

### Task 2: Private session endpoint and route

**Files:**
- Modify: `functions.php`
- Create: `page-interview.php`
- Create: `template-parts/interview-session.php`

- [x] Add coverage for access states, ownership, input validation, and single-claim reservation.
- [x] Register private interview session records and owner-checked REST create/read handlers.
- [x] Add interview page routing and render setup or workspace state.

### Task 3: Homepage entry and interface

**Files:**
- Modify: `front-page.php`
- Modify: `functions.php`
- Modify: `assets/css/polish.css`
- Create: `assets/css/interview-session.css`
- Create: `assets/js/interview-session.js`

- [x] Link the homepage practice feature to interview setup.
- [x] Add responsive setup and workspace UI, login return path, remaining-free-session messaging, shared paid membership modal link, and explicit AI-pending copy.
- [x] Verify PHP/JS syntax and the full test suite. Browser rendering remains unverified because the Local WordPress site is stopped.
