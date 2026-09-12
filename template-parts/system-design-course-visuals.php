<?php
/**
 * Original, code-native visual curriculum preview for the System Design course.
 *
 * @package CodesBlock
 */
?>
<section class="sd-visual-showcase" id="design-labs" aria-labelledby="sd-showcase-heading">
	<header class="sd-showcase-heading">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Learn on the architecture board', 'codesblock' ); ?></p>
			<h2 id="sd-showcase-heading"><?php esc_html_e( 'See the system. Trace the request. Defend every decision.', 'codesblock' ); ?></h2>
		</div>
		<p><?php esc_html_e( 'Every lab turns an open-ended prompt into a readable diagram, a capacity model, and the trade-offs an interviewer will ask you to explain.', 'codesblock' ); ?></p>
	</header>

	<figure class="sd-request-board">
		<div class="sd-board-toolbar" aria-hidden="true"><span></span><span></span><span></span><strong>whiteboard / photo-sharing-feed</strong><em>42 min</em></div>
		<div class="sd-board-canvas">
			<div class="sd-board-note sd-note-requirement"><small>01 / REQUIREMENT</small><strong>Home feed under 200 ms</strong><span>10M daily users · read heavy</span></div>
			<div class="sd-flow-row" aria-label="Photo sharing feed request path">
				<div class="sd-node sd-node-client"><span class="sd-node-icon">UI</span><strong>Mobile client</strong><small>GET /feed</small></div>
				<span class="sd-arrow" aria-hidden="true">→</span>
				<div class="sd-node"><span class="sd-node-icon">LB</span><strong>Load balancer</strong><small>health + routing</small></div>
				<span class="sd-arrow" aria-hidden="true">→</span>
				<div class="sd-node sd-node-primary"><span class="sd-node-icon">FS</span><strong>Feed service</strong><small>rank + paginate</small></div>
				<span class="sd-arrow" aria-hidden="true">→</span>
				<div class="sd-node sd-node-cache"><span class="sd-node-icon">C</span><strong>Feed cache</strong><small>precomputed IDs</small></div>
			</div>
			<div class="sd-board-branches">
				<div><b>↳</b><span><strong>Fan-out workers</strong><small>push updates asynchronously</small></span></div>
				<div><b>↳</b><span><strong>Post store</strong><small>durable source of truth</small></span></div>
				<div><b>↳</b><span><strong>Media CDN</strong><small>serve images near users</small></span></div>
			</div>
			<div class="sd-board-note sd-note-tradeoff"><small>INTERVIEWER FOLLOW-UP</small><strong>What breaks when a celebrity posts?</strong><span>Compare push, pull, and a hybrid fan-out path.</span></div>
		</div>
		<figcaption><strong><?php esc_html_e( 'Worked diagram: a photo-sharing feed', 'codesblock' ); ?></strong><span><?php esc_html_e( 'Requirements → request path → storage → bottleneck → trade-off', 'codesblock' ); ?></span></figcaption>
	</figure>

	<div class="sd-case-grid">
		<article class="sd-case-card sd-case-blue">
			<div class="sd-case-meta"><span>LAB 04</span><small>latency · caching</small></div>
			<h3><?php esc_html_e( 'Design a URL shortener', 'codesblock' ); ?></h3>
			<div class="sd-mini-diagram" aria-label="URL shortener request flow"><b>URL</b><i>→</i><b>API</b><i>→</i><b>Cache</b><i>→</i><b>DB</b></div>
			<p><?php esc_html_e( 'Choose an ID strategy, estimate key space, and keep redirects fast at global scale.', 'codesblock' ); ?></p>
			<footer><span>8 decisions</span><span>1 design drill</span></footer>
		</article>
		<article class="sd-case-card sd-case-green">
			<div class="sd-case-meta"><span>LAB 08</span><small>consistency · concurrency</small></div>
			<h3><?php esc_html_e( 'Design ticket booking', 'codesblock' ); ?></h3>
			<div class="sd-lock-diagram" aria-label="Concurrent booking lock"><span>User A</span><b>SEAT 14C<br><small>LOCKED · 01:58</small></b><span>User B</span></div>
			<p><?php esc_html_e( 'Prevent double booking while preserving throughput, recovery, and a clear checkout experience.', 'codesblock' ); ?></p>
			<footer><span>11 decisions</span><span>2 failure drills</span></footer>
		</article>
		<article class="sd-case-card sd-case-amber">
			<div class="sd-case-meta"><span>LAB 11</span><small>AI · cost · reliability</small></div>
			<h3><?php esc_html_e( 'Design an AI copilot', 'codesblock' ); ?></h3>
			<div class="sd-ai-diagram" aria-label="AI retrieval and generation flow"><b>Prompt</b><i>+</i><b>Context</b><i>→</i><b>Model</b><i>→</i><b>Eval</b></div>
			<p><?php esc_html_e( 'Budget latency and tokens, retrieve useful context, and build safety and evaluation into the path.', 'codesblock' ); ?></p>
			<footer><span>10 decisions</span><span>1 capstone</span></footer>
		</article>
	</div>

	<div class="sd-method-strip" aria-label="The course design method">
		<div><span>01</span><strong><?php esc_html_e( 'Clarify', 'codesblock' ); ?></strong><small><?php esc_html_e( 'users, scope, constraints', 'codesblock' ); ?></small></div>
		<div><span>02</span><strong><?php esc_html_e( 'Estimate', 'codesblock' ); ?></strong><small><?php esc_html_e( 'traffic, storage, bandwidth', 'codesblock' ); ?></small></div>
		<div><span>03</span><strong><?php esc_html_e( 'Draw', 'codesblock' ); ?></strong><small><?php esc_html_e( 'request path and data flow', 'codesblock' ); ?></small></div>
		<div><span>04</span><strong><?php esc_html_e( 'Stress-test', 'codesblock' ); ?></strong><small><?php esc_html_e( 'failures and bottlenecks', 'codesblock' ); ?></small></div>
		<div><span>05</span><strong><?php esc_html_e( 'Defend', 'codesblock' ); ?></strong><small><?php esc_html_e( 'trade-offs and alternatives', 'codesblock' ); ?></small></div>
	</div>
</section>
