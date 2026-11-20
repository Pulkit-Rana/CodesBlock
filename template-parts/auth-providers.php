<?php
/**
 * Social sign-in buttons (Google, GitHub) for the account dialog and My learning.
 *
 * @package CodesBlock
 *
 * @var array{redirect?: string} $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$codesblock_redirect  = isset( $args['redirect'] ) ? (string) $args['redirect'] : '';
$codesblock_providers = function_exists( 'codesblock_social_providers' ) ? codesblock_social_providers() : array();
$codesblock_icons     = array(
	'google' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M23.75 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.6a5.64 5.64 0 0 1-2.44 3.7v3.07h3.95c2.31-2.13 3.64-5.27 3.64-9.01z"/><path fill="#34A853" d="M12 24c3.3 0 6.07-1.09 8.1-2.96l-3.95-3.07c-1.1.74-2.5 1.18-4.15 1.18-3.19 0-5.89-2.15-6.86-5.05H1.06v3.17A12 12 0 0 0 12 24z"/><path fill="#FBBC05" d="M5.14 14.1a7.2 7.2 0 0 1 0-4.6V6.33H1.06a12 12 0 0 0 0 10.94l4.08-3.17z"/><path fill="#EA4335" d="M12 4.75c1.8 0 3.42.62 4.69 1.84l3.5-3.5A11.95 11.95 0 0 0 12 0 12 12 0 0 0 1.06 6.33l4.08 3.17C6.11 6.9 8.81 4.75 12 4.75z"/></svg>',
	'github' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 .5a11.5 11.5 0 0 0-3.64 22.41c.58.1.79-.25.79-.56v-2c-3.2.7-3.88-1.37-3.88-1.37-.52-1.33-1.28-1.69-1.28-1.69-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.29 1.19-3.09-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.17 1.18a11 11 0 0 1 5.77 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.8 1.19 1.83 1.19 3.09 0 4.42-2.7 5.39-5.26 5.68.41.36.78 1.06.78 2.14v3.17c0 .31.21.67.8.56A11.5 11.5 0 0 0 12 .5z"/></svg>',
);

?>
<label class="cbx-optin">
	<input type="checkbox" data-newsletter-optin value="1">
	<span><?php esc_html_e( 'Also send me the weekly build note. One email a week, unsubscribe anytime.', 'codesblock' ); ?></span>
</label>
<?php
if ( ! $codesblock_providers ) {
	return;
}
?>
<div class="cbx-providers">
	<?php foreach ( $codesblock_providers as $codesblock_provider_id => $codesblock_provider ) : ?>
		<a
			class="cbx-provider cbx-provider-<?php echo esc_attr( $codesblock_provider_id ); ?>"
			href="<?php echo esc_url( codesblock_social_login_url( $codesblock_provider['url'], $codesblock_redirect ) ); ?>"
			data-base-href="<?php echo esc_url( $codesblock_provider['url'] ); ?>"
			data-provider="<?php echo esc_attr( $codesblock_provider_id ); ?>"
			rel="nofollow"
		>
			<?php echo $codesblock_icons[ $codesblock_provider_id ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup. ?>
			<span><?php echo esc_html( sprintf( __( 'Continue with %s', 'codesblock' ), $codesblock_provider['label'] ) ); ?></span>
		</a>
	<?php endforeach; ?>
</div>
