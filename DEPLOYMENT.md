# WordPress setup, migration, and theme deployment

## What this repository deploys

Hostinger connects `Pulkit-Rana/CodesBlock`, branch `main`, to:

`public_html/wp-content/themes/codesblock`

This GitHub repository is the theme source. It does not include WordPress core, plugin source, the database, courses, lessons, articles, users, uploads, plugin settings, or secrets. A GitHub deploy cannot migrate those items.

## Fresh WordPress setup

1. Create a WordPress site in Hostinger hPanel and sign in to `/wp-admin`.
2. Install and activate the site's plugins through WordPress Admin. The CodesBlock theme expects the separately installed CodesBlock Core and CodesBlock Commerce plugins for course records, membership integration, and progress. Install and configure the other plugins the site needs through their normal WordPress or Hostinger setup.
3. In Hostinger hPanel, open **Advanced → Git**, connect the GitHub repository, select `main`, and set the deployment directory to `public_html/wp-content/themes/codesblock`.
4. Deploy the branch. In WordPress Admin, open **Appearance → Themes** and activate CodesBlock.
5. Create articles, courses, and lessons in their WordPress Admin screens. Configure menus, Site Identity, homepage text, and footer details in WordPress Admin/Customizer.
6. Configure plugin settings and enter API credentials through the relevant plugin or Hostinger settings. Do not put secrets in GitHub.
7. Save **Settings → Permalinks** once after setup. Verify the homepage, `/articles/`, `/courses/`, a course lesson, and sign-in/checkout pages.

## Moving an existing WordPress site

If you want to keep the content and configuration from an existing site, install WordPress and restore the site data before treating it as a fresh empty site:

1. Take a complete backup of the source WordPress database and `wp-content/uploads`. Keep a separate record of installed plugins and their configuration.
2. Restore or migrate the database and uploads using the WordPress/Hostinger migration process. This is what carries posts, courses, lessons, users, menus, and plugin settings; this GitHub repository does not.
3. Install and activate the same plugins. Re-enter any credentials that are held in Hostinger or external provider settings, then verify each plugin connection in WordPress Admin.
4. Connect Hostinger Git deployment to this repository and the theme directory above. Deploy `main`, then activate the CodesBlock theme if it is not already active.
5. Open **Appearance → Menus** and remove any saved link to the retired `/interview/` page. Remove or unpublish that WordPress page if one exists. Open **Settings → Permalinks** and click **Save Changes** to refresh the old theme route.
6. Verify the homepage, articles, courses, lesson access, member pages, and checkout. Create a draft post and course in WordPress Admin to confirm content entry remains there.

Keep the source backup outside the public web directory. Never commit database exports, full site archives, `wp-config.php`, credentials, or plugin folders.

## Everyday workflow

- Publish or edit content, menus, media, plugin configuration, and accounts in WordPress Admin.
- Change page structure, visual styles, and front-end behavior in the theme repository.
- Push reviewed theme changes to `main`; Hostinger's existing GitHub integration deploys tracked theme files.
- To roll back a theme change, revert the Git commit and push the revert. Restoring WordPress content or plugin data is a separate backup operation.
