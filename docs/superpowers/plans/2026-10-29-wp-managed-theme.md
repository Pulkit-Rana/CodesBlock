# WordPress Managed Theme Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [x]`) syntax for tracking.

**Goal:** Keep the GitHub repository focused on the CodesBlock presentation theme while WordPress Admin owns articles, courses, and site data.

**Architecture:** Retain dynamic theme templates and public plugin integrations for courses, lessons, and articles. Remove the theme-owned interview-session application and course-specific hardcoded landing/curriculum content. Rewrite owner-facing setup documents to distinguish theme deployment from WordPress database, plugin, media, and secret migration.

**Tech Stack:** WordPress PHP theme, vanilla JavaScript/CSS, GitHub Actions, Markdown.

**Spec:** `docs/superpowers/specs/2026-10-29-wp-managed-theme-design.md`

## Global Constraints

- WordPress Admin remains the source of truth for posts, course records, lessons, plugin configuration, and site data.
- The repository is deployed to `wp-content/themes/codesblock`; it is not a full WordPress installation or migration archive.
- Do not remove or modify plugins installed through WordPress Admin.
- Preserve custom course/article presentation templates and public plugin integrations.
- Keep course titles, summaries, syllabus, lesson counts, and lesson records sourced from WordPress rather than slug-specific theme defaults.
- Remove the custom `/interview/` page and session API.
- Do not add or run tests as part of this task; use static diff and reference checks, plus any existing commit hook.

## Review Focus

- Article archive and single-post templates continue to render WordPress post content; preserve them and their existing queries.
- Course archive and generic course templates continue to read the CodesBlock Core post type and metadata; preserve plugin calls and `codesblock_get_course_outline()` used by lesson navigation.
- The retired `/interview/` route may remain in WordPress rewrite rules or a saved menu; document the required Admin cleanup and permalink refresh.
- PMPro, Nextend, MailPoet, and CodesBlock Commerce integrations remain intact; remove only interview-session claims that no longer work.
- GitHub theme deployment does not migrate site data; setup documentation must explicitly cover database/media restore and plugin setup separately.

---

### Task 1: Remove the theme-owned interview application

**Files:**
- Modify: `functions.php`
- Modify: `front-page.php`
- Modify: `template-parts/paywall.php`
- Delete: `inc-interview.php`
- Delete: `page-interview.php`
- Delete: `template-parts/interview-session.php`
- Delete: `assets/css/interview-session.css`
- Delete: `assets/js/interview-session.js`
- Delete: `tests/unit/test-interview-access.php`
- Delete: `docs/superpowers/plans/2026-10-26-interview-session.md`
- Delete: `docs/superpowers/specs/2026-10-26-interview-session-design.md`

**Interfaces:**
- Remove the theme include and all calls to interview route, CPT, REST, access, and session helpers.
- Keep unrelated `/stats` REST route because `assets/js/main.js` uses it; keep course-outline helper because lesson views use it.

- [x] Remove the interview include, route/session code, and interview-only test/docs.
- [x] Remove the homepage interview-session workspace and session-specific CTA; retain WP-driven course and article sections.
- [x] Remove interview-session-specific membership copy and verify no active source references remain to the retired session API/page; migration docs may name the old route for cleanup.

### Task 2: Make WordPress content the only course/article source

**Files:**
- Modify: `single-course.php`
- Modify: `template-parts/course-map.php`
- Modify: `archive-course.php`
- Modify: `assets/css/main.css`
- Modify: `assets/css/polish.css`
- Modify: `assets/css/responsive.css`
- Modify: `assets/css/typography.css`
- Modify: `tests/unit/test-asset-manifest.php`
- Modify: `inc.php`
- Modify: `functions.php`
- Delete: `single-course-crack-the-system-design-interview.php`
- Delete: `assets/css/system-design-course.css`
- Delete: `template-parts/system-design-course-visuals.php`
- Delete: `template-parts/system-design-hero-visual.php`
- Delete: `assets/images/system-design-interview-lab-cover.svg`
- Preserve the dynamic behavior in: `archive-course.php`, `single.php`, `page-articles.php`, `single-course_lesson.php`, and `template-parts/course-map.php`

**Interfaces:**
- Course create/edit screens and metadata continue to come from the separately installed CodesBlock Core plugin.
- Theme templates query WordPress posts and courses and render their saved titles, excerpts, content, and plugin metadata.

- [x] Remove slug-specific hardcoded course landing dispatch/content, sample curriculum, fixed lesson counts, and dedicated course visuals/styles so every course uses the same WordPress-driven template.
- [x] Remove or update only stale default navigation copy that points to the retired practice section; existing saved menus remain WordPress Admin data.
- [x] Keep `codesblock_get_course_outline()` and all course/lesson plugin calls used by the remaining templates.

### Task 3: Rewrite setup and ownership documentation

**Files:**
- Rewrite: `README.md`
- Rewrite: `DEPLOYMENT.md`
- Rewrite: `CODEBLOCK-MAINTENANCE.md`
- Modify: `CONTRIBUTING.md` only where it describes removed features or the old full-application workflow.
- Preserve: `SECURITY.md` and general AI/security checklists.

- [x] Document the fresh setup sequence: install WordPress, install and configure the required plugins in Admin, connect Hostinger GitHub deployment to `main` at `wp-content/themes/codesblock`, and activate the theme.
- [x] Document existing-site migration separately: restore the WordPress database and uploads, reinstall/activate plugins, restore plugin settings/secrets through Admin or hosting configuration, then deploy the theme.
- [x] State that GitHub moves theme files only and does not contain or migrate posts, courses, lessons, users, media, plugins, plugin settings, or secrets.
- [x] Include post-deploy Admin cleanup for any saved `/interview/` page/menu item and a permalink refresh to discard the retired rewrite rule.
- [x] Keep the docs short, accurate to current WordPress Admin and installed plugin responsibilities, and focused on the UI/content workflow.

### Task 4: Review the final repository diff

**Files:**
- Review: all changed and deleted files.

- [x] Confirm `git diff --check` is clean.
- [x] Confirm no active source references remain to the retired interview route/API/helpers or deleted one-off course template; migration documentation may name the old route.
- [x] Confirm plugin sources and WordPress content are still absent from Git and the retained course/article templates remain tracked.
- [ ] Review the complete diff and commit/push the approved cleanup to `main`.
