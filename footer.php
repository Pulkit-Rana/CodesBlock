<?php
/**
 * Site footer.
 *
 * @package CodesBlock
 */
?>
<footer class="site-footer">
	<div class="container footer-grid">
		<div>
			<a class="brand footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="brand-mark">CB</span>
				<span class="brand-copy">
					<strong><?php bloginfo( 'name' ); ?></strong>
					<small><?php bloginfo( 'description' ); ?></small>
				</span>
			</a>
			<p>AI-assisted courses, interview guides, and practical articles built around real developer growth.</p>
			<div class="footer-socials" aria-label="Social media handles">
				<a href="https://www.youtube.com/@codesblock" target="_blank" rel="noreferrer"><span class="social-icon youtube-icon">YT</span>YouTube</a>
				<a href="https://www.instagram.com/codesblock" target="_blank" rel="noreferrer"><span class="social-icon instagram-icon">IG</span>Instagram</a>
				<a href="https://github.com/codesblock" target="_blank" rel="noreferrer"><span class="social-icon github-icon">GH</span>GitHub</a>
			</div>
		</div>
		<div>
			<h2>Explore</h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-menu',
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
		<div>
			<h2>Join In</h2>
			<p>Get one useful coding idea and one AI-assisted learning prompt in your inbox each week.</p>
			<form class="footer-form cb-newsletter-form" action="#" method="post" novalidate>
				<label class="screen-reader-text" for="footer-email">Email</label>
				<input id="footer-email" name="email" type="email" autocomplete="email" inputmode="email" placeholder="you@example.com" required>
				<input class="cb-honeypot" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
				<button type="submit">Join</button>
				<p class="cb-form-feedback" role="status" aria-live="polite"></p>
			</form>
			<div class="footer-actions">
				<?php
				$codesblock_footer_admin  = function_exists( 'cbcommerce_user_can_access_admin' ) ? cbcommerce_user_can_access_admin() : current_user_can( 'manage_options' );
				$codesblock_footer_member = function_exists( 'cbcommerce_is_frontend_member' ) ? cbcommerce_is_frontend_member() : ( is_user_logged_in() && ! $codesblock_footer_admin );
				?>
				<?php if ( $codesblock_footer_member ) : ?>
					<a href="<?php echo esc_url( home_url( '/#my-learning' ) ); ?>"><span class="action-icon">M</span>My learning</a>
					<a href="<?php echo esc_url( function_exists( 'cbcommerce_member_profile_url' ) ? cbcommerce_member_profile_url() : home_url( '/#my-learning' ) ); ?>"><span class="action-icon">P</span>Profile</a>
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><span class="action-icon">L</span>Sign out</a>
				<?php elseif ( $codesblock_footer_admin ) : ?>
					<a href="<?php echo esc_url( admin_url() ); ?>"><span class="action-icon">A</span>WP Admin</a>
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><span class="action-icon">L</span>Sign out</a>
				<?php else : ?>
					<a class="js-open-paywall" href="#paywall-overlay" aria-haspopup="dialog" aria-controls="paywall-overlay"><span class="action-icon">M</span>Become a member</a>
					<a class="js-open-member" data-member-view="signin" href="#paywall-overlay" aria-haspopup="dialog" aria-controls="paywall-overlay"><span class="action-icon">L</span>Sign in</a>
				<?php endif; ?>
				<a href="https://www.buymeacoffee.com/codesblock" target="_blank" rel="noreferrer"><span class="action-icon">C</span>Buy me coffee</a>
			</div>
		</div>
	</div>
	<div class="container footer-bottom">
		<span>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.</span>
		<span>Built for focused learning.</span>
	</div>
</footer>
<?php get_template_part( 'template-parts/paywall' ); ?>
<?php wp_footer(); ?>
</body>
</html>
