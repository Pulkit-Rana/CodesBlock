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
<?php if ( get_theme_mod( 'codesblock_promo_enabled', true ) ) : ?>
	<div class="top-promo-bar" role="region" aria-label="<?php esc_attr_e( 'Current promotion', 'codesblock' ); ?>">
		<div class="top-promo-inner">
			<span class="promo-badge"><?php esc_html_e( 'Limited time', 'codesblock' ); ?></span>
			<?php
			$codesblock_promo_text = get_theme_mod( 'codesblock_promo_text', 'Practical courses and interview prep for working developers.' );
			if ( 'Flash sale: Get 60% off AI interview prep this week.' === $codesblock_promo_text ) {
				$codesblock_promo_text = 'Practical courses and interview prep for working developers.';
			}
			?>
			<p><?php echo esc_html( $codesblock_promo_text ); ?></p>
			<a href="<?php echo esc_url( get_theme_mod( 'codesblock_promo_cta_url', '#courses' ) ); ?>">
				<?php echo esc_html( get_theme_mod( 'codesblock_promo_cta_text', 'Claim offer' ) ); ?>
			</a>
		</div>
	</div>
<?php endif; ?>
<header class="site-header" data-site-header>
	<div class="header-inner header-inner-full">
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
			<?php
			$codesblock_is_admin_session = function_exists( 'cbcommerce_user_can_access_admin' )
				? cbcommerce_user_can_access_admin()
				: current_user_can( 'manage_options' );
			$codesblock_is_frontend_member = function_exists( 'cbcommerce_is_frontend_member' )
				? cbcommerce_is_frontend_member()
				: ( is_user_logged_in() && ! $codesblock_is_admin_session );
			?>
			<?php if ( $codesblock_is_frontend_member ) : ?>
				<?php
					$current_user = wp_get_current_user();
					$profile_url = function_exists( 'cbcommerce_member_profile_url' ) ? cbcommerce_member_profile_url() : home_url( '/#my-learning' );
				?>
				<a class="header-profile" href="<?php echo esc_url( $profile_url ); ?>">
					<?php echo get_avatar( $current_user->ID, 32, '', '', array( 'class' => 'header-profile-avatar' ) ); ?>
					<span><?php echo esc_html( $current_user->display_name ); ?></span>
				</a>
				<a class="header-button" href="<?php echo esc_url( home_url( '/#my-learning' ) ); ?>"><?php esc_html_e( 'My learning', 'codesblock' ); ?></a>
			<?php elseif ( $codesblock_is_admin_session ) : ?>
				<a class="header-link" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'WP Admin', 'codesblock' ); ?></a>
			<?php else : ?>
				<a class="header-link js-open-member" data-member-view="signin" href="#paywall-overlay" aria-haspopup="dialog" aria-controls="paywall-overlay"><?php esc_html_e( 'Sign in', 'codesblock' ); ?></a>
				<a class="header-button js-open-paywall" href="#paywall-overlay" aria-haspopup="dialog" aria-controls="paywall-overlay"><?php esc_html_e( 'Become a member', 'codesblock' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</header>
