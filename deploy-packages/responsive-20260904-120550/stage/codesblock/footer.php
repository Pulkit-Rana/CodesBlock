<?php
/**
 * Site footer.
 *
 * @package CodesBlock
 */
?>
<footer id="contact" class="site-footer">
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
		<div class="footer-contact-card">
			<p class="footer-kicker"><?php esc_html_e( 'Contact', 'codesblock' ); ?></p>
			<h2><?php esc_html_e( 'Questions about learning or access?', 'codesblock' ); ?></h2>
			<p><?php esc_html_e( 'For course access, interview guides, partnerships, or feedback, send the CodesBlock team a note.', 'codesblock' ); ?></p>
			<a class="footer-contact-button" href="mailto:hello@codesblock.com"><?php esc_html_e( 'Start a conversation', 'codesblock' ); ?><span aria-hidden="true">&rarr;</span></a>
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
