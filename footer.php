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
			<form class="footer-form" action="#" method="post">
				<label class="screen-reader-text" for="footer-email">Email</label>
				<input id="footer-email" type="email" placeholder="you@example.com">
				<button type="submit">Join</button>
			</form>
			<div class="footer-actions">
				<a href="#member"><span class="action-icon">M</span>Become a member</a>
				<a href="<?php echo esc_url( wp_login_url() ); ?>"><span class="action-icon">L</span>Login</a>
				<a href="https://www.buymeacoffee.com/codesblock" target="_blank" rel="noreferrer"><span class="action-icon">C</span>Buy me coffee</a>
			</div>
		</div>
	</div>
	<div class="container footer-bottom">
		<span>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.</span>
		<span>Built for focused learning.</span>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
