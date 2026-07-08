<?php
/**
 * Site header.
 *
 * @package CodesBlock
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'codesblock' ); ?></a>
<header class="site-header" data-site-header>
	<div class="container header-inner">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<span class="brand-mark">
    <img
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>"
        alt="<?php bloginfo( 'name' ); ?>"
        class="brand-logo"
    >
</span>

<span class="brand-copy">
    <strong><?php bloginfo( 'name' ); ?></strong>
    <small><?php bloginfo( 'description' ); ?></small>
</span>
			<?php endif; ?>
		</a>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" data-nav-toggle>
			<span></span>
			<span></span>
			<span></span>
		</button>
		<nav class="primary-nav" id="primary-menu" data-primary-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'fallback_cb'    => 'codesblock_default_menu',
				)
			);
			?>
		</nav>
		<div class="header-actions">
			<a class="header-link" href="<?php echo esc_url( wp_login_url() ); ?>">Login</a>
			<a class="header-button" href="#member">Become a member</a>
		</div>
	</div>
</header>
