<?php
/**
 * Global member registration, sign-in, and plan modal.
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
$lifetime_level = function_exists( 'cbcommerce_level_id' ) && class_exists( 'PMPro_Membership_Level' ) ? new PMPro_Membership_Level( cbcommerce_level_id( 'lifetime' ) ) : false;
$pro_price      = $pro_level && function_exists( 'pmpro_formatPrice' ) ? pmpro_formatPrice( $pro_level->billing_amount ) : '$9';
$lifetime_price = $lifetime_level && function_exists( 'pmpro_formatPrice' ) ? pmpro_formatPrice( $lifetime_level->initial_payment ) : '$199';
$providers = array(
	'google' => array( 'label' => __( 'Google', 'codesblock' ), 'mark' => 'G' ),
);
?>
<div id="paywall-overlay" class="cb-member-overlay" hidden>
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

		<div class="cb-member-hero">
			<div>
				<span class="cb-member-kicker"><?php esc_html_e( 'Your CodesBlock account', 'codesblock' ); ?></span>
				<h2 id="paywall-title"><?php esc_html_e( 'Learn with a plan built for progress', 'codesblock' ); ?></h2>
				<p id="paywall-description"><?php esc_html_e( 'Save your learning, get tailored course recommendations, and choose paid access only when you need it.', 'codesblock' ); ?></p>
			</div>
			<div class="cb-trust-pill">
				<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 4.6-2.8 8.4-7 10-4.2-1.6-7-5.4-7-10V6l7-3z"/><path d="M9 12l2 2 4-5"/></svg>
				<?php esc_html_e( 'Private by default', 'codesblock' ); ?>
			</div>
		</div>

		<div class="cb-member-body">
			<div class="cb-auth-card">
				<div class="cb-auth-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Account options', 'codesblock' ); ?>">
					<button type="button" class="cb-auth-tab is-active" role="tab" aria-selected="true" aria-controls="cb-register-panel" id="cb-register-tab" data-member-view="register"><?php esc_html_e( 'Create account', 'codesblock' ); ?></button>
					<button type="button" class="cb-auth-tab" role="tab" aria-selected="false" aria-controls="cb-signin-panel" id="cb-signin-tab" data-member-view="signin"><?php esc_html_e( 'Sign in', 'codesblock' ); ?></button>
				</div>
				<div class="cb-social-grid" aria-label="<?php esc_attr_e( 'Google account access', 'codesblock' ); ?>">
					<?php foreach ( $providers as $provider_id => $provider ) : ?>
						<?php $is_enabled = class_exists( 'NextendSocialLogin' ) && NextendSocialLogin::isProviderEnabled( $provider_id ); ?>
						<div class="cb-social-slot cb-social-<?php echo esc_attr( $provider_id ); ?><?php echo $is_enabled ? ' is-enabled' : ' is-unavailable'; ?>">
							<?php if ( $is_enabled && shortcode_exists( 'nextend_social_login' ) ) : ?>
								<?php echo do_shortcode( '[nextend_social_login provider="' . esc_attr( $provider_id ) . '" style="default" redirect="' . esc_url( $redirect_url ) . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<button type="button" disabled aria-label="<?php echo esc_attr( sprintf( __( '%s sign in is not configured yet', 'codesblock' ), $provider['label'] ) ); ?>">
									<span class="cb-provider-mark" aria-hidden="true"><?php echo esc_html( $provider['mark'] ); ?></span>
									<span><?php echo esc_html( sprintf( __( 'Continue with %s', 'codesblock' ), $provider['label'] ) ); ?></span>
									<small><?php esc_html_e( 'Setup required', 'codesblock' ); ?></small>
								</button>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>

				<div id="cb-register-panel" class="cb-auth-panel is-active" role="tabpanel" aria-labelledby="cb-register-tab" data-member-panel="register">
					<div class="cb-auth-divider"><span><?php esc_html_e( 'or register with email', 'codesblock' ); ?></span></div>

					<form class="cb-member-form" data-cb-action="register" novalidate>
						<div class="cb-form-row">
							<label for="cb-register-name"><?php esc_html_e( 'First name', 'codesblock' ); ?> <span><?php esc_html_e( 'optional', 'codesblock' ); ?></span></label>
							<input id="cb-register-name" name="first_name" type="text" autocomplete="given-name" maxlength="50">
						</div>
						<div class="cb-form-row">
							<label for="cb-register-username"><?php esc_html_e( 'Username', 'codesblock' ); ?></label>
							<input id="cb-register-username" name="username" type="text" autocomplete="username" minlength="3" maxlength="60" required>
						</div>
						<div class="cb-form-row">
							<label for="cb-register-email"><?php esc_html_e( 'Email address', 'codesblock' ); ?></label>
							<input id="cb-register-email" name="email" type="email" autocomplete="email" inputmode="email" required>
						</div>
						<div class="cb-form-row">
							<label for="cb-register-password"><?php esc_html_e( 'Password', 'codesblock' ); ?> <span><?php esc_html_e( '10+ characters', 'codesblock' ); ?></span></label>
							<div class="cb-password-wrap">
								<input id="cb-register-password" name="password" type="password" autocomplete="new-password" minlength="10" required>
								<button type="button" class="cb-password-toggle" aria-label="<?php esc_attr_e( 'Show password', 'codesblock' ); ?>" data-password-toggle><?php esc_html_e( 'Show', 'codesblock' ); ?></button>
							</div>
						</div>

						<fieldset class="cb-interest-fieldset">
							<legend><?php esc_html_e( 'What should we tailor for you?', 'codesblock' ); ?> <span><?php esc_html_e( 'optional', 'codesblock' ); ?></span></legend>
							<?php foreach ( $interests as $interest_id => $interest_label ) : ?>
								<label><input type="checkbox" name="interests[]" value="<?php echo esc_attr( $interest_id ); ?>"><span><?php echo esc_html( $interest_label ); ?></span></label>
							<?php endforeach; ?>
						</fieldset>

						<label class="cb-consent-row">
							<input type="checkbox" name="marketing_consent" value="1">
							<span><?php esc_html_e( 'Send me the weekly newsletter and tailored course updates. I can unsubscribe anytime.', 'codesblock' ); ?></span>
						</label>
						<input class="cb-honeypot" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
						<button class="cb-primary-submit" type="submit"><?php esc_html_e( 'Create free account', 'codesblock' ); ?></button>
						<p class="cb-form-feedback" role="status" aria-live="polite"></p>
						<?php if ( $privacy_url ) : ?>
							<p class="cb-form-legal"><?php printf( wp_kses_post( __( 'By creating an account you agree to the site terms and acknowledge the <a href="%s">privacy policy</a>.', 'codesblock' ) ), esc_url( $privacy_url ) ); ?></p>
						<?php endif; ?>
					</form>
				</div>

				<div id="cb-signin-panel" class="cb-auth-panel" role="tabpanel" aria-labelledby="cb-signin-tab" data-member-panel="signin" hidden>
					<div class="cb-auth-divider"><span><?php esc_html_e( 'or sign in with username', 'codesblock' ); ?></span></div>
					<div class="cb-panel-heading">
						<h3><?php esc_html_e( 'Welcome back', 'codesblock' ); ?></h3>
						<p><?php esc_html_e( 'Use your username or email and your CodesBlock password.', 'codesblock' ); ?></p>
					</div>
					<form class="cb-member-form" data-cb-action="login" novalidate>
						<div class="cb-form-row">
							<label for="cb-login-identity"><?php esc_html_e( 'Username or email', 'codesblock' ); ?></label>
							<input id="cb-login-identity" name="identity" type="text" autocomplete="username" required>
						</div>
						<div class="cb-form-row">
							<div class="cb-label-split">
								<label for="cb-login-password"><?php esc_html_e( 'Password', 'codesblock' ); ?></label>
								<a href="<?php echo esc_url( wp_lostpassword_url( $redirect_url ) ); ?>"><?php esc_html_e( 'Forgot password?', 'codesblock' ); ?></a>
							</div>
							<div class="cb-password-wrap">
								<input id="cb-login-password" name="password" type="password" autocomplete="current-password" required>
								<button type="button" class="cb-password-toggle" aria-label="<?php esc_attr_e( 'Show password', 'codesblock' ); ?>" data-password-toggle><?php esc_html_e( 'Show', 'codesblock' ); ?></button>
							</div>
						</div>
						<label class="cb-remember-row"><input type="checkbox" name="remember" value="1"><span><?php esc_html_e( 'Keep me signed in on this device', 'codesblock' ); ?></span></label>
						<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect_url ); ?>">
						<button class="cb-primary-submit" type="submit"><?php esc_html_e( 'Sign in securely', 'codesblock' ); ?></button>
						<p class="cb-form-feedback" role="status" aria-live="polite"></p>
					</form>
				</div>
			</div>

			<aside class="cb-plan-panel" aria-labelledby="cb-plan-title">
				<div class="cb-plan-heading">
					<span><?php esc_html_e( 'Membership', 'codesblock' ); ?></span>
					<h3 id="cb-plan-title"><?php esc_html_e( 'Start free. Upgrade when it pays off.', 'codesblock' ); ?></h3>
					<p><?php esc_html_e( 'Transparent pricing, secure checkout, and no surprise renewals.', 'codesblock' ); ?></p>
				</div>

				<div class="cb-plan-list">
					<article class="cb-plan-option">
						<div><strong><?php esc_html_e( 'Starter', 'codesblock' ); ?></strong><span><?php esc_html_e( 'Free forever', 'codesblock' ); ?></span></div>
						<p><?php esc_html_e( 'Free courses, saved progress, and member updates.', 'codesblock' ); ?></p>
						<button type="button" data-focus-register><?php esc_html_e( 'Create free account', 'codesblock' ); ?></button>
					</article>
					<article class="cb-plan-option is-featured">
						<div><strong><?php esc_html_e( 'Pro', 'codesblock' ); ?></strong><span><b><?php echo wp_kses_post( $pro_price ); ?></b> <?php esc_html_e( '/ month', 'codesblock' ); ?></span></div>
						<p><?php esc_html_e( 'Every paid course, saved progress, and member-only learning updates.', 'codesblock' ); ?></p>
						<a href="<?php echo esc_url( function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'pro' ) : wp_registration_url() ); ?>"><?php esc_html_e( 'Choose Pro', 'codesblock' ); ?></a>
					</article>
					<article class="cb-plan-option">
						<div><strong><?php esc_html_e( 'Lifetime', 'codesblock' ); ?></strong><span><b><?php echo wp_kses_post( $lifetime_price ); ?></b> <?php esc_html_e( 'once', 'codesblock' ); ?></span></div>
						<p><?php esc_html_e( 'Permanent access to current and future courses.', 'codesblock' ); ?></p>
						<a href="<?php echo esc_url( function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'lifetime' ) : wp_registration_url() ); ?>"><?php esc_html_e( 'Choose Lifetime', 'codesblock' ); ?></a>
					</article>
				</div>

				<ul class="cb-plan-trust" role="list">
					<li><?php esc_html_e( 'Clear pricing before checkout', 'codesblock' ); ?></li>
					<li><?php esc_html_e( 'Cancel Pro anytime', 'codesblock' ); ?></li>
					<li><?php esc_html_e( 'Secure membership checkout', 'codesblock' ); ?></li>
				</ul>
			</aside>
		</div>
	</section>
</div>
