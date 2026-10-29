# WordPress Managed Content Theme

## Intent

Keep this GitHub repository focused on the CodesBlock WordPress theme. Use it for occasional presentation and layout changes. Create and edit articles and courses in WordPress Admin, where WordPress and the separately installed plugins store their content and settings.

## Goals

- Keep theme templates that display WordPress-authored posts, courses, and lessons.
- Keep styling and front-end integrations needed to present content and call installed plugins.
- Remove theme-owned features that are outside the requested articles, courses, and site UI, including the interview-session application and its private session API.
- Remove course-specific landing copy, lesson outlines, lesson-count defaults, and slug-based UI branches so every course uses the same WordPress-driven template.
- Rewrite setup and maintenance documentation around installing WordPress, restoring WordPress data and media when migrating an existing site, installing/configuring plugins in Admin, and connecting GitHub to deploy only the theme.
- State clearly that GitHub deploys theme files only; it does not migrate WordPress content, accounts, plugin code, plugin settings, media, or secrets.

## Non-goals

- Do not remove or modify plugins installed through WordPress Admin.
- Do not store courses, articles, users, uploaded media, plugin source, or secrets in this repository.
- Do not replace the custom course/article UI with default WordPress styling.
- Do not alter membership, payment, login, or access rules owned by installed plugins.

## Approaches

1. **WordPress-managed content with the existing theme UI (selected):** retain the archive, article, course, and lesson presentation templates and their plugin integrations; remove duplicated one-off application/content code. This preserves the user's ability to update UI in GitHub while creating content in Admin.
2. **Default WordPress presentation:** remove the custom content templates and rely on standard WordPress layouts. This is smaller but conflicts with the requirement to use GitHub for UI updates.
3. **Keep the entire custom application in the theme:** retain interview session APIs and one-off course content. This leaves non-content behavior and course copy tied to code, contrary to the requested ownership model.

## Constraints and calls

- WordPress Admin is the source of truth for posts, course records, lesson records, plugin configuration, and site data.
- Course titles, summaries, thumbnails, metadata, syllabus, and lesson records are read from WordPress; the theme does not supply a sample course syllabus or course-specific landing copy.
- The repository is deployed to `wp-content/themes/codesblock`; it is not a full WordPress installation or migration archive.
- CodesBlock Core, CodesBlock Commerce, PMPro, MailPoet, Nextend, Knit Pay, and other plugin code are installed separately and are not tracked here. Theme code may call their public APIs to render the current UI.
- The user confirmed that the custom `/interview/` page and session API should be removed.
- Preserve the WordPress Customizer settings that let the site owner manage presentation copy and links without code changes.

## Documentation outcome

Refresh the root README, deployment guide, and maintenance guide to explain ownership and the setup/migration workflow. Remove documentation that exists only for the retired interview-session feature. Retain general security and contributor guidance, updating references where they describe removed functionality.
