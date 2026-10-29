# CodesBlock site maintenance

## Manage the site in WordPress Admin

- **Articles:** **Posts → Add New**. Write the title and article in the editor; set the excerpt, categories, tags, and featured image there.
- **Courses:** **Courses → Add New Course**. CodesBlock Core provides the course editor and course fields. The theme displays the saved course title, excerpt, thumbnail, metadata, syllabus, and lessons for every course; there are no course-specific landing pages to edit in code.
- **Lessons:** **Courses → Course Lessons**. Add and edit lesson content in WordPress.
- **Navigation and site identity:** **Appearance → Menus** and **Appearance → Customize**.
- **Memberships and orders:** **Memberships** (Paid Memberships Pro).
- **Payments:** **Knit Pay** and the configured gateway settings.
- **Newsletter:** **MailPoet**.
- **Social sign-in:** **Settings → Nextend Social Login**.
- **Analytics and SEO:** use the installed Site Kit, Clarity, and Yoast screens.

WordPress stores this content and configuration in its database. Uploaded images and other media live under `wp-content/uploads`. Back these up and migrate them separately from the GitHub theme repository.

## What belongs in GitHub

The repository contains the CodesBlock theme: templates that display WordPress-authored content, styles, images used by the theme, and front-end interactions. `archive-course.php`, `single-course.php`, `single-course_lesson.php`, `page-articles.php`, and `single.php` shape how WordPress content appears; they do not create or store course and article records.

The separately installed CodesBlock Core and CodesBlock Commerce plugins provide course data entry and membership/progress integrations. Their source and settings do not belong in this theme repository. Other plugins are installed and configured through WordPress Admin or Hostinger.

Use GitHub for occasional UI and layout changes. Edit the theme, review the change, and push to `main`; Hostinger deploys the tracked theme files. Use WordPress Admin for ordinary content publishing and site configuration.

## Migration reminders

For a new WordPress site, follow [DEPLOYMENT.md](DEPLOYMENT.md). When migrating an existing site, restore its database and uploads, install/activate its plugins, and restore plugin credentials/settings separately. Connecting GitHub deploys the theme; it does not copy that site data.

After the interview-session theme feature was retired, remove any saved `/interview/` page or menu link in WordPress Admin and save **Settings → Permalinks** once. Existing WordPress data is managed separately from theme files.
