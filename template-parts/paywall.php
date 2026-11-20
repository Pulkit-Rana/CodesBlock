<?php
/**
 * Global account dialog (sign in = sign up) and the full-access plans dialog.
 *
 * @package CodesBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_admin_session    = function_exists( 'cbcommerce_user_can_access_admin' )
	? cbcommerce_user_can_access_admin()
	: current_user_can( 'manage_options' );
$has_paid_access     = function_exists( 'cbcommerce_user_has_paid_access' )
	? cbcommerce_user_has_paid_access()
	: $is_admin_session;
$show_auth_modal     = ! is_user_logged_in();
$show_purchase_modal = ! $has_paid_access;
$redirect_url        = function_exists( 'cbcommerce_member_home_url' ) ? cbcommerce_member_home_url() : home_url( '/my-learning/' );
$privacy_url         = get_privacy_policy_url();
$interests           = function_exists( 'cbcommerce_allowed_interests' )
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
$has_providers  = function_exists( 'codesblock_social_providers' ) && codesblock_social_providers();
$annual_monthly = $annual_level && function_exists( 'pmpro_formatPrice' ) ? pmpro_formatPrice( round( $annual_amount / 12 ) ) : '₹' . number_format_i18n( round( $annual_amount / 12 ) );
$checkout       = function ( $plan ) {
	return function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( $plan ) : wp_registration_url();
};
?>
<?php if ( $show_auth_modal ) : ?>
<div id="member-overlay" class="cb-member-overlay cbx-overlay" aria-hidden="true" hidden>
	<div class="cb-member-backdrop" data-member-close></div>
	<section class="cb-member-modal cbx-auth" role="dialog" aria-modal="true" aria-labelledby="member-title" aria-describedby="member-description" tabindex="-1" data-default-redirect="<?php echo esc_url( $redirect_url ); ?>">
		<button class="cb-member-close cbx-close" type="button" data-member-close aria-label="<?php esc_attr_e( 'Close', 'codesblock' ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>

		<aside class="cbx-auth-side" aria-hidden="true">
			<span class="cbx-brand">CB</span>
			<p class="cbx-side-kicker"><?php esc_html_e( 'One free account', 'codesblock' ); ?></p>
			<p class="cbx-side-title"><?php esc_html_e( 'Every course, every lab, one place to pick up where you left off.', 'codesblock' ); ?></p>
			<div class="cbx-side-card">
				<div class="cbx-side-card-top"><span><?php esc_html_e( 'Continue learning', 'codesblock' ); ?></span><b>62%</b></div>
				<strong><?php esc_html_e( 'Module 6 · Permission-aware RAG', 'codesblock' ); ?></strong>
				<i><span></span></i>
			</div>
			<ul class="cbx-side-list">
				<li><?php esc_html_e( 'Progress saved across devices', 'codesblock' ); ?></li>
				<li><?php esc_html_e( 'Free lessons in every course', 'codesblock' ); ?></li>
				<li><?php esc_html_e( 'Upgrade only when you want more', 'codesblock' ); ?></li>
			</ul>
		</aside>

		<div class="cbx-auth-main">
			<p class="cbx-kicker" data-auth-kicker data-default="<?php esc_attr_e( 'Welcome to CodesBlock', 'codesblock' ); ?>"><?php esc_html_e( 'Welcome to CodesBlock', 'codesblock' ); ?></p>
			<h2 id="member-title" data-default="<?php esc_attr_e( 'Sign in or create your free account', 'codesblock' ); ?>"><?php esc_html_e( 'Sign in or create your free account', 'codesblock' ); ?></h2>
			<p id="member-description" class="cbx-lead"><?php echo esc_html( $has_providers ? __( 'One click with Google or GitHub. New here? Your account is created automatically. No password, no card.', 'codesblock' ) : __( 'Use your email to sign in or create a free account. No card needed.', 'codesblock' ) ); ?></p>

			<?php get_template_part( 'template-parts/auth-providers', null, array( 'redirect' => $redirect_url ) ); ?>

			<details class="cbx-email"<?php echo $has_providers ? '' : ' open'; ?>>
				<summary><span><?php esc_html_e( 'Continue with email instead', 'codesblock' ); ?></span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></summary>

				<div class="cbx-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Email account options', 'codesblock' ); ?>">
					<button type="button" class="cbx-tab is-active" role="tab" aria-selected="true" aria-controls="cb-signin-panel" id="cb-signin-tab" data-member-view="signin"><?php esc_html_e( 'Sign in', 'codesblock' ); ?></button>
					<button type="button" class="cbx-tab" role="tab" aria-selected="false" aria-controls="cb-register-panel" id="cb-register-tab" data-member-view="register"><?php esc_html_e( 'Create account', 'codesblock' ); ?></button>
				</div>

				<div id="cb-signin-panel" class="cbx-panel is-active" role="tabpanel" aria-labelledby="cb-signin-tab" data-member-panel="signin">
					<form class="cb-member-form cbx-form" data-cb-action="login" novalidate>
						<label for="cb-login-identity"><?php esc_html_e( 'Email', 'codesblock' ); ?></label>
						<input id="cb-login-identity" name="identity" type="text" autocomplete="username" placeholder="you@example.com" required>
						<div class="cbx-label-row"><label for="cb-login-password"><?php esc_html_e( 'Password', 'codesblock' ); ?></label><a href="<?php echo esc_url( wp_lostpassword_url( $redirect_url ) ); ?>"><?php esc_html_e( 'Forgot?', 'codesblock' ); ?></a></div>
						<div class="cb-password-wrap cbx-password">
							<input id="cb-login-password" name="password" type="password" autocomplete="current-password" required>
							<button type="button" class="cb-password-toggle" aria-label="<?php esc_attr_e( 'Show password', 'codesblock' ); ?>" data-password-toggle><?php esc_html_e( 'Show', 'codesblock' ); ?></button>
						</div>
						<label class="cbx-check"><input type="checkbox" name="remember" value="1" checked><span><?php esc_html_e( 'Keep me signed in', 'codesblock' ); ?></span></label>
						<button class="cbx-submit" type="submit"><?php esc_html_e( 'Sign in', 'codesblock' ); ?></button>
						<p class="cb-form-feedback" role="status" aria-live="polite"></p>
					</form>
				</div>

				<div id="cb-register-panel" class="cbx-panel" role="tabpanel" aria-labelledby="cb-register-tab" data-member-panel="register" hidden>
					<form class="cb-member-form cbx-form" data-cb-action="register" novalidate>
						<label for="cb-register-name"><?php esc_html_e( 'First name', 'codesblock' ); ?></label>
						<input id="cb-register-name" name="first_name" type="text" autocomplete="given-name" maxlength="50">
						<label for="cb-register-email"><?php esc_html_e( 'Email', 'codesblock' ); ?></label>
						<input id="cb-register-email" name="email" type="email" autocomplete="email" inputmode="email" placeholder="you@example.com" required>
						<label for="cb-register-password"><?php esc_html_e( 'Password', 'codesblock' ); ?> <small><?php esc_html_e( '10+ characters', 'codesblock' ); ?></small></label>
						<div class="cb-password-wrap cbx-password">
							<input id="cb-register-password" name="password" type="password" autocomplete="new-password" minlength="10" required>
							<button type="button" class="cb-password-toggle" aria-label="<?php esc_attr_e( 'Show password', 'codesblock' ); ?>" data-password-toggle><?php esc_html_e( 'Show', 'codesblock' ); ?></button>
						</div>
						<label class="cbx-check"><input type="checkbox" name="marketing_consent" value="1"><span><?php esc_html_e( 'Also send me the weekly build note (optional)', 'codesblock' ); ?></span></label>
						<input class="cb-honeypot" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
						<button class="cbx-submit" type="submit"><?php esc_html_e( 'Create free account', 'codesblock' ); ?></button>
						<p class="cb-form-feedback" role="status" aria-live="polite"></p>
					</form>
				</div>
			</details>

			<p class="cbx-legal">
				<?php esc_html_e( 'Just here to read? Articles never need an account.', 'codesblock' ); ?>
				<?php if ( $privacy_url ) : ?>
					<a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Privacy policy', 'codesblock' ); ?></a>
				<?php endif; ?>
			</p>
		</div>
	</section>
</div>
<?php endif; ?>

<?php if ( $show_purchase_modal ) : ?>
<div id="paywall-overlay" class="cb-member-overlay cbx-overlay" aria-hidden="true" hidden>
	<div class="cb-member-backdrop" data-member-close></div>
	<section class="cb-member-modal cbx-pricing" role="dialog" aria-modal="true" aria-labelledby="paywall-title" aria-describedby="paywall-description" tabindex="-1">
		<button class="cb-member-close cbx-close" type="button" data-member-close aria-label="<?php esc_attr_e( 'Close plans', 'codesblock' ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>

		<header class="cbx-pricing-head">
			<p class="cbx-kicker"><?php esc_html_e( 'CodesBlock full access', 'codesblock' ); ?></p>
			<h2 id="paywall-title"><?php esc_html_e( 'Unlock every course, lab and mock interview', 'codesblock' ); ?></h2>
			<p id="paywall-description" class="cbx-lead"><?php esc_html_e( 'One upfront payment. No auto-renewal, no surprise charges. Renew only if you want more time.', 'codesblock' ); ?></p>
		</header>

		<div class="cbx-plans">
			<article class="cbx-plan">
				<h3><?php esc_html_e( '30-day pass', 'codesblock' ); ?></h3>
				<p class="cbx-plan-for"><?php esc_html_e( 'For a focused interview sprint', 'codesblock' ); ?></p>
				<p class="cbx-price"><?php echo wp_kses_post( $pro_price ); ?><small><?php esc_html_e( 'once', 'codesblock' ); ?></small></p>
				<ul role="list">
					<li><?php esc_html_e( 'All paid courses for 30 days', 'codesblock' ); ?></li>
					<li><?php esc_html_e( 'Labs, drills and mock interviews', 'codesblock' ); ?></li>
					<li><?php esc_html_e( 'Progress saved', 'codesblock' ); ?></li>
				</ul>
				<a class="cbx-plan-cta" href="<?php echo esc_url( $checkout( 'pro' ) ); ?>"><?php echo esc_html( $payments_ready ? __( 'Get 30 days', 'codesblock' ) : __( 'Get launch update', 'codesblock' ) ); ?></a>
			</article>
			<article class="cbx-plan is-featured">
				<span class="cbx-plan-badge"><?php echo esc_html( $annual_saving ? sprintf( __( 'Best value · save %d%%', 'codesblock' ), $annual_saving ) : __( 'Best value', 'codesblock' ) ); ?></span>
				<h3><?php esc_html_e( '1-year pass', 'codesblock' ); ?></h3>
				<p class="cbx-plan-for"><?php esc_html_e( 'For building skills that compound', 'codesblock' ); ?></p>
				<p class="cbx-price"><?php echo wp_kses_post( $annual_price ); ?><small><?php echo wp_kses_post( sprintf( __( '≈ %s / month', 'codesblock' ), $annual_monthly ) ); ?></small></p>
				<ul role="list">
					<li><?php esc_html_e( 'All paid courses for 12 months', 'codesblock' ); ?></li>
					<li><?php esc_html_e( 'Every new course and lesson as it launches', 'codesblock' ); ?></li>
					<li><?php esc_html_e( 'Labs, drills and mock interviews', 'codesblock' ); ?></li>
				</ul>
				<a class="cbx-plan-cta" href="<?php echo esc_url( $checkout( 'pro_annual' ) ); ?>"><?php echo esc_html( $payments_ready ? __( 'Get 1 year', 'codesblock' ) : __( 'Get launch update', 'codesblock' ) ); ?></a>
			</article>
			<article class="cbx-plan">
				<h3><?php esc_html_e( 'Founding lifetime', 'codesblock' ); ?></h3>
				<p class="cbx-plan-for"><?php esc_html_e( 'Pay once, keep access', 'codesblock' ); ?></p>
				<p class="cbx-price"><?php echo wp_kses_post( $lifetime_price ); ?><small><?php esc_html_e( 'once', 'codesblock' ); ?></small></p>
				<ul role="list">
					<li><?php esc_html_e( 'Permanent access to paid courses', 'codesblock' ); ?></li>
					<li><?php esc_html_e( 'Future courses included', 'codesblock' ); ?></li>
					<li><?php esc_html_e( 'Founding-member price', 'codesblock' ); ?></li>
				</ul>
				<a class="cbx-plan-cta" href="<?php echo esc_url( $checkout( 'lifetime' ) ); ?>"><?php echo esc_html( $payments_ready ? __( 'Get lifetime', 'codesblock' ) : __( 'Notify me', 'codesblock' ) ); ?></a>
			</article>
		</div>

		<footer class="cbx-pricing-foot">
			<div class="cbx-pay">
				<span><b>UPI</b> GPay · PhonePe · BHIM</span>
				<span><b><?php esc_html_e( 'Cards', 'codesblock' ); ?></b> Visa · Mastercard · RuPay</span>
				<span class="cbx-secure"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><?php echo esc_html( $india_ready ? __( 'Secure checkout · instant access', 'codesblock' ) : __( 'Payment methods appear at checkout', 'codesblock' ) ); ?></span>
			</div>
			<?php if ( $payments_ready ) : ?>
				<p class="cbx-coupon"><?php echo wp_kses_post( __( 'New here? Use <b>WELCOME25</b> for 25% off the 30-day pass.', 'codesblock' ) ); ?></p>
			<?php endif; ?>
			<?php if ( ! is_user_logged_in() ) : ?>
				<p class="cbx-free-note"><?php esc_html_e( 'Not ready yet?', 'codesblock' ); ?> <button type="button" class="cbx-linklike js-open-member" data-member-view="register"><?php esc_html_e( 'Start free', 'codesblock' ); ?></button> <?php esc_html_e( '— free lessons in every course, no card.', 'codesblock' ); ?></p>
			<?php endif; ?>
		</footer>
	</section>
</div>
<?php endif; ?>
