<?php
/**
 * Email-only newsletter sign-up (no account needed). Double opt-in via MailPoet.
 *
 * @package CodesBlock
 *
 * @var array{variant?: string, email?: string} $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$codesblock_variant = isset( $args['variant'] ) && in_array( $args['variant'], array( 'band', 'inline', 'account' ), true ) ? $args['variant'] : 'inline';
$codesblock_email   = isset( $args['email'] ) ? sanitize_email( (string) $args['email'] ) : '';
$codesblock_heading = get_theme_mod( 'codesblock_newsletter_heading', __( 'Join the weekly build note', 'codesblock' ) );
$codesblock_copy    = get_theme_mod( 'codesblock_newsletter_copy', __( 'One practical engineering idea and one focused learning prompt, delivered without the noise.', 'codesblock' ) );
$codesblock_form_id = 'cb-newsletter-' . $codesblock_variant;
?>
<section class="cbx-news cbx-news-<?php echo esc_attr( $codesblock_variant ); ?>"<?php echo 'band' === $codesblock_variant ? ' id="newsletter"' : ''; ?> aria-labelledby="<?php echo esc_attr( $codesblock_form_id ); ?>-title">
	<div class="cbx-news-copy">
		<span class="cbx-news-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg></span>
		<div>
			<h2 id="<?php echo esc_attr( $codesblock_form_id ); ?>-title"><?php echo esc_html( 'account' === $codesblock_variant ? __( 'Weekly build note', 'codesblock' ) : $codesblock_heading ); ?></h2>
			<p><?php echo esc_html( 'account' === $codesblock_variant ? __( 'Get one practical engineering idea a week. Confirm from your inbox; unsubscribe from any email.', 'codesblock' ) : $codesblock_copy ); ?></p>
		</div>
	</div>
	<form class="cb-newsletter-form cbx-news-form" novalidate>
		<?php if ( $codesblock_email ) : ?>
			<input type="hidden" name="email" value="<?php echo esc_attr( $codesblock_email ); ?>">
		<?php else : ?>
			<label class="screen-reader-text" for="<?php echo esc_attr( $codesblock_form_id ); ?>-email"><?php esc_html_e( 'Email address', 'codesblock' ); ?></label>
			<input id="<?php echo esc_attr( $codesblock_form_id ); ?>-email" name="email" type="email" autocomplete="email" inputmode="email" placeholder="<?php esc_attr_e( 'you@example.com', 'codesblock' ); ?>" required>
		<?php endif; ?>
		<input class="cb-honeypot" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
		<button type="submit"><?php echo esc_html( $codesblock_email ? __( 'Subscribe with my email', 'codesblock' ) : __( 'Subscribe free', 'codesblock' ) ); ?></button>
		<p class="cb-form-feedback" role="status" aria-live="polite"></p>
	</form>
	<?php if ( 'account' !== $codesblock_variant ) : ?>
		<p class="cbx-news-note"><?php esc_html_e( 'No account needed · One email a week · Unsubscribe anytime', 'codesblock' ); ?></p>
	<?php endif; ?>
</section>
