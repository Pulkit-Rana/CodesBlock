<?php
/**
 * Global member registration, sign-in, and India-ready plan modal.
 *
 * @package CodesBlock
 */

if ( is_user_logged_in() ) {
	return;
}

$redirect_url = home_url( '/' );
$privacy_url  = get_privacy_policy_url();
$interests    = function_exists( 'cbcommerce_allowed_interests' )
	? cbcommerce_allowed_interests()
	: array(
		'ai-coding'     => __( 'AI & coding', 'codesblock' ),
		'interviews'    => __( 'Interview preparation', 'codesblock' ),
		'career-growth' => __( 'Career growth', 'codesblock' ),
	);

$pro_level      = function_exists( 'cbcommerce_level_id' ) && class_exists( 'PMPro_Membership_Level' ) ? new PMPro_Membership_Level( cbcommerce_level_id( 'pro' ) ) : false;
$annual_level   = function_exists( 'cbcommerce_level_id' ) && class_exists( 'PMPro_Membership_Level' ) ? new PMPro_Membership_Level( cbcommerce_level_id( 'pro_annual' ) ) : false;
$lifetime_level = function_exists( 'cbcommerce_level_id' ) && class_exists( 'PMPro_Membership_Level' ) ? new PMPro_Membership_Level( cbcommerce_level_id( 'lifetime' ) ) : false;
$pro_amount     = $pro_level ? (float) $pro_level->initial_payment : 999;
$annual_amount  = $annual_level ? (float) $annual_level->initial_payment : 8499;
$pro_price      = $pro_level && function_exists( 'pmpro_formatPrice' ) ? pmpro_formatPrice( $pro_amount ) : '₹999';
$annual_price   = $annual_level && function_exists( 'pmpro_formatPrice' ) ? pmpro_formatPrice( $annual_amount ) : '₹8,499';
$lifetime_price = $lifetime_level && function_exists( 'pmpro_formatPrice' ) ? pmpro_formatPrice( $lifetime_level->initial_payment ) : '₹19,999';
$annual_saving  = $pro_amount > 0 ? max( 0, (int) round( ( 1 - ( $annual_amount / ( $pro_amount * 12 ) ) ) * 100 ) ) : 0;
$payments_ready = function_exists( 'cbcommerce_payments_ready' ) && cbcommerce_payments_ready();
$india_ready    = function_exists( 'cbcommerce_india_payments_ready' ) && cbcommerce_india_payments_ready();
$providers      = array(
	'google' => array( 'label' => __( 'Google', 'codesblock' ), 'mark' => 'G' ),
);
?>
<div id="paywall-overlay" class="cb-member-overlay" aria-hidden="true" hidden>
	<div class="cb-member-backdrop" data-member-close></div>
	<section
		class="cb-member-modal"
		role="dialog"
		aria-modal="true"
		aria-labelledby="paywall-title"
		aria-describedby="paywall-description"
		tabindex="-1"
	>
		<button class="cb-member-close" type="button" data-member-close aria-label="<?php esc_attr_e( 'Close member dialog', 'codesblock' ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>

		<div class="cb-member-body">
			<div class="cb-auth-card">
				<div class="cb-auth-heading">
					<a class="cb-auth-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" tabindex="-1" aria-hidden="true">CB</a>
					<div>
						<span><?php esc_html_e( 'CodesBlock account', 'codesblock' ); ?></span>
						<h2 id="paywall-title"><?php esc_html_e( 'Your learning, saved.', 'codesblock' ); ?></h2>
						<p id="paywall-description"><?php esc_html_e( 'Create a free account in under a minute. Upgrade only when a paid course is useful.', 'codesblock' ); ?></p>
					</div>
				</div>

				<div class="cb-auth-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Account options', 'codesblock' ); ?>">
					<button type="button" class="cb-auth-tab is-active" role="tab" aria-selected="true" aria-controls="cb-register-panel" id="cb-register-tab" data-member-view="register"><?php esc_html_e( 'Create account', 'codesblock' ); ?></button>
					<button type="button" class="cb-auth-tab" role="tab" aria-selected="false" aria-controls="cb-signin-panel" id="cb-signin-tab" data-member-view="signin"><?php esc_html_e( 'Sign in', 'codesblock' ); ?></button>
				</div>

				<div class="cb-social-grid" aria-label="<?php esc_attr_e( 'Social account access', 'codesblock' ); ?>">
					<?php foreach ( $providers as $provider_id => $provider ) : ?>
						<?php $is_enabled = class_exists( 'NextendSocialLogin' ) && NextendSocialLogin::isProviderEnabled( $provider_id ); ?>
						<div class="cb-social-slot cb-social-<?php echo esc_attr( $provider_id ); ?><?php echo $is_enabled ? ' is-enabled' : ' is-unavailable'; ?>">
							<?php if ( $is_enabled && shortcode_exists( 'nextend_social_login' ) ) : ?>
								<?php echo do_shortcode( '[nextend_social_login provider="' . esc_attr( $provider_id ) . '" style="default" redirect="' . esc_url( $redirect_url ) . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<button type="button" disabled aria-label="<?php echo esc_attr( sprintf( __( '%s sign in is not configured yet', 'codesblock' ), $provider['label'] ) ); ?>">
									<span class="cb-provider-mark" aria-hidden="true">
										<svg viewBox="0 0 24 24" width="20" height="20"><path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.29v3.15C3.26 21.3 7.31 24 12 24z"/><path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.29C.47 8.21 0 10.05 0 12s.47 3.79 1.29 5.42l3.99-3.15z"/><path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.58l3.99 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/></svg>
									</span>
									<span><?php echo esc_html( sprintf( __( 'Continue with %s', 'codesblock' ), $provider['label'] ) ); ?></span>
								</button>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>

				<div id="cb-register-panel" class="cb-auth-panel is-active" role="tabpanel" aria-labelledby="cb-register-tab" data-member-panel="register">
					<div class="cb-auth-divider"><span><?php esc_html_e( 'or continue with email', 'codesblock' ); ?></span></div>
					<form class="cb-member-form" data-cb-action="register" novalidate>
						<div class="cb-form-pair">
							<div class="cb-form-row">
								<label for="cb-register-name"><?php esc_html_e( 'First name', 'codesblock' ); ?> <span><?php esc_html_e( 'optional', 'codesblock' ); ?></span></label>
								<input id="cb-register-name" name="first_name" type="text" autocomplete="given-name" maxlength="50" placeholder="<?php esc_attr_e( 'How should we greet you?', 'codesblock' ); ?>">
							</div>
							<div class="cb-form-row">
								<label for="cb-register-email"><?php esc_html_e( 'Email', 'codesblock' ); ?></label>
								<input id="cb-register-email" name="email" type="email" autocomplete="email" inputmode="email" placeholder="you@example.com" required>
							</div>
						</div>
						<div class="cb-form-row">
							<label for="cb-register-password"><?php esc_html_e( 'Password', 'codesblock' ); ?> <span><?php esc_html_e( '10+ characters', 'codesblock' ); ?></span></label>
							<div class="cb-password-wrap">
								<input id="cb-register-password" name="password" type="password" autocomplete="new-password" minlength="10" placeholder="<?php esc_attr_e( 'Create a secure password', 'codesblock' ); ?>" required>
								<button type="button" class="cb-password-toggle" aria-label="<?php esc_attr_e( 'Show password', 'codesblock' ); ?>" data-password-toggle><?php esc_html_e( 'Show', 'codesblock' ); ?></button>
							</div>
						</div>

						<details class="cb-personalize">
							<summary><?php esc_html_e( 'Personalize my recommendations', 'codesblock' ); ?> <span><?php esc_html_e( 'optional', 'codesblock' ); ?></span></summary>
							<fieldset class="cb-interest-fieldset">
								<legend><?php esc_html_e( 'What are you learning?', 'codesblock' ); ?></legend>
								<?php foreach ( $interests as $interest_id => $interest_label ) : ?>
									<label><input type="checkbox" name="interests[]" value="<?php echo esc_attr( $interest_id ); ?>"><span><?php echo esc_html( $interest_label ); ?></span></label>
								<?php endforeach; ?>
							</fieldset>
						</details>

						<label class="cb-consent-row">
							<input type="checkbox" name="marketing_consent" value="1">
							<span><?php esc_html_e( 'Send me the weekly practical learning email. Optional, and I can unsubscribe anytime.', 'codesblock' ); ?></span>
						</label>
						<input class="cb-honeypot" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
						<button class="cb-primary-submit" type="submit"><?php esc_html_e( 'Create my free account', 'codesblock' ); ?></button>
						<p class="cb-submit-note"><span aria-hidden="true">&#10003;</span><?php esc_html_e( 'No card required', 'codesblock' ); ?></p>
						<p class="cb-form-feedback" role="status" aria-live="polite"></p>
						<?php if ( $privacy_url ) : ?>
							<p class="cb-form-legal"><?php printf( wp_kses_post( __( 'By continuing, you acknowledge the <a href="%s">privacy policy</a>.', 'codesblock' ) ), esc_url( $privacy_url ) ); ?></p>
						<?php endif; ?>
					</form>
				</div>

				<div id="cb-signin-panel" class="cb-auth-panel" role="tabpanel" aria-labelledby="cb-signin-tab" data-member-panel="signin" hidden>
					<div class="cb-auth-divider"><span><?php esc_html_e( 'or continue with email', 'codesblock' ); ?></span></div>
					<form class="cb-member-form cb-signin-form" data-cb-action="login" novalidate>
						<div class="cb-form-row">
							<label for="cb-login-identity"><?php esc_html_e( 'Email or username', 'codesblock' ); ?></label>
							<input id="cb-login-identity" name="identity" type="text" autocomplete="username" placeholder="you@example.com" required>
						</div>
						<div class="cb-form-row">
							<div class="cb-label-split">
								<label for="cb-login-password"><?php esc_html_e( 'Password', 'codesblock' ); ?></label>
								<a href="<?php echo esc_url( wp_lostpassword_url( $redirect_url ) ); ?>"><?php esc_html_e( 'Forgot password?', 'codesblock' ); ?></a>
							</div>
							<div class="cb-password-wrap">
								<input id="cb-login-password" name="password" type="password" autocomplete="current-password" placeholder="<?php esc_attr_e( 'Your password', 'codesblock' ); ?>" required>
								<button type="button" class="cb-password-toggle" aria-label="<?php esc_attr_e( 'Show password', 'codesblock' ); ?>" data-password-toggle><?php esc_html_e( 'Show', 'codesblock' ); ?></button>
							</div>
						</div>
						<label class="cb-remember-row"><input type="checkbox" name="remember" value="1"><span><?php esc_html_e( 'Keep me signed in on this device', 'codesblock' ); ?></span></label>
						<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect_url ); ?>">
						<button class="cb-primary-submit" type="submit"><?php esc_html_e( 'Sign in', 'codesblock' ); ?></button>
						<p class="cb-form-feedback" role="status" aria-live="polite"></p>
					</form>
				</div>
			</div>

			<aside class="cb-plan-panel" aria-labelledby="cb-plan-title">
				<div class="cb-plan-heading">
					<span><?php esc_html_e( 'Membership', 'codesblock' ); ?></span>
					<h3 id="cb-plan-title"><?php esc_html_e( 'One payment. No surprise renewal.', 'codesblock' ); ?></h3>
					<p><?php esc_html_e( 'Start free, or choose a fixed access pass. Paid plans renew only when you decide.', 'codesblock' ); ?></p>
				</div>

				<div class="cb-free-plan">
					<div><span><?php esc_html_e( 'Starter', 'codesblock' ); ?></span><strong><?php esc_html_e( 'Free forever', 'codesblock' ); ?></strong></div>
					<ul role="list"><li><?php esc_html_e( 'Free courses', 'codesblock' ); ?></li><li><?php esc_html_e( 'Saved progress', 'codesblock' ); ?></li><li><?php esc_html_e( 'Member library', 'codesblock' ); ?></li></ul>
				</div>

				<div class="cb-plan-list">
					<article class="cb-plan-option">
						<div class="cb-plan-option-top"><span><?php esc_html_e( '30-day pass', 'codesblock' ); ?></span><strong><?php echo wp_kses_post( $pro_price ); ?></strong></div>
						<p><?php esc_html_e( 'Unlock every paid course for 30 days. Renew manually if you need more time.', 'codesblock' ); ?></p>
						<a href="<?php echo esc_url( function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'pro' ) : wp_registration_url() ); ?>"><?php echo esc_html( $payments_ready ? __( 'Choose 30 days', 'codesblock' ) : __( 'Get launch update', 'codesblock' ) ); ?></a>
					</article>
					<article class="cb-plan-option is-featured">
						<div class="cb-plan-badge"><?php echo esc_html( $annual_saving ? sprintf( __( 'Save %d%%', 'codesblock' ), $annual_saving ) : __( 'Best value', 'codesblock' ) ); ?></div>
						<div class="cb-plan-option-top"><span><?php esc_html_e( '1-year pass', 'codesblock' ); ?></span><strong><?php echo wp_kses_post( $annual_price ); ?></strong></div>
						<p><?php esc_html_e( 'A full year of courses, resources, certificates, and saved progress.', 'codesblock' ); ?></p>
						<a href="<?php echo esc_url( function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'pro_annual' ) : wp_registration_url() ); ?>"><?php echo esc_html( $payments_ready ? __( 'Choose 1 year', 'codesblock' ) : __( 'Get launch update', 'codesblock' ) ); ?></a>
					</article>
				</div>

				<div class="cb-lifetime-row">
					<div><span><?php esc_html_e( 'Founding lifetime', 'codesblock' ); ?><small><?php esc_html_e( 'One payment, permanent access', 'codesblock' ); ?></small></span><strong><?php echo wp_kses_post( $lifetime_price ); ?></strong></div>
					<a href="<?php echo esc_url( function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'lifetime' ) : wp_registration_url() ); ?>"><?php echo esc_html( $payments_ready ? __( 'View lifetime', 'codesblock' ) : __( 'Notify me', 'codesblock' ) ); ?></a>
				</div>

				<div class="cb-payment-preview<?php echo $india_ready ? ' is-ready' : ''; ?>">
					<div class="cb-payment-preview-heading">
						<span><?php esc_html_e( 'Pay your way', 'codesblock' ); ?></span>
						<small><?php echo esc_html( $india_ready ? __( 'Available at checkout', 'codesblock' ) : __( 'Gateway setup pending', 'codesblock' ) ); ?></small>
					</div>
					<div class="cb-payment-methods" aria-label="<?php esc_attr_e( 'Planned payment methods', 'codesblock' ); ?>">
						<span class="cb-payment-upi"><b>UPI</b><small><?php esc_html_e( 'GPay · PhonePe · BHIM', 'codesblock' ); ?></small></span>
						<span><b><?php esc_html_e( 'Cards', 'codesblock' ); ?></b><small><?php esc_html_e( 'Visa · Mastercard · RuPay', 'codesblock' ); ?></small></span>
					</div>
					<p>
						<span aria-hidden="true">&#10003;</span>
						<?php echo esc_html( $india_ready ? __( 'Secure gateway · Instant access after successful payment', 'codesblock' ) : __( 'Payment methods appear after gateway and webhook verification', 'codesblock' ) ); ?>
					</p>
				</div>

				<p class="cb-coupon-note">
					<?php if ( $payments_ready ) : ?>
						<strong><?php esc_html_e( 'New member offer:', 'codesblock' ); ?></strong> <?php esc_html_e( 'Use WELCOME25 to save 25% on the 30-day pass.', 'codesblock' ); ?>
					<?php else : ?>
						<strong><?php esc_html_e( 'Paid enrollment opens after gateway testing.', 'codesblock' ); ?></strong> <?php esc_html_e( 'Free membership is available now.', 'codesblock' ); ?>
					<?php endif; ?>
				</p>
			</aside>
		</div>
	</section>
</div>
