# CodesBlock commerce setup

This plugin is the small compatibility layer between the CodesBlock theme, Paid Memberships Pro, and Nextend Social Login. Course content remains a normal WordPress `course` post and is not deleted if this plugin is disabled.

## Day-to-day management

- Plans and members: **Memberships → Dashboard**
- Payment provider: **Memberships → Settings → Payment Gateway**
- Orders and receipts: **Memberships → Orders**
- Course content and pricing labels: **Courses → All Courses**
- Social providers: **Settings → Nextend Social Login**
- Newsletter subscribers and interest tags: **MailPoet → Subscribers**
- An individual member's consent and interests: **Users → Edit User → CodesBlock communication preferences**

The plan IDs are stored in the `codesblock_membership_levels` option. Theme templates use helper functions from this plugin instead of hard-coded IDs.

## Production launch checklist

1. Publish the site on its final HTTPS domain before creating OAuth callback URLs.
2. Connect Stripe in PMPro, start in Stripe test mode, and complete one successful and one failed-payment test.
3. Confirm the Stripe webhook reports successful deliveries before switching to live mode.
4. Configure and verify Google in Nextend Social Login.
5. Test account creation, returning-user login, cancellation, expired-card handling, refund handling, and paid-content lockout in a private browser window.
6. Keep WordPress core, PMPro, Nextend, and this plugin updated; take an off-site backup before updates.

The member modal stores an explicit marketing-consent flag and learning interests on the WordPress user. Consented members are added to MailPoet's newsletter list and tagged by interest. First-time social registrations complete the same preference step before their account is created.

Ordinary members stay on the front end: email/username and Google login send them to the homepage learning dashboard, their progress is stored per course, and the WordPress admin bar is hidden. If a member opens `/wp-admin`, WordPress signs out that member session and shows the default WordPress administrator login. The administrator login accepts accounts with the `manage_options` capability and does not display front-end social buttons. Administrators do not render as members in the theme header, profile, or learning dashboard.

Never paste live Stripe secrets or OAuth client secrets into theme files or commit them to source control. Store them only in the relevant plugin settings or production secret manager.
