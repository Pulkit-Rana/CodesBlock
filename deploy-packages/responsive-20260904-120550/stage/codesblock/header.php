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
<?php $codesblock_promo = function_exists( 'codesblock_get_promo_config' ) ? codesblock_get_promo_config() : array( 'visible' => false ); ?>
<?php if ( ! empty( $codesblock_promo['visible'] ) ) : ?>
	<?php
	$codesblock_dismiss_labels = array(
		'session' => __( 'Dismiss announcement until the next browser session', 'codesblock' ),
		'24'      => __( 'Dismiss announcement for 1 day', 'codesblock' ),
		'168'     => __( 'Dismiss announcement for 7 days', 'codesblock' ),
		'720'     => __( 'Dismiss announcement for 30 days', 'codesblock' ),
	);
	$codesblock_dismissal = isset( $codesblock_promo['dismissal'] ) ? (string) $codesblock_promo['dismissal'] : '168';
	$codesblock_bar_classes = array( 'top-promo-bar', 'promo-scheme-' . $codesblock_promo['scheme'] );
	if ( ! empty( $codesblock_promo['status'] ) ) {
		$codesblock_bar_classes[] = 'promo-status-' . sanitize_html_class( $codesblock_promo['status'] );
	}
	if ( empty( $codesblock_promo['mobile'] ) ) {
		$codesblock_bar_classes[] = 'promo-hide-mobile';
	}
	?>
	<div
		class="<?php echo esc_attr( implode( ' ', $codesblock_bar_classes ) ); ?>"
		role="region"
		aria-label="<?php esc_attr_e( 'CodesBlock announcement', 'codesblock' ); ?>"
		data-promo-campaign="<?php echo esc_attr( $codesblock_promo['campaign'] ); ?>"
		data-promo-dismissal="<?php echo esc_attr( $codesblock_dismissal ); ?>"
		data-promo-preview="<?php echo ! empty( $codesblock_promo['preview'] ) ? '1' : '0'; ?>"
		<?php if ( ! empty( $codesblock_promo['ends_at'] ) ) : ?>data-promo-expires="<?php echo esc_attr( (string) ( $codesblock_promo['ends_at'] * 1000 ) ); ?>"<?php endif; ?>
	>
		<span class="promo-glow promo-glow-one" aria-hidden="true"></span>
		<span class="promo-glow promo-glow-two" aria-hidden="true"></span>
		<div class="top-promo-inner">
			<?php if ( ! empty( $codesblock_promo['icon'] ) ) : ?>
				<span class="promo-icon" aria-hidden="true"><?php echo esc_html( $codesblock_promo['icon'] ); ?></span>
			<?php endif; ?>
			<div class="promo-message">
				<span class="promo-badge"><?php echo esc_html( $codesblock_promo['badge'] ); ?></span>
				<p><?php echo esc_html( $codesblock_promo['text'] ); ?></p>
			</div>
			<?php if ( ! empty( $codesblock_promo['countdown'] ) ) : ?>
				<div class="promo-countdown" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Campaign ends %s', 'codesblock' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $codesblock_promo['ends_at'], wp_timezone() ) ) ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
					<span><?php esc_html_e( 'Ends in', 'codesblock' ); ?></span>
					<strong data-promo-countdown aria-hidden="true">--</strong>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $codesblock_promo['cta'] ) && ! empty( $codesblock_promo['url'] ) ) : ?>
				<a
					class="promo-cta <?php echo esc_attr( implode( ' ', $codesblock_promo['classes'] ) ); ?>"
					href="<?php echo esc_url( $codesblock_promo['url'] ); ?>"
					data-promo-action="click"
					<?php if ( ! empty( $codesblock_promo['new_tab'] ) ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
					<?php if ( ! empty( $codesblock_promo['member_view'] ) ) : ?>data-member-view="<?php echo esc_attr( $codesblock_promo['member_view'] ); ?>" aria-haspopup="dialog" aria-controls="member-overlay"<?php endif; ?>
				>
					<span><?php echo esc_html( $codesblock_promo['cta'] ); ?></span>
					<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h11M11 6l4 4-4 4"></path></svg>
				</a>
			<?php endif; ?>
			<?php if ( ! empty( $codesblock_promo['dismissible'] ) ) : ?>
				<button class="promo-dismiss" type="button" data-promo-dismiss aria-label="<?php echo esc_attr( $codesblock_dismiss_labels[ $codesblock_dismissal ] ?? $codesblock_dismiss_labels['168'] ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
				</button>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
<header class="site-header" data-site-header>
	<div class="header-inner header-inner-full">
		<?php if ( has_custom_logo() ) : ?>
			<div class="brand">
				<?php the_custom_logo(); ?>
			</div>
		<?php else : ?>
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<span class="brand-mark">
					<img
						src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-cb-gloss-transparent.png' ); ?>"
						alt=""
						class="brand-logo"
						width="512"
						height="512"
					>
				</span>
				<span class="brand-copy">
					<strong><?php bloginfo( 'name' ); ?></strong>
					<small><?php bloginfo( 'description' ); ?></small>
				</span>
			</a>
		<?php endif; ?>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" aria-label="<?php esc_attr_e( 'Open navigation', 'codesblock' ); ?>" data-nav-toggle>
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
			<?php if ( get_theme_mod( 'codesblock_support_url', 'https://www.buymeacoffee.com/codesblock' ) ) : ?>
				<a class="header-coffee-btn" href="<?php echo esc_url( get_theme_mod( 'codesblock_support_url', 'https://www.buymeacoffee.com/codesblock' ) ); ?>" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'Buy Me a Coffee', 'codesblock' ); ?>" aria-label="<?php esc_attr_e( 'Buy Me a Coffee', 'codesblock' ); ?>">
					<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 7h12l-1-2H7L6 7z" fill="#ffffff" stroke="#1e293b"/><path d="M6.5 9l1.2 10.5a2 2 0 0 0 2 1.8h4.6a2 2 0 0 0 2-1.8L17.5 9H6.5z" fill="#ffdd00" stroke="#1e293b"/><rect x="5.5" y="7" width="13" height="2" rx="1" fill="#ffffff" stroke="#1e293b"/></svg>
				</a>
			<?php endif; ?>
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
					$profile_url = function_exists( 'cbcommerce_member_profile_url' ) ? cbcommerce_member_profile_url() : home_url( '/my-learning/#profile' );
					$learning_url = function_exists( 'cbcommerce_member_home_url' ) ? cbcommerce_member_home_url() : home_url( '/my-learning/' );
				?>
				<a class="header-profile" href="<?php echo esc_url( $profile_url ); ?>">
					<?php echo get_avatar( $current_user->ID, 32, '', '', array( 'class' => 'header-profile-avatar' ) ); ?>
					<span><?php echo esc_html( $current_user->display_name ); ?></span>
				</a>
				<a class="header-button header-learning-button" href="<?php echo esc_url( $learning_url ); ?>"><span aria-hidden="true">&#9654;</span><?php esc_html_e( 'My learning', 'codesblock' ); ?></a>
			<?php elseif ( $codesblock_is_admin_session ) : ?>
				<a class="header-link" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'WP Admin', 'codesblock' ); ?></a>
			<?php else : ?>
				<a class="header-link header-signin-button js-open-member" data-member-view="signin" href="#member-overlay" aria-haspopup="dialog" aria-controls="member-overlay"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg><?php esc_html_e( 'Sign in', 'codesblock' ); ?></a>
				<a class="header-button js-open-paywall" href="#paywall-overlay" aria-haspopup="dialog" aria-controls="paywall-overlay"><?php esc_html_e( 'Become a member', 'codesblock' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</header>
