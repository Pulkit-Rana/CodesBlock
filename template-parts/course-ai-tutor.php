<?php
/**
 * Course page: AI tutor showcase. A small live demo (highlight-to-ask and voice
 * mode) followed by the features that make it worth paying for.
 *
 * @package CodesBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$codesblock_ai_features = array(
	array(
		'icon'  => 'highlight',
		'title' => __( 'Highlight and ask', 'codesblock' ),
		'text'  => __( 'Select any line, diagram label or code snippet. The tutor answers about exactly that, in the context of the lesson you are on.', 'codesblock' ),
	),
	array(
		'icon'  => 'mic',
		'title' => __( 'Talk it through by voice', 'codesblock' ),
		'text'  => __( 'Explain your design out loud, the way you will in the interview. Interrupt, ask follow-ups and keep going hands-free.', 'codesblock' ),
	),
	array(
		'icon'  => 'book',
		'title' => __( 'Answers from the course', 'codesblock' ),
		'text'  => __( 'Every reply is grounded in these lessons and labs, and links back to the lesson it came from, so you can check it.', 'codesblock' ),
	),
	array(
		'icon'  => 'hint',
		'title' => __( 'Hints, not spoilers', 'codesblock' ),
		'text'  => __( 'Stuck on a lab or a 10x drill? You get a nudge and a question back, not the full answer. You still do the thinking.', 'codesblock' ),
	),
	array(
		'icon'  => 'interview',
		'title' => __( 'Interviewer mode', 'codesblock' ),
		'text'  => __( 'The tutor plays the interviewer: it changes a constraint mid-design and pushes on your weakest trade-off.', 'codesblock' ),
	),
	array(
		'icon'  => 'quiz',
		'title' => __( 'Quiz me on this', 'codesblock' ),
		'text'  => __( 'Turn a lesson, or just your highlights, into a two-minute quiz before you move on, so the ideas stick.', 'codesblock' ),
	),
);

$codesblock_ai_icons = array(
	'highlight' => '<path d="M4 20h7"/><path d="M14.5 4.5l5 5L10 19H5v-5z"/>',
	'mic'       => '<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/>',
	'book'      => '<path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/><path d="M4 21V5M9 8h6"/>',
	'hint'      => '<path d="M9 18h6M10 21h4"/><path d="M12 3a6 6 0 0 0-3.5 10.9V16h7v-2.1A6 6 0 0 0 12 3z"/>',
	'interview' => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.4A8 8 0 1 1 21 12z"/><path d="M9 11h.01M12 11h.01M15 11h.01"/>',
	'quiz'      => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>',
);
?>
<section class="cbl-ai" id="ai-tutor" aria-labelledby="ai-tutor-heading">
	<div class="cbl-ai-head">
		<p class="cbl-eyebrow"><?php esc_html_e( 'AI tutor, built in', 'codesblock' ); ?></p>
		<h2 id="ai-tutor-heading"><?php esc_html_e( 'A study partner that has read every lesson', 'codesblock' ); ?></h2>
		<p><?php esc_html_e( 'Highlight a line you do not get, ask by text or voice, and get an answer grounded in the course. It is there when you are stuck, and switched off during timed mocks so your scores stay your own.', 'codesblock' ); ?></p>
	</div>

	<div class="cbl-ai-demo" data-cbl-ai-demo data-mode="text">
		<div class="cbl-ai-lesson" aria-hidden="true">
			<p class="cbl-ai-lesson-kicker"><?php esc_html_e( 'Module 2 · Consistency models', 'codesblock' ); ?></p>
			<p class="cbl-ai-lesson-title"><?php esc_html_e( 'Session guarantees with replicas', 'codesblock' ); ?></p>
			<p class="cbl-ai-lesson-intro"><?php esc_html_e( 'Async replicas let you scale reads cheaply. The catch is lag.', 'codesblock' ); ?></p>
			<p>
				<span class="cbl-ai-mark">
					<span class="cbl-ai-toolbar">
						<span class="is-primary"><svg viewBox="0 0 24 24"><path d="M12 3l1.8 4.6L18.5 9l-4.7 1.4L12 15l-1.8-4.6L5.5 9l4.7-1.4z"/></svg><?php esc_html_e( 'Ask AI', 'codesblock' ); ?></span>
						<span><?php esc_html_e( 'Explain simpler', 'codesblock' ); ?></span>
						<span><?php esc_html_e( 'Quiz me', 'codesblock' ); ?></span>
						<span><svg viewBox="0 0 24 24"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0"/></svg><?php esc_html_e( 'Talk', 'codesblock' ); ?></span>
					</span>
					<?php esc_html_e( 'Read-your-writes breaks when a follower lags behind the leader', 'codesblock' ); ?></span>,
				<?php esc_html_e( 'so a user can save a change and not see it on the next page.', 'codesblock' ); ?>
			</p>
			<p class="cbl-ai-lesson-fade"><?php esc_html_e( 'Session tokens carry the last write position so a read can wait for, or skip, a replica that is behind.', 'codesblock' ); ?></p>
		</div>

		<div class="cbl-ai-chat">
			<div class="cbl-ai-chat-top">
				<span class="cbl-ai-avatar" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3l1.8 4.6L18.5 9l-4.7 1.4L12 15l-1.8-4.6L5.5 9l4.7-1.4z"/></svg></span>
				<strong><?php esc_html_e( 'AI tutor', 'codesblock' ); ?></strong>
				<div class="cbl-ai-modes" role="group" aria-label="<?php esc_attr_e( 'Demo mode', 'codesblock' ); ?>">
					<button type="button" data-cbl-ai-mode="text" aria-pressed="true"><?php esc_html_e( 'Text', 'codesblock' ); ?></button>
					<button type="button" data-cbl-ai-mode="voice" aria-pressed="false"><?php esc_html_e( 'Voice', 'codesblock' ); ?></button>
				</div>
			</div>

			<div class="cbl-ai-thread" data-cbl-ai-pane="text">
				<div class="cbl-ai-msg is-user">
					<q><?php esc_html_e( 'read-your-writes breaks when a follower lags…', 'codesblock' ); ?></q>
					<?php esc_html_e( 'Why does this get worse at 10x?', 'codesblock' ); ?>
				</div>
				<div class="cbl-ai-msg is-ai">
					<p><?php esc_html_e( 'More traffic means more writes to replicate and more replicas to serve reads, so lag grows and more users land on a stale follower.', 'codesblock' ); ?></p>
					<p><?php esc_html_e( 'Two common fixes: send a user to the leader for a few seconds after they write, or pass their last write position and only read from a replica that has caught up.', 'codesblock' ); ?></p>
					<span class="cbl-ai-source"><svg viewBox="0 0 24 24"><path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/></svg><?php esc_html_e( 'From Module 2 · Strong, eventual, causal and session consistency', 'codesblock' ); ?></span>
				</div>
				<div class="cbl-ai-chips">
					<span><?php esc_html_e( 'What does pinning cost the leader?', 'codesblock' ); ?></span>
					<span><?php esc_html_e( 'Quiz me on this', 'codesblock' ); ?></span>
				</div>
			</div>

			<div class="cbl-ai-thread" data-cbl-ai-pane="voice" hidden>
				<div class="cbl-ai-voice">
					<span class="cbl-ai-wave" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span>
					<small><?php esc_html_e( 'Interviewer mode · listening', 'codesblock' ); ?></small>
				</div>
				<div class="cbl-ai-msg is-ai is-spoken">
					<p><?php esc_html_e( '“Traffic just went 10x. What breaks first in your design?”', 'codesblock' ); ?></p>
				</div>
				<div class="cbl-ai-msg is-user is-spoken">
					<?php esc_html_e( '“Replica lag. I would pin a user’s reads to the leader right after they write.”', 'codesblock' ); ?>
				</div>
				<div class="cbl-ai-msg is-ai is-spoken">
					<p><?php esc_html_e( '“Good. Now the leader takes those reads at 10x. How do you keep it from becoming the bottleneck?”', 'codesblock' ); ?></p>
				</div>
			</div>

			<div class="cbl-ai-input" aria-hidden="true">
				<span><?php esc_html_e( 'Ask about this lesson…', 'codesblock' ); ?></span>
				<i class="cbl-ai-mic"><svg viewBox="0 0 24 24"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/></svg></i>
			</div>
		</div>
	</div>

	<ul class="cbl-ai-features" role="list">
		<?php foreach ( $codesblock_ai_features as $codesblock_ai_feature ) : ?>
			<li>
				<span class="cbl-ai-ico" aria-hidden="true"><svg viewBox="0 0 24 24"><?php echo $codesblock_ai_icons[ $codesblock_ai_feature['icon'] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG paths defined above. ?></svg></span>
				<strong><?php echo esc_html( $codesblock_ai_feature['title'] ); ?></strong>
				<p><?php echo esc_html( $codesblock_ai_feature['text'] ); ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
