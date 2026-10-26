<?php
/** Interview setup and private session workspace. */

$is_logged_in = is_user_logged_in();
$user_id      = get_current_user_id();
$is_paid      = $is_logged_in && codesblock_interview_user_has_paid_access( $user_id );
$free_used    = $is_logged_in && codesblock_interview_free_session_used( $user_id );
$access_state = codesblock_interview_access_state( $is_logged_in, $is_paid, $free_used );
$tracks       = codesblock_interview_tracks();
$free_session_id = $is_logged_in ? codesblock_interview_free_session_id( $user_id ) : 0;
$free_session_post = $free_session_id ? get_post( $free_session_id ) : null;
$can_resume_free_session = codesblock_interview_session_belongs_to_user( $free_session_post, $user_id );
$session_id   = isset( $_GET['session'] ) ? absint( wp_unslash( $_GET['session'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only ID, owner checked below.
$session      = $session_id ? get_post( $session_id ) : null;
$owns_session = codesblock_interview_session_belongs_to_user( $session, $user_id );
$login_url    = wp_login_url( home_url( '/interview/' ) );
?>
<main id="main" class="interview-page">
	<section class="interview-shell">
		<div class="interview-heading">
			<a class="interview-back-link" href="<?php echo esc_url( home_url( '/#practice' ) ); ?>"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Interview practice', 'codesblock' ); ?></a>
			<p class="eyebrow"><?php esc_html_e( 'CodesBlock interview lab', 'codesblock' ); ?></p>
			<h1><?php echo $owns_session ? esc_html__( 'Your interview workspace', 'codesblock' ) : esc_html__( 'Make your next interview count.', 'codesblock' ); ?></h1>
			<p><?php esc_html_e( 'Work through a focused prompt, shape your reasoning, and get ready to defend your choices.', 'codesblock' ); ?></p>
		</div>

		<?php if ( ! $is_logged_in ) : ?>
			<section class="interview-gate" aria-labelledby="interview-login-title">
				<div class="interview-gate-icon" aria-hidden="true">01</div>
				<div><span class="interview-kicker"><?php esc_html_e( 'Your first session is free', 'codesblock' ); ?></span><h2 id="interview-login-title"><?php esc_html_e( 'Sign in to start your private session', 'codesblock' ); ?></h2><p><?php esc_html_e( 'Your workspace is saved to your account. Create a free account or sign in to begin.', 'codesblock' ); ?></p></div>
				<div class="interview-gate-actions"><a class="button button-primary" href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Sign in to start', 'codesblock' ); ?></a><a class="interview-text-link" href="<?php echo esc_url( add_query_arg( 'redirect_to', home_url( '/interview/' ), wp_registration_url() ) ); ?>"><?php esc_html_e( 'Create a free account', 'codesblock' ); ?></a></div>
			</section>
		<?php elseif ( $session_id && ! $owns_session ) : ?>
			<section class="interview-gate" role="status"><div><span class="interview-kicker"><?php esc_html_e( 'Private workspace', 'codesblock' ); ?></span><h2><?php esc_html_e( 'We couldn’t find that session', 'codesblock' ); ?></h2><p><?php esc_html_e( 'Sessions are private to the account that created them. Start a new session from your workspace.', 'codesblock' ); ?></p></div><a class="button button-primary" href="<?php echo esc_url( home_url( '/interview/' ) ); ?>"><?php esc_html_e( 'Back to setup', 'codesblock' ); ?></a></section>
		<?php elseif ( $owns_session ) : ?>
			<?php
			$session_track = get_post_meta( $session_id, '_cb_interview_track', true );
			$session_level = get_post_meta( $session_id, '_cb_interview_difficulty', true );
			$session_prompt = get_post_meta( $session_id, '_cb_interview_prompt', true );
			$track_label = isset( $tracks[ $session_track ] ) ? $tracks[ $session_track ]['label'] : __( 'Interview practice', 'codesblock' );
			?>
			<section class="interview-workspace" data-interview-session="<?php echo esc_attr( (string) $session_id ); ?>">
				<div class="interview-session-bar"><div><span class="session-live-dot"></span><span><?php esc_html_e( 'SESSION WORKSPACE', 'codesblock' ); ?></span></div><a href="<?php echo esc_url( home_url( '/interview/' ) ); ?>"><?php esc_html_e( 'Exit workspace', 'codesblock' ); ?></a></div>
				<div class="interview-session-meta"><span><?php echo esc_html( $track_label ); ?></span><span><?php echo esc_html( ucfirst( $session_level ) ); ?> level</span><span><?php esc_html_e( 'Your first session', 'codesblock' ); ?></span></div>
				<div class="interview-workspace-grid">
					<article class="interview-challenge">
						<p class="interview-kicker"><?php esc_html_e( 'THE PROMPT', 'codesblock' ); ?></p>
						<h2><?php echo esc_html( $track_label ); ?></h2>
						<p class="interview-prompt-copy"><?php echo esc_html( $session_prompt ); ?></p>
						<div class="interview-coach-note"><span aria-hidden="true">✦</span><p><strong><?php esc_html_e( 'A note on this first phase', 'codesblock' ); ?></strong><br><?php esc_html_e( 'Your prompt and private notes are ready. The live AI interviewer and automated feedback will connect in phase two.', 'codesblock' ); ?></p></div>
					</article>
					<aside class="interview-draft-panel" aria-labelledby="interview-draft-title">
						<div class="interview-draft-heading"><div><p class="interview-kicker"><?php esc_html_e( 'YOUR THINKING', 'codesblock' ); ?></p><h2 id="interview-draft-title"><?php esc_html_e( 'Build your answer', 'codesblock' ); ?></h2></div><span class="draft-save-status" data-draft-status><?php esc_html_e( 'Saved on this device', 'codesblock' ); ?></span></div>
						<label class="screen-reader-text" for="interview-draft"><?php esc_html_e( 'Private interview notes', 'codesblock' ); ?></label>
						<textarea id="interview-draft" data-interview-draft placeholder="<?php esc_attr_e( 'Start with the questions you would clarify, then map the main components and the tradeoffs you want to discuss…', 'codesblock' ); ?>"></textarea>
						<div class="interview-draft-footer"><span><?php esc_html_e( 'Your draft stays in this browser for now.', 'codesblock' ); ?></span><span data-draft-count>0 words</span></div>
					</aside>
				</div>
				<div class="interview-phase-next"><span>02</span><div><strong><?php esc_html_e( 'Next: practice with an AI interviewer', 'codesblock' ); ?></strong><p><?php esc_html_e( 'A conversational interviewer and tailored feedback are the next integration for this workspace.', 'codesblock' ); ?></p></div><span class="phase-status"><?php esc_html_e( 'Coming next', 'codesblock' ); ?></span></div>
			</section>
		<?php elseif ( 'membership_required' === $access_state ) : ?>
			<section class="interview-gate interview-upgrade-gate" aria-labelledby="interview-upgrade-title">
				<div class="interview-gate-icon interview-lock-icon" aria-hidden="true">&#10003;</div>
				<div><span class="interview-kicker"><?php esc_html_e( 'Free session complete', 'codesblock' ); ?></span><h2 id="interview-upgrade-title"><?php esc_html_e( 'Ready for another round?', 'codesblock' ); ?></h2><p><?php esc_html_e( 'Choose the same CodesBlock membership used for courses to unlock more interview sessions alongside your learning library.', 'codesblock' ); ?></p></div>
				<div class="interview-gate-actions">
					<?php if ( $can_resume_free_session ) : ?><a class="button button-secondary" href="<?php echo esc_url( add_query_arg( 'session', $free_session_id, home_url( '/interview/' ) ) ); ?>"><?php esc_html_e( 'Resume your free session', 'codesblock' ); ?></a><?php endif; ?>
					<a class="button button-primary js-open-paywall" href="#paywall-overlay" aria-haspopup="dialog" aria-controls="paywall-overlay"><?php esc_html_e( 'View membership plans', 'codesblock' ); ?></a>
				</div>
			</section>
		<?php else : ?>
			<section class="interview-setup-layout">
				<div class="interview-setup-main">
					<div class="interview-step-label"><span>01</span><span><?php esc_html_e( 'SET UP YOUR SESSION', 'codesblock' ); ?></span></div>
					<h2><?php esc_html_e( 'Choose what you want to practice.', 'codesblock' ); ?></h2>
					<p class="interview-setup-lede"><?php esc_html_e( 'Pick a track and level. You’ll get a focused prompt and a private place to shape your response.', 'codesblock' ); ?></p>
					<form class="interview-setup-form" data-interview-setup>
						<label for="interview-track"><?php esc_html_e( 'Interview track', 'codesblock' ); ?></label>
						<div class="interview-track-options">
							<?php $track_index = 0; foreach ( $tracks as $track_key => $track ) : ++$track_index; ?>
								<label class="interview-track-option<?php echo 1 === $track_index ? ' is-selected' : ''; ?>"><input type="radio" name="track" value="<?php echo esc_attr( $track_key ); ?>" <?php checked( 1 === $track_index ); ?>><span class="track-option-icon" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $track_index ) ); ?></span><span><strong><?php echo esc_html( $track['label'] ); ?></strong><small><?php echo esc_html( array( 'system-design' => __( 'Architecture, scale, and tradeoffs', 'codesblock' ), 'ai-engineering' => __( 'RAG, agents, and production AI', 'codesblock' ), 'behavioral' => __( 'Ownership, conflict, and leadership', 'codesblock' ) )[ $track_key ] ); ?></small></span><span class="track-option-check" aria-hidden="true">&#10003;</span></label>
							<?php endforeach; ?>
						</div>
						<label for="interview-difficulty"><?php esc_html_e( 'Experience level', 'codesblock' ); ?></label>
						<select id="interview-difficulty" name="difficulty"><option value="foundation"><?php esc_html_e( 'Foundation — build confidence with the basics', 'codesblock' ); ?></option><option value="intermediate" selected><?php esc_html_e( 'Intermediate — practice clear, structured thinking', 'codesblock' ); ?></option><option value="advanced"><?php esc_html_e( 'Advanced — defend deeper technical tradeoffs', 'codesblock' ); ?></option></select>
						<p class="interview-form-error" data-interview-error role="alert" hidden></p>
						<button class="button button-primary interview-start-button" type="submit"><span><?php esc_html_e( 'Start my free session', 'codesblock' ); ?></span><span aria-hidden="true">&rarr;</span></button>
						<p class="interview-form-footnote"><span aria-hidden="true">&#128274;</span><?php esc_html_e( 'Private to your account · No payment details required', 'codesblock' ); ?></p>
					</form>
				</div>
				<aside class="interview-included-card">
					<div class="included-card-top"><span class="included-star" aria-hidden="true">✦</span><span><?php esc_html_e( 'YOUR SESSION', 'codesblock' ); ?></span></div>
					<h3><?php esc_html_e( 'One focused round. A better next answer.', 'codesblock' ); ?></h3>
					<ul><li><span>01</span><?php esc_html_e( 'A realistic prompt for your chosen track', 'codesblock' ); ?></li><li><span>02</span><?php esc_html_e( 'A private workspace for your reasoning', 'codesblock' ); ?></li><li><span>03</span><?php esc_html_e( 'Your first session is free, with no card required', 'codesblock' ); ?></li></ul>
					<div class="included-card-footer"><span class="session-counter"><span></span></span><span><?php esc_html_e( '1 free session available', 'codesblock' ); ?></span></div>
				</aside>
			</section>
		<?php endif; ?>
	</section>
</main>
