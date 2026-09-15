# CodesBlock maintenance map

The site is split into small layers so everyday publishing does not require code changes.

## What to edit in WordPress

- **Articles:** Posts -> Add New. Use categories, tags, excerpt, featured image, and the CodesBlock visibility box.
- **Courses:** Courses -> Add New. Lessons use the normal editor; price, level, duration, outcomes, and curriculum use the Course setup box.
- **Homepage message and buttons:** Appearance -> Customize -> CodesBlock Homepage.
- **Logo and site name:** Appearance -> Customize -> Site Identity.
- **Footer copy and social links:** Appearance -> Customize -> CodesBlock Footer & Social.
- **Navigation:** Appearance -> Menus.
- **Membership plans, checkout, and Stripe:** Memberships in WordPress admin.
- **Newsletter subscribers:** MailPoet.
- **Google sign-in:** Settings -> Nextend Social Login.

## Custom code responsibilities

- `wp-content/plugins/codesblock-core`: Courses, editable course fields, featured/premium flags, and server-side protected previews. This remains active even if the theme changes.
- `wp-content/plugins/codesblock-commerce`: Connects PMPro, membership access, member login, newsletter consent, and learning progress.
- `wp-content/themes/codesblock`: Visual presentation only. `assets/css/polish.css` is the small final brand layer; the larger historical layout rules remain in `assets/css/main.css`.

## Lean, low-cost plugin stack

Keep the plugin list short and give every plugin one job:

1. Paid Memberships Pro: memberships, Stripe checkout, renewals, and account pages.
2. MailPoet: newsletters and subscriber management.
3. Nextend Social Login: optional Google member sign-in.
4. Highlighting Code Block: readable code examples in articles and course lessons.

Avoid adding a page builder unless a page genuinely cannot be built with the WordPress block editor. The CodesBlock homepage will also render a chosen static front page, so a block-built landing page can replace the theme homepage later without rebuilding the rest of the site.

## Before launch

1. Use HTTPS and connect Stripe in test mode.
2. Complete successful, failed, cancelled, and refunded checkout tests.
3. Verify a logged-out visitor receives only excerpts for paid courses and premium articles.
4. Configure email delivery and send a real test newsletter.
5. Configure Google sign-in only after the final domain is known.
6. Add a backup plugin or host-level daily backup before accepting payments.
7. Update WordPress and plugins on staging first, then take a fresh backup before production updates.

