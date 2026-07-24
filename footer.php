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
			<p><?php echo esc_html( get_theme_mod( 'codesblock_footer_description', 'AI-assisted courses, interview guides, and practical articles built around real developer growth.' ) ); ?></p>
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
		<div id="newsletter" class="footer-newsletter-card">
			<h2><?php echo esc_html( get_theme_mod( 'codesblock_newsletter_heading', 'Join the weekly build note' ) ); ?></h2>
			<p><?php echo esc_html( get_theme_mod( 'codesblock_newsletter_copy', 'One practical engineering idea and one focused learning prompt, delivered without the noise.' ) ); ?></p>
			<form class="footer-form cb-newsletter-form" action="#" method="post" novalidate>
				<label class="screen-reader-text" for="footer-email">Email</label>
				<input id="footer-email" name="email" type="email" autocomplete="email" inputmode="email" placeholder="you@example.com" required>
				<input class="cb-honeypot" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
				<button type="submit">Join</button>
				<p class="cb-form-feedback" role="status" aria-live="polite"></p>
			</form>
			<p class="footer-form-note"><?php esc_html_e( 'Marketing email only. Confirm through your inbox; unsubscribe anytime.', 'codesblock' ); ?></p>
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
