<?php
/**
 * Sticky Collapsible Course Map & 10-Phase Curriculum for CodesBlock.
 *
 * ALL CHANGES FOR THE COURSE MAP ARE CONTAINED IN THIS SINGLE FILE.
 * TO REVERT: Replace this file with template-parts/course-map.php.original-backup
 * or run: git checkout template-parts/course-map.php
 *
 * @package CodesBlock
 */

$course_id = isset( $args['course_id'] ) ? absint( $args['course_id'] ) : 0;
$lesson_id = isset( $args['lesson_id'] ) ? absint( $args['lesson_id'] ) : 0;

if ( ! $course_id ) {
	return;
}

$course_post = get_post( $course_id );
$course_slug = $course_post ? $course_post->post_name : '';

// 10-Phase Curriculum as published on https://codesblock.io/courses/crack-the-system-design-interview/
$crack_system_design_curriculum = array(
	array(
		'number'   => '01',
		'title'    => __( 'Think like a system designer', 'codesblock' ),
		'subtitle' => __( 'interview structure · requirements · estimation · APIs · trade-offs', 'codesblock' ),
		'summary'  => __( 'Build the interview operating system before memorizing architecture.', 'codesblock' ),
		'lessons'  => array(
			__( 'What system design interviews actually test', 'codesblock' ),
			__( 'Functional vs non-functional requirements', 'codesblock' ),
			__( 'QPS, storage, bandwidth and latency estimates', 'codesblock' ),
			__( 'API contracts and core entities', 'codesblock' ),
			__( 'Critical-path thinking', 'codesblock' ),
			__( 'SCALE-45 framework', 'codesblock' ),
		),
	),
	array(
		'number'   => '02',
		'title'    => __( 'Scale — from one server to millions', 'codesblock' ),
		'subtitle' => __( 'networking · load balancing · caching · databases · queues', 'codesblock' ),
		'summary'  => __( 'Learn each building block as an answer to a specific bottleneck.', 'codesblock' ),
		'lessons'  => array(
			__( 'HTTP, TCP/UDP, DNS, WebSockets and gRPC', 'codesblock' ),
			__( 'Load balancers, gateways and stateless services', 'codesblock' ),
			__( 'Redis, cache-aside, TTL, invalidation and stampede', 'codesblock' ),
			__( 'SQL/NoSQL, indexes, replication and sharding', 'codesblock' ),
			__( 'Kafka, queues, pub/sub, workers and backpressure', 'codesblock' ),
			__( 'CDNs, object storage and media delivery', 'codesblock' ),
		),
	),
	array(
		'number'   => '03',
		'title'    => __( 'Distributed reality', 'codesblock' ),
		'subtitle' => __( 'consistency · failure · concurrency · reliability · operations', 'codesblock' ),
		'summary'  => __( 'Move beyond diagrams into the problems production systems actually have.', 'codesblock' ),
		'lessons'  => array(
			__( 'CAP, consistency models and replication lag', 'codesblock' ),
			__( 'Quorums, leader election and consensus intuition', 'codesblock' ),
			__( 'Retries, timeouts, circuit breakers and bulkheads', 'codesblock' ),
			__( 'Idempotency, duplicate delivery and saga workflows', 'codesblock' ),
			__( 'Hot keys, partitions, skew and rate limiting', 'codesblock' ),
			__( 'Metrics, logs, traces, SLIs/SLOs and recovery', 'codesblock' ),
		),
	),
	array(
		'number'   => '04',
		'title'    => __( 'Classic architecture labs', 'codesblock' ),
		'subtitle' => __( 'feeds · chat · booking · payments · video · collaboration', 'codesblock' ),
		'summary'  => __( 'Apply the same interview framework repeatedly until it becomes automatic.', 'codesblock' ),
		'lessons'  => array(
			__( 'URL shortener + rate limiter', 'codesblock' ),
			__( 'Chat, presence and notifications', 'codesblock' ),
			__( 'News feed and fan-out', 'codesblock' ),
			__( 'Ticket booking and concurrency', 'codesblock' ),
			__( 'Payments and idempotent workflows', 'codesblock' ),
			__( 'File sync, video streaming and nearby search', 'codesblock' ),
		),
	),
	array(
		'number'   => '05',
		'title'    => __( 'AI becomes a backend dependency', 'codesblock' ),
		'subtitle' => __( 'models · gateways · context · routing · cost · fallbacks', 'codesblock' ),
		'summary'  => __( 'Understand why an LLM behaves differently from a normal service dependency.', 'codesblock' ),
		'lessons'  => array(
			__( 'Tokens, context windows and streaming', 'codesblock' ),
			__( 'Structured output and tool calling', 'codesblock' ),
			__( 'AI gateways, provider routing and fallback', 'codesblock' ),
			__( 'Token-based rate limits and cost budgets', 'codesblock' ),
			__( 'Prompt/context versioning and state', 'codesblock' ),
			__( 'Semantic caching and graceful degradation', 'codesblock' ),
		),
	),
	array(
		'number'   => '06',
		'title'    => __( 'Production RAG & semantic search', 'codesblock' ),
		'subtitle' => __( 'ingestion · embeddings · vector indexes · reranking · permissions', 'codesblock' ),
		'summary'  => __( 'Go far beyond “vector DB + LLM”. Design the complete information pipeline.', 'codesblock' ),
		'lessons'  => array(
			__( 'Parsing, chunking and embedding pipelines', 'codesblock' ),
			__( 'HNSW and ANN intuition', 'codesblock' ),
			__( 'Vector vs lexical vs hybrid retrieval', 'codesblock' ),
			__( 'Reranking and context assembly', 'codesblock' ),
			__( 'Freshness, access control and multitenancy', 'codesblock' ),
			__( 'RAG quality, latency and failure modes', 'codesblock' ),
		),
	),
	array(
		'number'   => '07',
		'title'    => __( 'Agents meet microservices', 'codesblock' ),
		'subtitle' => __( 'tools · MCP · memory · workflows · safety · durability', 'codesblock' ),
		'summary'  => __( 'Keep deterministic services deterministic; let the agent operate through controlled interfaces.', 'codesblock' ),
		'lessons'  => array(
			__( 'Workflow vs agent: when not to use autonomy', 'codesblock' ),
			__( 'Tool schemas, registries and discovery', 'codesblock' ),
			__( 'Short-term memory, long-term memory and checkpoints', 'codesblock' ),
			__( 'Retries, idempotent tool calls and durable execution', 'codesblock' ),
			__( 'MCP architecture and service integration', 'codesblock' ),
			__( 'Human approval, permissions and agent budgets', 'codesblock' ),
		),
	),
	array(
		'number'   => '08',
		'title'    => __( 'Production AI reliability', 'codesblock' ),
		'subtitle' => __( 'evals · tracing · security · cost · observability', 'codesblock' ),
		'summary'  => __( 'The chapter that turns an AI demo into an operable production system.', 'codesblock' ),
		'lessons'  => array(
			__( 'Golden sets, offline/online evals and regression tests', 'codesblock' ),
			__( 'Prompt, retrieval and tool-call tracing', 'codesblock' ),
			__( 'Hallucination and quality failure handling', 'codesblock' ),
			__( 'Prompt injection and poisoned retrieval', 'codesblock' ),
			__( 'Cost-per-request, routing and caching', 'codesblock' ),
			__( 'Provider outages, queues and degradation', 'codesblock' ),
		),
	),
	array(
		'number'   => '09',
		'title'    => __( 'Realtime voice architecture', 'codesblock' ),
		'subtitle' => __( 'WebRTC · streaming · interruption · latency · sessions', 'codesblock' ),
		'summary'  => __( 'Use CodesBlock\'s own voice tutor as a system-design case study.', 'codesblock' ),
		'lessons'  => array(
			__( 'STT → LLM → TTS vs speech-to-speech', 'codesblock' ),
			__( 'WebRTC and WebSockets', 'codesblock' ),
			__( 'Voice activity detection and barge-in', 'codesblock' ),
			__( 'Conversation/session state', 'codesblock' ),
			__( 'Tool use during voice conversations', 'codesblock' ),
			__( 'Latency budgets and concurrent-call scaling', 'codesblock' ),
		),
	),
	array(
		'number'   => '10',
		'title'    => __( 'Pressure test & capstone', 'codesblock' ),
		'subtitle' => __( 'constraint ladders · architecture report · guided review path', 'codesblock' ),
		'summary'  => __( 'Finish by designing systems from a blank canvas and defending them under changing requirements.', 'codesblock' ),
		'lessons'  => array(
			__( 'Classic architecture mock', 'codesblock' ),
			__( 'Senior reliability mock', 'codesblock' ),
			__( 'RAG architecture mock', 'codesblock' ),
			__( 'Agent architecture mock', 'codesblock' ),
			__( 'Voice architecture mock', 'codesblock' ),
			__( 'Capstone: design, break and defend', 'codesblock' ),
		),
	),
);

// Determine if this course is the System Design course or general
$is_system_design = in_array( $course_slug, array( 'crack-the-system-design-interview', 'system-design', 'system-design-interview-sprint' ), true )
	|| false !== stripos( get_the_title( $course_id ), 'system design' )
	|| 34 === $course_id;

if ( $is_system_design ) {
	$outline = $crack_system_design_curriculum;
} else {
	$fallback_outline = function_exists( 'codesblock_get_course_outline' ) ? codesblock_get_course_outline( $course_id ) : array();
	$outline = ! empty( $fallback_outline ) ? $fallback_outline : $crack_system_design_curriculum;
}

if ( empty( $outline ) ) {
	return;
}

$published_lessons = function_exists( 'cbcore_get_course_lessons' ) ? cbcore_get_course_lessons( $course_id ) : array();

// Map published lesson posts to module/position and slug
$lesson_lookup_by_pos = array();
$lesson_lookup_by_id  = array();
$lesson_lookup_by_slug = array();

foreach ( $published_lessons as $published_lesson ) {
	$mod = absint( get_post_meta( $published_lesson->ID, '_cbcore_lesson_module', true ) );
	$pos = max( 1, absint( get_post_meta( $published_lesson->ID, '_cbcore_lesson_position', true ) ) );
	if ( ! isset( $lesson_lookup_by_pos[ $mod ][ $pos ] ) ) {
		$lesson_lookup_by_pos[ $mod ][ $pos ] = $published_lesson;
	}
	$lesson_lookup_by_id[ $published_lesson->ID ] = $published_lesson;
	$lesson_lookup_by_slug[ $published_lesson->post_name ] = $published_lesson;
}

$current_lesson_post = $lesson_id ? get_post( $lesson_id ) : null;
$current_lesson_slug = $current_lesson_post ? $current_lesson_post->post_name : '';

$planned_total     = 0;
$current_step      = 0;
$running_step      = 0;
$active_phase_idx  = -1;
$active_lesson_idx = -1;

foreach ( $outline as $phase_idx => $phase ) {
	$phase_lessons = isset( $phase['lessons'] ) && is_array( $phase['lessons'] ) ? $phase['lessons'] : array();
	$planned_total += count( $phase_lessons );

	foreach ( $phase_lessons as $lesson_idx => $lesson_title ) {
		$running_step++;
		$pos = $lesson_idx + 1;

		// Check matching published post
		$matched = false;
		if ( isset( $lesson_lookup_by_pos[ $phase_idx ][ $pos ] ) && $lesson_lookup_by_pos[ $phase_idx ][ $pos ]->ID === $lesson_id ) {
			$matched = true;
		} elseif ( 0 === $phase_idx && 0 === $lesson_idx && 'what-system-design-is-and-why-it-matters' === $current_lesson_slug ) {
			$matched = true;
		} elseif ( $lesson_id && isset( $lesson_lookup_by_id[ $lesson_id ] ) ) {
			$mod = absint( get_post_meta( $lesson_id, '_cbcore_lesson_module', true ) );
			$lpos = max( 1, absint( get_post_meta( $lesson_id, '_cbcore_lesson_position', true ) ) );
			if ( $mod === $phase_idx && $lpos === $pos ) {
				$matched = true;
			}
		}

		if ( $matched ) {
			$current_step      = $running_step;
			$active_phase_idx  = $phase_idx;
			$active_lesson_idx = $lesson_idx;
		}
	}
}

// Fallback to step 1 if current lesson is the first lesson
if ( ! $current_step && $lesson_id && ( 'what-system-design-is-and-why-it-matters' === $current_lesson_slug || 77 === $lesson_id ) ) {
	$current_step      = 1;
	$active_phase_idx  = 0;
	$active_lesson_idx = 0;
}

$progress_percent = $planned_total && $current_step ? max( 2, round( ( $current_step / $planned_total ) * 100 ) ) : 2;

$is_editor        = current_user_can( 'edit_post', $course_id );
$is_member        = function_exists( 'cbcommerce_is_frontend_member' ) ? cbcommerce_is_frontend_member() : is_user_logged_in();
$has_access       = function_exists( 'codesblock_user_can_view_protected_content' ) ? codesblock_user_can_view_protected_content( $course_id ) : true;
$can_open_lessons = $is_editor || ( $is_member && $has_access );

$sidebar_id       = 'cb-sticky-course-sidebar-' . $course_id;
$toggle_pill_id   = 'cb-smap-trigger-' . $course_id;
?>

<!-- ==========================================================================
     CODESBLOCK STICKY COLLAPSIBLE COURSE MAP COMPONENT
     Single-file implementation: Markup, Scoped Styles, and Controller Script.
     ========================================================================== -->

<style id="cb-sticky-course-map-styles">
/* CodesBlock Sticky Side Menu Variables */
:root {
	--cb-smap-w: 340px;
	--cb-smap-bg: #ffffff;
	--cb-smap-ink: #0f172a;
	--cb-smap-ink-muted: #64748b;
	--cb-smap-border: #e2e8f0;
	--cb-smap-blue: #2563eb;
	--cb-smap-blue-hover: #1d4ed8;
	--cb-smap-blue-soft: #eff6ff;
	--cb-smap-green: #10b981;
	--cb-smap-navy: #0b1120;
	--cb-smap-navy-card: #151f32;
	--cb-smap-header-h: 74px;
}

/* Hide legacy old drawer elements from course-learning.css */
.cb-course-map__toggle,
.cb-course-map__scrim,
.cb-course-map__panel {
	display: none !important;
}

/* Floating trigger button when sidebar is collapsed/hidden */
.cb-smap-trigger-btn {
	align-items: center;
	background: #0b1120;
	border: 1px solid rgba(255, 255, 255, 0.16);
	border-radius: 999px;
	box-shadow: 0 10px 25px -4px rgba(11, 17, 32, 0.3), 0 4px 10px rgba(0, 0, 0, 0.12);
	color: #ffffff;
	cursor: pointer;
	display: inline-flex;
	font-family: inherit;
	font-size: 12.5px;
	font-weight: 750;
	gap: 9px;
	left: 18px;
	letter-spacing: 0.01em;
	opacity: 0;
	padding: 9px 15px 9px 12px;
	pointer-events: none;
	position: fixed;
	top: calc(var(--cb-smap-header-h, 74px) + 14px);
	transform: translateX(-16px);
	transition: opacity 0.22s ease, transform 0.22s ease, background 0.18s ease, box-shadow 0.18s ease;
	z-index: 9998;
}

.cb-smap-trigger-btn:hover {
	background: #1e293b;
	box-shadow: 0 14px 30px -4px rgba(11, 17, 32, 0.45), 0 6px 14px rgba(37, 99, 235, 0.2);
	transform: translateX(0) scale(1.02);
}

.cb-smap-trigger-btn:focus-visible {
	outline: 2px solid var(--cb-smap-blue);
	outline-offset: 2px;
}

.cb-smap-trigger-btn svg {
	fill: none;
	height: 17px;
	stroke: currentColor;
	stroke-linecap: round;
	stroke-linejoin: round;
	stroke-width: 2;
	width: 17px;
}

.cb-smap-trigger-btn .cb-smap-step-pill {
	background: rgba(37, 99, 235, 0.28);
	border: 1px solid rgba(96, 165, 250, 0.35);
	border-radius: 99px;
	color: #93c5fd;
	font-size: 11px;
	font-weight: 700;
	padding: 1px 7px;
}

/* Sidebar docked container */
.cb-smap-sidebar {
	background: var(--cb-smap-bg);
	border-right: 1px solid var(--cb-smap-border);
	bottom: 0;
	box-shadow: 12px 0 35px -10px rgba(15, 23, 42, 0.08);
	display: flex;
	flex-direction: column;
	font-family: inherit;
	left: 0;
	overflow: hidden;
	position: fixed;
	top: var(--cb-smap-header-h, 74px);
	transform: translateX(0);
	transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.28s ease;
	width: var(--cb-smap-w);
	z-index: 9995;
}

/* Sidebar Backdrop for mobile/tablet */
.cb-smap-backdrop {
	background: rgba(11, 17, 32, 0.5);
	backdrop-filter: blur(3px);
	-webkit-backdrop-filter: blur(3px);
	inset: 0;
	opacity: 0;
	pointer-events: none;
	position: fixed;
	transition: opacity 0.25s ease;
	z-index: 9994;
}

/* When Sidebar is HIDDEN */
body:not(.cb-smap-open) .cb-smap-sidebar {
	box-shadow: none;
	pointer-events: none;
	transform: translateX(-100%);
}

body:not(.cb-smap-open) .cb-smap-trigger-btn {
	opacity: 1;
	pointer-events: auto;
	transform: translateX(0);
}

/* Header section of Course Map sidebar */
.cb-smap-header {
	background:
		radial-gradient(circle at 90% 10%, rgba(37, 99, 235, 0.22), transparent 45%),
		linear-gradient(150deg, #0b1120 0%, #152238 100%);
	border-bottom: 1px solid rgba(255, 255, 255, 0.08);
	color: #ffffff;
	flex-shrink: 0;
	padding: 18px 18px 16px;
	position: relative;
}

.cb-smap-header-top {
	align-items: flex-start;
	display: flex;
	gap: 12px;
	justify-content: space-between;
}

.cb-smap-kicker {
	color: #60a5fa;
	font-size: 10.5px;
	font-weight: 800;
	letter-spacing: 0.1em;
	margin: 0 0 4px;
	text-transform: uppercase;
}

.cb-smap-course-title {
	color: #ffffff;
	font-size: 14.5px;
	font-weight: 800;
	line-height: 1.35;
	margin: 0;
}

/* Sleek Hide button in sidebar header */
.cb-smap-hide-btn {
	align-items: center;
	background: rgba(255, 255, 255, 0.1);
	border: 1px solid rgba(255, 255, 255, 0.18);
	border-radius: 8px;
	color: #e2e8f0;
	cursor: pointer;
	display: inline-flex;
	font-family: inherit;
	font-size: 11.5px;
	font-weight: 700;
	gap: 5px;
	padding: 5px 9px;
	transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
	white-space: nowrap;
}

.cb-smap-hide-btn:hover {
	background: rgba(255, 255, 255, 0.2);
	border-color: rgba(255, 255, 255, 0.35);
	color: #ffffff;
}

.cb-smap-hide-btn svg {
	fill: none;
	height: 13px;
	stroke: currentColor;
	stroke-linecap: round;
	stroke-linejoin: round;
	stroke-width: 2.2;
	width: 13px;
}

/* Progress bar inside sidebar header */
.cb-smap-progress-wrap {
	margin-top: 14px;
}

.cb-smap-progress-meta {
	align-items: center;
	color: #94a3b8;
	display: flex;
	font-size: 11px;
	font-weight: 700;
	justify-content: space-between;
	margin-bottom: 6px;
}

.cb-smap-progress-meta strong {
	color: #38bdf8;
	font-weight: 800;
}

.cb-smap-progress-track {
	background: rgba(255, 255, 255, 0.14);
	border-radius: 99px;
	height: 5px;
	overflow: hidden;
	position: relative;
	width: 100%;
}

.cb-smap-progress-fill {
	background: linear-gradient(90deg, #2563eb, #38bdf8);
	border-radius: 99px;
	height: 100%;
	transition: width 0.35s ease;
	width: <?php echo esc_attr( $progress_percent ); ?>%;
}

/* Overview quick link strip */
.cb-smap-overview-link {
	align-items: center;
	background: #f8fafc;
	border-bottom: 1px solid var(--cb-smap-border);
	color: #334155;
	display: flex;
	flex-shrink: 0;
	font-size: 12px;
	font-weight: 750;
	gap: 8px;
	padding: 10px 18px;
	text-decoration: none;
	transition: background 0.15s ease, color 0.15s ease;
}

.cb-smap-overview-link:hover {
	background: var(--cb-smap-blue-soft);
	color: var(--cb-smap-blue);
}

.cb-smap-overview-link svg {
	fill: none;
	height: 14px;
	stroke: currentColor;
	stroke-linecap: round;
	stroke-linejoin: round;
	stroke-width: 2;
	width: 14px;
}

/* Scrollable Modules and Lessons List */
.cb-smap-nav {
	flex: 1;
	overflow-y: auto;
	overscroll-behavior: contain;
	padding: 10px 0 16px;
	scrollbar-color: #cbd5e1 transparent;
	scrollbar-width: thin;
}

.cb-smap-nav::-webkit-scrollbar {
	width: 6px;
}

.cb-smap-nav::-webkit-scrollbar-thumb {
	background: #cbd5e1;
	border-radius: 99px;
}

/* Module Section */
.cb-smap-module {
	border-bottom: 1px solid #f1f5f9;
}

.cb-smap-module:last-child {
	border-bottom: none;
}

/* Module Header Toggle Button */
.cb-smap-module-toggle {
	align-items: center;
	background: transparent;
	border: 0;
	color: var(--cb-smap-ink);
	cursor: pointer;
	display: flex;
	font-family: inherit;
	gap: 10px;
	padding: 12px 18px;
	text-align: left;
	transition: background 0.15s ease;
	width: 100%;
}

.cb-smap-module-toggle:hover {
	background: #f8fafc;
}

.cb-smap-module-toggle:focus-visible {
	outline: 2px solid var(--cb-smap-blue);
	outline-offset: -2px;
}

/* Number pill for module */
.cb-smap-module-badge {
	align-items: center;
	background: #f1f5f9;
	border: 1px solid #e2e8f0;
	border-radius: 6px;
	color: #475569;
	display: inline-flex;
	flex-shrink: 0;
	font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
	font-size: 11px;
	font-weight: 800;
	height: 24px;
	justify-content: center;
	width: 24px;
}

.cb-smap-module.is-active-module .cb-smap-module-badge {
	background: #eff6ff;
	border-color: #bfdbfe;
	color: #1d4ed8;
}

.cb-smap-module-text {
	display: flex;
	flex: 1;
	flex-direction: column;
	gap: 2px;
	min-width: 0;
}

.cb-smap-module-heading {
	font-size: 12.5px;
	font-weight: 750;
	line-height: 1.35;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.cb-smap-module-count {
	color: var(--cb-smap-ink-muted);
	font-size: 10.5px;
	font-weight: 600;
}

/* Chevron arrow */
.cb-smap-module-icon {
	color: #94a3b8;
	flex-shrink: 0;
	height: 14px;
	transition: transform 0.22s ease, color 0.15s ease;
	width: 14px;
}

.cb-smap-module-toggle[aria-expanded="true"] .cb-smap-module-icon {
	color: var(--cb-smap-ink);
	transform: rotate(180deg);
}

/* Module Lessons List */
.cb-smap-lessons {
	background: #fafcff;
	border-top: 1px solid #f1f5f9;
	list-style: none;
	margin: 0;
	padding: 6px 0;
}

.cb-smap-lessons[hidden] {
	display: none;
}

.cb-smap-lesson {
	margin: 0;
	padding: 0;
	position: relative;
}

/* Lesson Link or Div */
.cb-smap-lesson a,
.cb-smap-lesson .cb-smap-lesson-placeholder {
	align-items: flex-start;
	color: #334155;
	display: flex;
	font-size: 12px;
	gap: 10px;
	line-height: 1.4;
	padding: 9px 18px 9px 24px;
	position: relative;
	text-decoration: none;
	transition: background 0.15s ease, color 0.15s ease;
}

.cb-smap-lesson a:hover {
	background: #edf5ff;
	color: var(--cb-smap-blue);
}

.cb-smap-lesson a:focus-visible {
	outline: 2px solid var(--cb-smap-blue);
	outline-offset: -2px;
}

/* Step marker bullet / number */
.cb-smap-lesson-num {
	align-items: center;
	background: #e2e8f0;
	border-radius: 50%;
	color: #64748b;
	display: inline-flex;
	flex-shrink: 0;
	font-size: 10px;
	font-weight: 800;
	height: 18px;
	justify-content: center;
	margin-top: 1px;
	width: 18px;
}

.cb-smap-lesson-info {
	display: flex;
	flex: 1;
	flex-direction: column;
	gap: 3px;
	min-width: 0;
}

.cb-smap-lesson-title {
	font-weight: 650;
	word-break: break-word;
}

/* Active / Current Lesson Item */
.cb-smap-lesson.is-current {
	background: #eff6ff;
}

.cb-smap-lesson.is-current::before {
	background: var(--cb-smap-blue);
	border-radius: 0 4px 4px 0;
	bottom: 0;
	content: "";
	left: 0;
	position: absolute;
	top: 0;
	width: 4px;
}

.cb-smap-lesson.is-current a,
.cb-smap-lesson.is-current .cb-smap-lesson-placeholder {
	color: #1d4ed8;
}

.cb-smap-lesson.is-current .cb-smap-lesson-num {
	background: var(--cb-smap-blue);
	color: #ffffff;
}

.cb-smap-lesson.is-current .cb-smap-lesson-title {
	font-weight: 800;
}

/* Status Pill */
.cb-smap-status-badge {
	align-self: flex-start;
	border-radius: 99px;
	font-size: 9.5px;
	font-weight: 800;
	letter-spacing: 0.04em;
	padding: 1px 7px;
	text-transform: uppercase;
}

.cb-smap-status-current {
	background: #dbeafe;
	color: #1e40af;
}

.cb-smap-status-planned {
	background: #f1f5f9;
	color: #94a3b8;
}

.cb-smap-status-published {
	background: #ecfdf5;
	color: #047857;
}

/* Sidebar Footer */
.cb-smap-footer {
	background: #ffffff;
	border-top: 1px solid var(--cb-smap-border);
	flex-shrink: 0;
	padding: 12px 16px;
}

.cb-smap-footer-cta {
	align-items: center;
	background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
	border: 0;
	border-radius: 10px;
	box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
	color: #ffffff;
	cursor: pointer;
	display: flex;
	font-family: inherit;
	font-size: 12px;
	font-weight: 750;
	justify-content: space-between;
	padding: 10px 14px;
	text-decoration: none;
	transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
	width: 100%;
}

.cb-smap-footer-cta:hover {
	background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
	box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
	color: #ffffff;
	transform: translateY(-1px);
}

/* ==========================================================================
   DESKTOP DOCKED SIDEBAR LAYOUT (>= 1024px)
   ========================================================================== */
@media (min-width: 1024px) {
	/* When course map is OPEN on desktop, push lesson main content cleanly */
	body.single-course_lesson.cb-smap-open .cb-lesson-page {
		padding-left: var(--cb-smap-w, 340px) !important;
		transition: padding-left 0.28s cubic-bezier(0.16, 1, 0.3, 1);
	}

	body.single-course_lesson:not(.cb-smap-open) .cb-lesson-page {
		padding-left: 0 !important;
		transition: padding-left 0.28s cubic-bezier(0.16, 1, 0.3, 1);
	}

	.cb-smap-backdrop {
		display: none !important;
	}
}

/* ==========================================================================
   MOBILE & TABLET SLIDE-OVER DRAWER (< 1024px)
   ========================================================================== */
@media (max-width: 1023px) {
	.cb-smap-sidebar {
		border-radius: 0 16px 16px 0;
		box-shadow: 18px 0 45px rgba(11, 17, 32, 0.25);
		top: 0;
		width: min(var(--cb-smap-w), calc(100vw - 44px));
		z-index: 100002;
	}

	body.cb-smap-open .cb-smap-sidebar {
		transform: translateX(0);
	}

	body.cb-smap-open .cb-smap-backdrop {
		opacity: 1;
		pointer-events: auto;
	}

	body.cb-smap-open {
		overflow: hidden;
	}

	.cb-smap-trigger-btn {
		bottom: 20px;
		left: 18px;
		top: auto;
		transform: none !important;
	}

	body.cb-smap-open .cb-smap-trigger-btn {
		opacity: 0 !important;
		pointer-events: none !important;
	}
}

@media (prefers-reduced-motion: reduce) {
	.cb-smap-sidebar,
	.cb-smap-trigger-btn,
	.cb-smap-backdrop,
	.cb-lesson-page {
		transition: none !important;
	}
}
</style>

<!-- Floating Toggle Pill (Always available when side menu is hidden) -->
<button
	id="<?php echo esc_attr( $toggle_pill_id ); ?>"
	class="cb-smap-trigger-btn"
	type="button"
	aria-expanded="false"
	aria-controls="<?php echo esc_attr( $sidebar_id ); ?>"
	title="<?php esc_attr_e( 'Open course map', 'codesblock' ); ?>"
	data-cb-smap-open
>
	<svg viewBox="0 0 24 24" aria-hidden="true">
		<path d="M4 6h16M4 12h10M4 18h14"/>
	</svg>
	<span><?php esc_html_e( 'Course Map', 'codesblock' ); ?></span>
	<?php if ( $current_step ) : ?>
		<span class="cb-smap-step-pill"><?php echo esc_html( $current_step . '/' . $planned_total ); ?></span>
	<?php endif; ?>
</button>

<!-- Mobile Scrim/Backdrop -->
<div class="cb-smap-backdrop" data-cb-smap-close aria-hidden="true"></div>

<!-- Sticky Collapsible Course Map Sidebar -->
<aside
	id="<?php echo esc_attr( $sidebar_id ); ?>"
	class="cb-smap-sidebar"
	aria-label="<?php esc_attr_e( 'Course learning map', 'codesblock' ); ?>"
	data-cb-smap-panel
	data-course-id="<?php echo esc_attr( $course_id ); ?>"
>
	<!-- Header -->
	<header class="cb-smap-header">
		<div class="cb-smap-header-top">
			<div>
				<p class="cb-smap-kicker"><?php esc_html_e( 'Course Roadmap', 'codesblock' ); ?></p>
				<h2 class="cb-smap-course-title"><?php echo esc_html( get_the_title( $course_id ) ); ?></h2>
			</div>
			<!-- Hide Sidebar Button -->
			<button
				class="cb-smap-hide-btn"
				type="button"
				data-cb-smap-close
				title="<?php esc_attr_e( 'Hide course map', 'codesblock' ); ?>"
				aria-label="<?php esc_attr_e( 'Hide course map', 'codesblock' ); ?>"
			>
				<svg viewBox="0 0 24 24" aria-hidden="true">
					<path d="M15 18l-6-6 6-6"/>
				</svg>
				<span><?php esc_html_e( 'Hide', 'codesblock' ); ?></span>
			</button>
		</div>

		<!-- Progress Meta & Bar -->
		<div class="cb-smap-progress-wrap">
			<div class="cb-smap-progress-meta">
				<span>
					<?php
					if ( $current_step ) {
						printf(
							/* translators: 1: current step, 2: total steps */
							esc_html__( 'Step %1$d of %2$d', 'codesblock' ),
							absint( $current_step ),
							absint( $planned_total )
						);
					} else {
						printf(
							/* translators: 1: phase count, 2: lesson count */
							esc_html__( '%1$d phases · %2$d lessons', 'codesblock' ),
							count( $outline ),
							absint( $planned_total )
						);
					}
					?>
				</span>
				<strong><?php echo esc_html( $progress_percent ); ?>%</strong>
			</div>
			<div class="cb-smap-progress-track" role="progressbar" aria-valuenow="<?php echo esc_attr( $progress_percent ); ?>" aria-valuemin="0" aria-valuemax="100">
				<div class="cb-smap-progress-fill"></div>
			</div>
		</div>
	</header>

	<!-- Overview Quick Link -->
	<a class="cb-smap-overview-link" href="<?php echo esc_url( get_permalink( $course_id ) ); ?>">
		<svg viewBox="0 0 24 24" aria-hidden="true">
			<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
			<polyline points="9 22 9 12 15 12 15 22"/>
		</svg>
		<span><?php esc_html_e( 'Course Overview', 'codesblock' ); ?></span>
	</a>

	<!-- Scrollable Curriculum Navigation -->
	<nav class="cb-smap-nav" aria-label="<?php esc_attr_e( 'Curriculum Phases', 'codesblock' ); ?>">
		<?php
		$running_item_index = 0;
		foreach ( $outline as $phase_index => $phase ) :
			$phase_no       = isset( $phase['number'] ) ? $phase['number'] : str_pad( (string) ( $phase_index + 1 ), 2, '0', STR_PAD_LEFT );
			$phase_title    = isset( $phase['title'] ) ? $phase['title'] : sprintf( __( 'Phase %d', 'codesblock' ), $phase_index + 1 );
			$phase_lessons  = isset( $phase['lessons'] ) && is_array( $phase['lessons'] ) ? $phase['lessons'] : array();
			$is_active_mod  = ( $active_phase_idx === $phase_index );
			$is_expanded    = $is_active_mod || ( -1 === $active_phase_idx && 0 === $phase_index );
			$module_panel_id = 'cb-smap-panel-' . $course_id . '-' . $phase_index;
			?>
			<section class="cb-smap-module<?php echo $is_active_mod ? ' is-active-module' : ''; ?>" data-cb-smap-module>
				<button
					class="cb-smap-module-toggle"
					type="button"
					aria-expanded="<?php echo $is_expanded ? 'true' : 'false'; ?>"
					aria-controls="<?php echo esc_attr( $module_panel_id ); ?>"
					data-cb-smap-module-toggle
				>
					<span class="cb-smap-module-badge"><?php echo esc_html( $phase_no ); ?></span>
					<span class="cb-smap-module-text">
						<span class="cb-smap-module-heading"><?php echo esc_html( $phase_title ); ?></span>
						<span class="cb-smap-module-count"><?php echo esc_html( count( $phase_lessons ) . ' ' . __( 'topics', 'codesblock' ) ); ?></span>
					</span>
					<svg class="cb-smap-module-icon" viewBox="0 0 20 20" aria-hidden="true">
						<path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" fill="currentColor"/>
					</svg>
				</button>

				<ol id="<?php echo esc_attr( $module_panel_id ); ?>" class="cb-smap-lessons"<?php echo $is_expanded ? '' : ' hidden'; ?>>
					<?php foreach ( $phase_lessons as $lesson_index => $planned_topic ) : ?>
						<?php
						$running_item_index++;
						$position       = $lesson_index + 1;
						$lesson_post    = null;

						// Try matching published lesson
						if ( isset( $lesson_lookup_by_pos[ $phase_index ][ $position ] ) ) {
							$lesson_post = $lesson_lookup_by_pos[ $phase_index ][ $position ];
						} elseif ( 0 === $phase_index && 0 === $lesson_index && isset( $lesson_lookup_by_slug['what-system-design-is-and-why-it-matters'] ) ) {
							$lesson_post = $lesson_lookup_by_slug['what-system-design-is-and-why-it-matters'];
						}

						$is_current = false;
						if ( $lesson_id && $lesson_post && $lesson_post->ID === $lesson_id ) {
							$is_current = true;
						} elseif ( 0 === $phase_index && 0 === $lesson_index && 'what-system-design-is-and-why-it-matters' === $current_lesson_slug ) {
							$is_current = true;
						}

						$display_title = $planned_topic;
						if ( $is_current && $current_lesson_post ) {
							$display_title = $current_lesson_post->post_title;
						}
						?>
						<li class="cb-smap-lesson<?php echo $is_current ? ' is-current' : ''; ?>"<?php echo $is_current ? ' data-cb-smap-current' : ''; ?>>
							<?php if ( $lesson_post && ( $can_open_lessons || $is_current ) ) : ?>
								<a href="<?php echo esc_url( get_permalink( $lesson_post ) ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
									<span class="cb-smap-lesson-num"><?php echo esc_html( $position ); ?></span>
									<span class="cb-smap-lesson-info">
										<span class="cb-smap-lesson-title"><?php echo esc_html( $display_title ); ?></span>
										<?php if ( $is_current ) : ?>
											<span class="cb-smap-status-badge cb-smap-status-current"><?php esc_html_e( 'You are here', 'codesblock' ); ?></span>
										<?php else : ?>
											<span class="cb-smap-status-badge cb-smap-status-published"><?php esc_html_e( 'Read lesson', 'codesblock' ); ?></span>
										<?php endif; ?>
									</span>
								</a>
							<?php else : ?>
								<div class="cb-smap-lesson-placeholder">
									<span class="cb-smap-lesson-num"><?php echo esc_html( $position ); ?></span>
									<span class="cb-smap-lesson-info">
										<span class="cb-smap-lesson-title"><?php echo esc_html( $planned_topic ); ?></span>
										<span class="cb-smap-status-badge cb-smap-status-planned"><?php esc_html_e( 'Planned', 'codesblock' ); ?></span>
									</span>
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>
		<?php endforeach; ?>
	</nav>

	<!-- Footer -->
	<footer class="cb-smap-footer">
		<a class="cb-smap-footer-cta" href="<?php echo esc_url( get_permalink( $course_id ) ); ?>">
			<span><?php esc_html_e( 'Course Overview', 'codesblock' ); ?></span>
			<span aria-hidden="true">&rarr;</span>
		</a>
	</footer>
</aside>

<!-- ==========================================================================
     STICKY SIDEBAR CONTROLLER JAVASCRIPT
     Handles: Toggle, Sticky positioning, LocalStorage, Active Item Auto-Scroll,
     and Syncing the Lesson Hero roadmap counter.
     ========================================================================== -->
<script id="cb-sticky-course-map-script">
(function() {
	'use strict';

	var courseId = '<?php echo esc_js( $course_id ); ?>';
	var totalSteps = <?php echo absint( $planned_total ); ?>;
	var currentStep = <?php echo absint( $current_step ); ?>;
	var storageKey = 'codesblock_smap_hidden_' + courseId;

	var sidebar = document.querySelector('[data-cb-smap-panel]');
	var openTrigger = document.querySelector('[data-cb-smap-open]');
	var closeButtons = document.querySelectorAll('[data-cb-smap-close]');
	var moduleToggles = document.querySelectorAll('[data-cb-smap-module-toggle]');
	var activeLessonEl = document.querySelector('[data-cb-smap-current]');

	if (!sidebar || !openTrigger) return;

	function isDesktop() {
		return window.matchMedia('(min-width: 1024px)').matches;
	}

	function updateHeaderOffset() {
		var header = document.querySelector('.site-header');
		var offset = 74;
		if (header) {
			var rect = header.getBoundingClientRect();
			offset = Math.max(0, Math.ceil(rect.bottom));
		}
		document.documentElement.style.setProperty('--cb-smap-header-h', offset + 'px');
	}

	function setSidebarVisibility(isOpen, savePreference) {
		document.body.classList.toggle('cb-smap-open', isOpen);
		openTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		sidebar.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

		if (isOpen) {
			sidebar.removeAttribute('inert');
			if (activeLessonEl) {
				window.requestAnimationFrame(function() {
					activeLessonEl.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
				});
			}
		} else {
			sidebar.setAttribute('inert', '');
		}

		if (savePreference) {
			try {
				localStorage.setItem(storageKey, isOpen ? 'visible' : 'hidden');
			} catch (e) {}
		}
	}

	// Module accordion
	moduleToggles.forEach(function(toggle) {
		toggle.addEventListener('click', function() {
			var targetId = toggle.getAttribute('aria-controls');
			var panel = targetId ? document.getElementById(targetId) : null;
			if (!panel) return;

			var isExpanded = toggle.getAttribute('aria-expanded') === 'true';
			var nextState = !isExpanded;

			toggle.setAttribute('aria-expanded', nextState ? 'true' : 'false');
			panel.hidden = !nextState;
		});
	});

	// Trigger open
	openTrigger.addEventListener('click', function(e) {
		e.preventDefault();
		setSidebarVisibility(true, true);
	});

	// Close buttons (Hide button and backdrop)
	closeButtons.forEach(function(btn) {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			setSidebarVisibility(false, true);
		});
	});

	// Escape key to hide
	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape' && document.body.classList.contains('cb-smap-open')) {
			setSidebarVisibility(false, true);
			openTrigger.focus();
		}
	});

	// Dynamic header offset sync
	updateHeaderOffset();
	window.addEventListener('resize', updateHeaderOffset, { passive: true });
	window.addEventListener('scroll', updateHeaderOffset, { passive: true });

	// Initialize state from storage or default
	var savedPref = null;
	try {
		savedPref = localStorage.getItem(storageKey);
	} catch (e) {}

	// Default: On desktop, start visible; on mobile, start hidden
	var shouldBeOpen = isDesktop();
	if (savedPref === 'hidden') {
		shouldBeOpen = false;
	} else if (savedPref === 'visible' && isDesktop()) {
		shouldBeOpen = true;
	}

	setSidebarVisibility(shouldBeOpen, false);

	// Sync Lesson Hero Roadmap Step Counter to 60 steps
	if (totalSteps > 0 && currentStep > 0) {
		var heroProgressText = document.querySelector('.cb-lesson-progress small');
		if (heroProgressText) {
			heroProgressText.textContent = 'Step ' + currentStep + ' of ' + totalSteps + ' in the course roadmap';
		}
		var heroProgressBar = document.querySelector('.cb-lesson-progress');
		if (heroProgressBar) {
			var pct = Math.max(2, Math.round((currentStep / totalSteps) * 100));
			heroProgressBar.style.setProperty('--cb-lesson-progress', pct + '%');
		}
	}
})();
</script>
