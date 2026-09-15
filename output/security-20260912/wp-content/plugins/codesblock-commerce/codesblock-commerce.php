<?php
/**
 * Plugin Name: CodesBlock Commerce
 * Description: Connects the CodesBlock course experience to Paid Memberships Pro and social login.
 * Version: 1.6.1
 * Requires PHP: 7.4
 * Requires Plugins: codesblock-core, paid-memberships-pro
 * Author: CodesBlock
 * Text Domain: codesblock-commerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CBCOMMERCE_VERSION', '1.6.1' );

function cbcommerce_seed_ai_agents_course() {
	$existing = get_page_by_path( 'build-production-ready-ai-agents', OBJECT, 'course' );
	if ( $existing ) {
		return absint( $existing->ID );
	}

	$content  = <<<'HTML'
<!-- wp:heading -->
<h2 class="wp-block-heading">Build agents that do useful work, not impressive demos</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>This practical course takes you from a single tool-calling loop to a production-ready agent system with typed tools, durable state, evaluations, observability, and human approval gates. You will build a research-and-operations agent that can plan work, use APIs safely, recover from failures, and explain what it did.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">The project</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>You will ship an agent that receives a business question, gathers evidence from approved sources, produces a cited brief, and asks for approval before taking any external action. Every module adds one production concern to the same codebase.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">How the course works</h3>
<!-- /wp:heading -->
<!-- wp:list -->
<ul><li>Short concept lessons followed by implementation labs</li><li>Architecture reviews that explain the tradeoffs</li><li>Failure-injection exercises for timeouts, malformed tool output, and prompt attacks</li><li>A final launch checklist covering cost, latency, privacy, and rollback</li></ul>
<!-- /wp:list -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Prerequisites</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>You should be comfortable reading Python or TypeScript, calling a REST API, and using Git. Prior machine-learning experience is not required.</p>
<!-- /wp:paragraph -->
HTML;

	$postarr = array(
		'ID'           => $existing ? $existing->ID : 0,
		'post_type'    => 'course',
		'post_status'  => 'publish',
		'post_name'    => 'build-production-ready-ai-agents',
		'post_title'   => 'Build Production-Ready AI Agents',
		'post_excerpt' => 'Design, build, evaluate, and ship reliable AI agents with safe tool use, memory, observability, and human approval.',
		'post_content' => $content,
	);
	$course_id = wp_insert_post( $postarr, true );
	if ( is_wp_error( $course_id ) ) {
		return 0;
	}

	$meta = array(
		'_course_price'          => 'Included in Pro',
		'_course_original_price' => '',
		'_course_level'          => 'Intermediate',
		'_course_duration'       => '6 weeks · 18 hours',
		'_course_badge'          => 'New · Project-based',
		'_course_features'       => "Lifetime access\n32 focused lessons\n8 implementation labs\nProduction starter repo\nCertificate of completion\nSaved learning progress",
		'_course_what_you_learn' => "Design an agent loop with explicit stop conditions\nBuild typed, permissioned tools with safe failure handling\nUse short-term and durable memory without leaking sensitive data\nAdd human approval before consequential actions\nCreate evals for correctness, safety, latency, and cost\nInstrument traces and debug multi-step failures\nDefend against prompt injection and untrusted tool output\nDeploy with budgets, retries, fallbacks, and rollback controls",
		'_course_syllabus'       => "## Module 1 · Agent Foundations\n- What makes a workflow an agent?\n- The model–tool–environment loop\n- Your first bounded tool-calling agent\n- Lab: research brief agent\n## Module 2 · Tools You Can Trust\n- Typed tool contracts and validation\n- Permissions, idempotency, and retries\n- Treating tool output as untrusted input\n- Lab: safe web and database tools\n## Module 3 · State, Memory, and Context\n- Session state versus durable memory\n- Retrieval strategies and context budgets\n- Privacy, retention, and deletion\n- Lab: resumable agent runs\n## Module 4 · Planning and Multi-Agent Patterns\n- When planning improves results\n- Handoffs, specialists, and orchestration\n- Avoiding loops and coordination overhead\n- Lab: planner and reviewer workflow\n## Module 5 · Evaluations and Safety\n- Building a representative eval set\n- Task success, groundedness, cost, and latency\n- Prompt injection and data exfiltration defenses\n- Lab: automated regression suite\n## Module 6 · Production Operations\n- Tracing, logs, and incident debugging\n- Rate limits, fallbacks, and budget controls\n- Human approval and audit trails\n- Capstone: launch a production-ready agent",
		'_course_ai_summary'     => 'A hands-on course for developers who want to move beyond agent demos. You will build one production system while adding safe tools, memory, evaluations, observability, and approval gates.',
		'_course_ai_faqs'        => wp_json_encode(
			array(
				array( 'q' => 'Do I need machine learning experience?', 'a' => 'No. You need basic Python or TypeScript and API experience; the course focuses on software engineering for agent systems.' ),
				array( 'q' => 'What will I build?', 'a' => 'A research-and-operations agent that gathers evidence, produces a cited brief, persists state, and requires approval before external actions.' ),
				array( 'q' => 'Is this course framework specific?', 'a' => 'No. Examples use lightweight primitives so the architecture transfers to the major agent SDKs and frameworks.' ),
				array( 'q' => 'How do I get access?', 'a' => 'The course is included with CodesBlock Pro and Lifetime membership.' ),
			)
		),
		'_codesblock_recommended' => '1',
	);
	foreach ( $meta as $key => $value ) {
		update_post_meta( $course_id, $key, $value );
	}

	update_option( 'codesblock_ai_agents_course_id', $course_id, false );
	return $course_id;
}

/**
 * Seed a useful starter library as normal WordPress content.
 *
 * Existing slugs are never overwritten, so every article and course remains
 * safe to edit from WordPress admin after the initial setup.
 */
function cbcommerce_seed_editable_library() {
	$articles = array(
		array(
			'slug'        => 'why-ai-agents-fail-in-production',
			'title'       => 'Why AI Agents Fail in Production: 7 Guardrails That Matter',
			'excerpt'     => 'A practical review of the controls that turn an impressive agent demo into a system a team can operate safely.',
			'category'    => 'Production AI',
			'recommended' => true,
			'premium'     => false,
			'content'     => <<<'HTML'
<!-- wp:paragraph --><p>Agent demos usually fail in production for ordinary software reasons: unclear permissions, weak error handling, missing measurements, and no safe way to stop. The model is only one component of the system.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">1. Give every tool a narrow contract</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Define accepted inputs, returned outputs, timeouts, and failure states. Reject unexpected fields before a tool reaches a database, browser, or external API.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">2. Separate suggestions from actions</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>An agent may draft an email, refund, deployment, or database change. A person or policy engine should approve consequential actions before they leave the system.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">3. Test the failures you expect</h2><!-- /wp:heading -->
<!-- wp:list --><ul><li>Malformed or adversarial tool output</li><li>Rate limits, timeouts, and partial responses</li><li>Prompt injection inside retrieved content</li><li>Loops that consume the entire token or cost budget</li></ul><!-- /wp:list -->
<!-- wp:heading --><h2 class="wp-block-heading">4. Measure outcomes, not confidence</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Track task success, groundedness, latency, cost, human corrections, and unsafe action attempts. A confident answer is not evidence that the task was completed correctly.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">The production checklist</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Ship with least-privilege tools, explicit stop conditions, durable traces, representative evaluations, privacy rules, human approval, budget limits, and a rollback plan. If one is missing, the agent is still a prototype.</p><!-- /wp:paragraph -->
HTML,
		),
		array(
			'slug'        => 'system-design-interview-45-minute-framework',
			'title'       => 'System Design Interview: A 45-Minute Answer Framework',
			'excerpt'     => 'A calm, repeatable structure for requirements, scale, architecture, bottlenecks, and tradeoffs under interview pressure.',
			'category'    => 'System Design',
			'recommended' => true,
			'premium'     => false,
			'content'     => <<<'HTML'
<!-- wp:paragraph --><p>A strong system design answer is a sequence of decisions, not a tour of every technology you know. Use the clock to make your reasoning visible.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Minutes 0-5: clarify the problem</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Confirm users, core actions, consistency needs, latency expectations, geography, and what is explicitly out of scope. Write down the three requirements that will drive the architecture.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Minutes 5-10: estimate the load</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Use rounded assumptions for requests per second, storage growth, object size, and read-to-write ratio. The goal is to expose scale-sensitive choices, not to perform perfect arithmetic.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Minutes 10-25: draw the first complete path</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Show clients, APIs, data stores, queues, caches, and the main read and write flows. Start simple. Name the failure boundary and the source of truth.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Minutes 25-38: go deep where it matters</h2><!-- /wp:heading -->
<!-- wp:list --><ul><li>Partitioning and hot-key risk</li><li>Consistency and duplicate processing</li><li>Cache invalidation and stale reads</li><li>Backpressure, retries, and dead-letter handling</li></ul><!-- /wp:list -->
<!-- wp:heading --><h2 class="wp-block-heading">Minutes 38-45: close with tradeoffs</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Summarize the design, the biggest risk, what you would measure, and the next improvement you would make with more time. Interviewers remember a clear close.</p><!-- /wp:paragraph -->
HTML,
		),
		array(
			'slug'        => 'evaluate-ai-features-before-launch',
			'title'       => 'From Prompt to Production: Evaluate AI Features Before Launch',
			'excerpt'     => 'A lightweight evaluation loop for teams shipping LLM features without relying on vibes or cherry-picked demos.',
			'category'    => 'Production AI',
			'recommended' => false,
			'premium'     => false,
			'content'     => <<<'HTML'
<!-- wp:paragraph --><p>Before changing prompts or models, write down what a good result means. A small, representative evaluation set is more useful than dozens of polished screenshots.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Build cases from real work</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Collect normal requests, difficult edge cases, ambiguous inputs, and hostile content. Remove sensitive data and preserve the structure of the task.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Score the dimensions separately</h2><!-- /wp:heading -->
<!-- wp:list --><ul><li>Task completion and factual support</li><li>Safety and policy compliance</li><li>Latency and cost</li><li>Clarity for the person who must use the result</li></ul><!-- /wp:list -->
<!-- wp:heading --><h2 class="wp-block-heading">Keep human review in the loop</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Automated graders help with scale, but a recurring human review catches subtle regressions. Record disagreements and turn them into better rubrics or new test cases.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Ship with a rollback threshold</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Decide in advance which failure rate, latency increase, or cost spike pauses the rollout. Evaluations are most valuable when they drive an operational decision.</p><!-- /wp:paragraph -->
HTML,
		),
		array(
			'slug'        => 'senior-engineer-interview-stories',
			'title'       => 'Senior Engineer Interviews: Turn Project Work Into Strong Evidence',
			'excerpt'     => 'A practical way to turn architecture, delivery, and incident work into concise senior-level interview stories.',
			'category'    => 'Career Growth',
			'recommended' => true,
			'premium'     => true,
			'content'     => <<<'HTML'
<!-- wp:paragraph --><p>Senior interviews reward judgment. The strongest stories show the constraints you saw, the decision you owned, the tradeoff you accepted, and what changed because of your work.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Start with the engineering tension</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Replace “I built a service” with the real tension: reliability versus speed, migration risk versus product deadlines, or team autonomy versus platform consistency.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Make your decision visible</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Explain the options, the evidence you gathered, who you involved, and why the chosen path was appropriate at that moment. Avoid pretending there was only one correct answer.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Show leverage beyond your own code</h2><!-- /wp:heading -->
<!-- wp:list --><ul><li>A decision framework another team reused</li><li>An incident practice that reduced recovery time</li><li>A migration plan that made delivery safer</li><li>Mentoring that changed ownership or quality</li></ul><!-- /wp:list -->
<!-- wp:heading --><h2 class="wp-block-heading">Close with the honest lesson</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Name what you would change now. A credible reflection demonstrates stronger judgment than a story where every decision appears perfect.</p><!-- /wp:paragraph -->
HTML,
		),
	);

	foreach ( $articles as $index => $article ) {
		$existing = get_page_by_path( $article['slug'], OBJECT, 'post' );
		$replacing_default = false;

		if ( ! $existing && 0 === $index ) {
			$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
			if ( $hello && 'Hello world!' === $hello->post_title ) {
				$existing          = $hello;
				$replacing_default = true;
			}
		}

		if ( $existing && ! $replacing_default ) {
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'ID'             => $existing ? $existing->ID : 0,
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'post_name'      => $article['slug'],
				'post_title'     => $article['title'],
				'post_excerpt'   => $article['excerpt'],
				'post_content'   => $article['content'],
				'comment_status' => 'closed',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		$term = term_exists( $article['category'], 'category' );
		if ( ! $term ) {
			$term = wp_insert_term( $article['category'], 'category' );
		}
		if ( ! is_wp_error( $term ) ) {
			wp_set_post_categories( $post_id, array( absint( is_array( $term ) ? $term['term_id'] : $term ) ) );
		}

		update_post_meta( $post_id, '_codesblock_recommended', $article['recommended'] ? '1' : '0' );
		update_post_meta( $post_id, '_codesblock_premium', $article['premium'] ? '1' : '0' );

		if ( $replacing_default ) {
			foreach ( get_comments( array( 'post_id' => $post_id ) ) as $comment ) {
				if ( false !== strpos( (string) $comment->comment_content, 'Hi, this is a comment.' ) ) {
					wp_set_comment_status( $comment->comment_ID, 'trash' );
				}
			}
		}
	}

	$courses = array(
		array(
			'slug'     => 'system-design-interview-sprint',
			'title'    => 'System Design Interview Sprint',
			'excerpt'  => 'Learn system design the proven way: read, reason, sketch, and practise. Then use an AI study companion to explain selected text, explore trade-offs, and keep the follow-up going.',
			'content'  => '<!-- wp:heading --><h2 class="wp-block-heading">Turn ambiguous prompts into clear, defensible systems</h2><!-- /wp:heading --><!-- wp:paragraph --><p>This sprint teaches a repeatable interview operating system: clarify the problem, estimate the scale, draw a complete path, choose components from constraints, and defend the trade-offs. You will work through realistic designs instead of memorising finished diagrams.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Learn deeply, without getting stuck</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Read each lesson at your own pace and form your answer first. When a sentence, pattern, or decision needs more context, use the course study companion to ask for a simpler explanation, explore an alternative, research the surrounding concept, or continue with follow-up questions.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">What you will leave with</h3><!-- /wp:heading --><!-- wp:list --><ul><li>A reusable 45-minute interview framework</li><li>A personal architecture playbook with estimation shortcuts</li><li>Six end-to-end design walkthroughs and timed drills</li><li>Trade-off notes you can explain in your own words</li><li>A final mock-review checklist for the day before your interview</li></ul><!-- /wp:list -->',
			'price'    => 'Included in Pro',
			'original' => '',
			'level'    => 'Intermediate - adaptable',
			'duration' => '4 weeks - 12 hours',
			'badge'    => 'AI-accelerated',
			'features' => "AI-assisted lesson companion\nHighlight text and ask in context\n24 focused lessons\n6 end-to-end design drills\nSaved learning progress",
			'learn'    => "Run a repeatable 45-minute system design framework\nClarify requirements and estimate scale with useful precision\nChoose databases, caches, queues, and protocols from constraints\nExplain consistency, reliability, cost, and operational trade-offs\nHandle interviewer follow-ups without losing the design thread\nAdapt the same method from intern fundamentals to senior depth",
			'syllabus' => "## Module 1 - Your interview operating system\n- What interviewers evaluate at each level\n- The 45-minute delivery framework\n- Requirements, scope, and success metrics\n- Drawing the first complete path\n## Module 2 - Scale before components\n- Back-of-the-envelope estimates\n- Latency, throughput, storage, and bandwidth\n- Read-heavy and write-heavy workloads\n- Turning numbers into architecture decisions\n## Module 3 - Data and consistency\n- Data models and access patterns\n- SQL, NoSQL, and search trade-offs\n- Replication, partitioning, and hot keys\n- Strong, eventual, and read-your-own-writes consistency\n## Module 4 - Speed, resilience, and operations\n- Caching strategies and invalidation\n- Queues, streams, retries, and backpressure\n- Rate limits, idempotency, and graceful degradation\n- Observability and failure-mode review\n## Module 5 - End-to-end design labs\n- URL shortener and rate limiter\n- News feed and chat system\n- Video platform and ride sharing\n- Defending alternatives and deeper follow-ups\n## Module 6 - Interview sprint\n- Timed requirement and estimation drills\n- Architecture walkthroughs at three experience levels\n- AI-assisted challenge and follow-up practice\n- Final mock review and day-before checklist",
			'faqs'     => array(
				array( 'q' => 'Is this suitable for beginners?', 'a' => 'Yes. Early-career learners can follow the foundations path, while experienced engineers can move faster into scale, reliability, and deeper trade-offs.' ),
				array( 'q' => 'How does the AI study companion help?', 'a' => 'It is designed to help you ask about selected lesson text, request simpler explanations, explore alternatives, and continue with follow-up questions while you read.' ),
				array( 'q' => 'What will I produce?', 'a' => 'You will build a reusable interview framework, estimation notes, six system walkthroughs, trade-off cards, and a final mock-review checklist.' ),
			),
		),
		array(
			'slug'     => 'dsa-patterns-with-python',
			'title'    => 'DSA Patterns With Python',
			'excerpt'  => 'Learn the small set of reusable patterns behind common coding interview problems.',
			'content'  => '<!-- wp:heading --><h2 class="wp-block-heading">Stop memorizing isolated solutions</h2><!-- /wp:heading --><!-- wp:paragraph --><p>This free starter course teaches how to recognize and apply sliding windows, two pointers, hash maps, stacks, trees, graphs, and dynamic programming.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Practice format</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Each lesson includes a recognition clue, a worked example, complexity notes, and a short problem set.</p><!-- /wp:paragraph -->',
			'price'    => 'Free',
			'original' => '',
			'level'    => 'Beginner',
			'duration' => '2 weeks - 5 hours',
			'badge'    => 'Free starter',
			'features' => "Free full access\n14 focused lessons\n24 practice prompts\nPython reference solutions\nSaved learning progress",
			'learn'    => "Recognize common interview problem shapes\nChoose an appropriate data structure quickly\nExplain time and space complexity clearly\nWrite readable Python under time pressure\nReview mistakes with a repeatable checklist",
			'syllabus' => "## Module 1 - Arrays and strings\n- Two pointers\n- Sliding windows\n- Prefix sums\n## Module 2 - Maps, stacks, and queues\n- Counting and lookup\n- Monotonic stacks\n- Breadth-first patterns\n## Module 3 - Trees and graphs\n- Traversals\n- Search and visited state\n- Topological ordering\n## Module 4 - Dynamic programming\n- State and transitions\n- Memoization\n- Tabulation",
		),
		array(
			'slug'     => 'reliable-llm-apps',
			'title'    => 'Reliable LLM Apps: Evals, RAG, and Observability',
			'excerpt'  => 'Build an evidence-backed LLM feature with retrieval, evaluations, tracing, cost controls, and a safe rollout.',
			'content'  => '<!-- wp:heading --><h2 class="wp-block-heading">Build an LLM feature your team can trust</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Move beyond prompt tweaking. You will create a retrieval-backed assistant, measure its behavior, inspect traces, and ship it behind a controlled rollout.</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">The project</h3><!-- /wp:heading --><!-- wp:paragraph --><p>A support copilot that cites approved knowledge, abstains when evidence is weak, and reports quality, latency, and cost.</p><!-- /wp:paragraph -->',
			'price'    => 'Included in Pro',
			'original' => '',
			'level'    => 'Advanced',
			'duration' => '4 weeks - 12 hours',
			'badge'    => 'Production track',
			'features' => "Lifetime access\n24 focused lessons\n7 implementation labs\nEvaluation starter kit\nSaved learning progress",
			'learn'    => "Design retrieval around evidence and permissions\nBuild representative evaluation datasets\nMeasure groundedness, usefulness, latency, and cost\nTrace multi-step LLM calls and retrieval failures\nRoll out safely with thresholds and fallbacks",
			'syllabus' => "## Module 1 - Product and data boundaries\n- Define the task\n- Data permissions\n- Failure taxonomy\n## Module 2 - Retrieval that earns trust\n- Chunking and metadata\n- Ranking and citations\n- Abstention rules\n## Module 3 - Evaluations\n- Representative test sets\n- Rubrics and graders\n- Regression gates\n## Module 4 - Operations\n- Tracing and dashboards\n- Cost and latency budgets\n- Safe rollout and rollback",
		),
	);

	foreach ( $courses as $course ) {
		if ( get_page_by_path( $course['slug'], OBJECT, 'course' ) ) {
			continue;
		}

		$course_id = wp_insert_post(
			array(
				'post_type'    => 'course',
				'post_status'  => 'publish',
				'post_name'    => $course['slug'],
				'post_title'   => $course['title'],
				'post_excerpt' => $course['excerpt'],
				'post_content' => $course['content'],
			),
			true
		);

		if ( is_wp_error( $course_id ) ) {
			continue;
		}

		$meta = array(
			'_course_price'          => $course['price'],
			'_course_original_price' => $course['original'],
			'_course_level'          => $course['level'],
			'_course_duration'       => $course['duration'],
			'_course_badge'          => $course['badge'],
			'_course_features'       => $course['features'],
			'_course_what_you_learn' => $course['learn'],
			'_course_syllabus'       => $course['syllabus'],
			'_course_ai_summary'     => $course['excerpt'],
			'_course_ai_faqs'        => wp_json_encode( isset( $course['faqs'] ) ? $course['faqs'] : array() ),
			'_codesblock_recommended' => '1',
		);

		foreach ( $meta as $key => $value ) {
			update_post_meta( $course_id, $key, $value );
		}
	}
}

/**
 * Add one restrained launch coupon that remains editable in PMPro admin.
 */
function cbcommerce_seed_welcome_coupon() {
	global $wpdb;

	$pro_id = cbcommerce_level_id( 'pro' );
	if ( ! $pro_id || ! class_exists( 'PMPro_Discount_Code' ) ) {
		return;
	}

	$existing_id   = absint( $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->pmpro_discount_codes} WHERE code = %s LIMIT 1", 'WELCOME25' ) ) );
	$code          = $existing_id ? new PMPro_Discount_Code( $existing_id ) : new PMPro_Discount_Code();
	$code->code    = 'WELCOME25';
	$code->starts  = $existing_id && ! empty( $code->starts ) ? $code->starts : current_time( 'Y-m-d' );
	$code->expires = $existing_id && ! empty( $code->expires ) ? $code->expires : wp_date( 'Y-m-d', strtotime( '+90 days', current_time( 'timestamp' ) ) );
	$code->uses    = $existing_id && ! empty( $code->uses ) ? $code->uses : 100;
	$code->levels  = array(
		$pro_id => array(
			'initial_payment'   => 749,
			'billing_amount'    => 0,
			'cycle_number'      => 0,
			'cycle_period'      => '',
			'billing_limit'     => 0,
			'trial_amount'      => 0,
			'trial_limit'       => 0,
			'expiration_number' => 1,
			'expiration_period' => 'Month',
		),
	);
	$code->save();
}

function cbcommerce_upsert_level( $key, $data ) {
	if ( ! class_exists( 'PMPro_Membership_Level' ) ) {
		return 0;
	}

	$stored = (array) get_option( 'codesblock_membership_levels', array() );
	$level  = ! empty( $stored[ $key ] ) ? new PMPro_Membership_Level( absint( $stored[ $key ] ) ) : false;

	if ( ! $level ) {
		foreach ( pmpro_getAllLevels( true, true ) as $candidate ) {
			if ( $candidate->name === $data['name'] ) {
				$level = new PMPro_Membership_Level( $candidate->id );
				break;
			}
		}
	}

	if ( ! $level ) {
		$level = new PMPro_Membership_Level();
	}

	foreach ( $data as $property => $value ) {
		$level->{$property} = $value;
	}
	$level->allow_signups = 1;
	$level->save();

	$stored[ $key ] = absint( $level->id );
	update_option( 'codesblock_membership_levels', $stored, false );
	return absint( $level->id );
}

function cbcommerce_setup_memberships() {
	if ( ! function_exists( 'pmpro_generatePages' ) || ! class_exists( 'PMPro_Membership_Level' ) ) {
		return;
	}

	pmpro_generatePages(
		array(
			'account'             => 'Member Dashboard',
			'billing'             => 'Billing',
			'cancel'              => 'Cancel Membership',
			'checkout'            => 'Secure Checkout',
			'confirmation'        => 'Welcome to CodesBlock',
			'invoice'             => 'Order Receipt',
			'levels'              => 'Choose Your Plan',
			'login'               => array(
				'title'   => 'Member Sign In',
				'content' => '[codesblock_social_login][pmpro_login]',
			),
			'member_profile_edit' => 'Edit Profile',
		)
	);

	$embedded_shortcodes = array(
		'checkout' => "[codesblock_social_login]\n[pmpro_checkout]",
		'login'    => "[codesblock_social_login]\n[pmpro_login]",
	);
	foreach ( $embedded_shortcodes as $page_name => $page_content ) {
		$page_id = absint( get_option( 'pmpro_' . $page_name . '_page_id' ) );
		if ( $page_id && false === strpos( (string) get_post_field( 'post_content', $page_id ), '[codesblock_social_login]' ) ) {
			wp_update_post( array( 'ID' => $page_id, 'post_content' => $page_content ) );
		}
	}

	cbcommerce_upsert_level(
		'starter',
		array(
			'name'              => 'Starter',
			'description'       => 'Free articles, selected lessons, community updates, and learning progress.',
			'confirmation'      => 'Welcome to CodesBlock. Your free member account is ready.',
			'initial_payment'   => 0,
			'billing_amount'    => 0,
			'cycle_number'      => 0,
			'cycle_period'      => '',
			'billing_limit'     => 0,
			'trial_amount'      => 0,
			'trial_limit'       => 0,
			'expiration_number' => 0,
			'expiration_period' => '',
		)
	);

	cbcommerce_upsert_level(
		'pro',
		array(
			'name'              => 'Pro Monthly Pass',
			'description'       => 'Thirty days of every paid course, implementation resources, certificates, and saved progress. Renew only when you want to continue.',
			'confirmation'      => 'Your 30-day CodesBlock Pro pass is active. Every paid course is unlocked.',
			'initial_payment'   => 999,
			'billing_amount'    => 0,
			'cycle_number'      => 0,
			'cycle_period'      => '',
			'billing_limit'     => 0,
			'trial_amount'      => 0,
			'trial_limit'       => 0,
			'expiration_number' => 1,
			'expiration_period' => 'Month',
		)
	);

	cbcommerce_upsert_level(
		'pro_annual',
		array(
			'name'              => 'Pro Annual Pass',
			'description'       => 'A full year of every paid course, implementation resources, certificates, and saved progress. One payment, no auto-renewal.',
			'confirmation'      => 'Your one-year CodesBlock Pro pass is active.',
			'initial_payment'   => 8499,
			'billing_amount'    => 0,
			'cycle_number'      => 0,
			'cycle_period'      => '',
			'billing_limit'     => 0,
			'trial_amount'      => 0,
			'trial_limit'       => 0,
			'expiration_number' => 1,
			'expiration_period' => 'Year',
		)
	);

	cbcommerce_upsert_level(
		'lifetime',
		array(
			'name'              => 'Lifetime',
			'description'       => 'Permanent access to all current and future CodesBlock courses.',
			'confirmation'      => 'Lifetime access is active. Welcome to the inner circle.',
			'initial_payment'   => 19999,
			'billing_amount'    => 0,
			'cycle_number'      => 0,
			'cycle_period'      => '',
			'billing_limit'     => 0,
			'trial_amount'      => 0,
			'trial_limit'       => 0,
			'expiration_number' => 0,
			'expiration_period' => '',
		)
	);

	update_option( 'users_can_register', 1 );
	update_option( 'pmpro_currency', 'INR' );

	$social_flow_page = get_page_by_path( 'complete-your-profile' );
	$social_flow_id   = wp_insert_post(
		array(
			'ID'           => $social_flow_page ? $social_flow_page->ID : 0,
			'post_title'   => 'Complete Your Profile',
			'post_name'    => 'complete-your-profile',
			'post_content' => '[nextend_social_login_register_flow]',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		),
		true
	);
	if ( ! is_wp_error( $social_flow_id ) ) {
		update_option( 'cbcommerce_social_flow_page_id', absint( $social_flow_id ), false );
	}

	$learning_page    = get_page_by_path( 'my-learning', OBJECT, 'page' );
	$learning_page_id = wp_insert_post(
		array(
			'ID'             => $learning_page ? $learning_page->ID : 0,
			'post_title'     => 'My Learning',
			'post_name'      => 'my-learning',
			'post_content'   => $learning_page ? $learning_page->post_content : '',
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
		),
		true
	);
	if ( ! is_wp_error( $learning_page_id ) ) {
		update_post_meta( $learning_page_id, '_wp_page_template', 'page-my-learning.php' );
		update_option( 'cbcommerce_learning_page_id', absint( $learning_page_id ), false );
	}

	cbcommerce_seed_ai_agents_course();
	cbcommerce_seed_editable_library();
	cbcommerce_seed_welcome_coupon();
	if ( function_exists( 'cbcommerce_mailpoet_list_id' ) ) {
		cbcommerce_mailpoet_list_id();
	}
	update_option( 'cbcommerce_setup_version', CBCOMMERCE_VERSION, false );
}

register_activation_hook( __FILE__, 'cbcommerce_setup_memberships' );
add_action(
	'admin_init',
	function () {
		$setup_version = get_option( 'cbcommerce_setup_version' );
		if ( ! $setup_version || CBCOMMERCE_VERSION !== $setup_version ) {
			cbcommerce_setup_memberships();
		}
	}
);

function cbcommerce_level_id( $key ) {
	$levels = (array) get_option( 'codesblock_membership_levels', array() );
	return ! empty( $levels[ $key ] ) ? absint( $levels[ $key ] ) : 0;
}

function cbcommerce_payments_ready() {
	$gateway = trim( (string) get_option( 'pmpro_gateway', '' ) );
	if ( '' === $gateway ) {
		return false;
	}

	if ( 'knit_pay' === $gateway ) {
		$config_ids = get_option( 'pmpro_knit_pay_config_id', array() );
		if ( empty( $config_ids ) ) {
			$config_ids = get_option( 'pronamic_pay_config_id', array() );
		}
		return ! empty( array_filter( (array) $config_ids ) );
	}

	return true;
}

/**
 * True only when the India-focused Knit Pay checkout has a configuration.
 */
function cbcommerce_india_payments_ready() {
	return 'knit_pay' === trim( (string) get_option( 'pmpro_gateway', '' ) ) && cbcommerce_payments_ready();
}

function cbcommerce_checkout_url( $key, $discount_code = '', $force_checkout = false ) {
	if ( 'starter' !== $key && ! $force_checkout && ! cbcommerce_payments_ready() ) {
		return home_url( '/#newsletter' );
	}

	$level_id = cbcommerce_level_id( $key );
	if ( $level_id && function_exists( 'pmpro_url' ) ) {
		$args = array( 'level' => $level_id );
		if ( $discount_code ) {
			$args['discount_code'] = sanitize_key( $discount_code );
		}
		return add_query_arg( $args, pmpro_url( 'checkout' ) );
	}
	return wp_login_url( home_url( '/courses/' ) );
}

/**
 * Return the active, directly claimable launch offer for the announcement bar.
 */
function cbcommerce_promo_offer() {
	if ( ! cbcommerce_payments_ready() ) {
		return false;
	}

	global $wpdb;
	$table = isset( $wpdb->pmpro_discount_codes ) ? $wpdb->pmpro_discount_codes : $wpdb->prefix . 'pmpro_discount_codes';
	$code  = $wpdb->get_row( $wpdb->prepare( "SELECT code, starts, expires FROM {$table} WHERE code = %s LIMIT 1", 'WELCOME25' ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$today = current_time( 'Y-m-d' );
	if ( ! $code || ( $code->starts && $code->starts > $today ) || ( $code->expires && '0000-00-00' !== $code->expires && $code->expires < $today ) ) {
		return false;
	}

	return array(
		'campaign' => 'welcome25-pro',
		'badge'    => __( '25% off', 'codesblock-commerce' ),
		'text'     => __( 'WELCOME25 takes 25% off your first Pro month.', 'codesblock-commerce' ),
		'cta'      => __( 'Claim Pro offer', 'codesblock-commerce' ),
		'url'      => cbcommerce_checkout_url( 'pro', 'WELCOME25' ),
	);
}

function cbcommerce_user_has_paid_access( $user_id = null ) {
	$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
	if ( $user instanceof WP_User && cbcommerce_user_can_access_admin( $user ) ) {
		return true;
	}
	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) ) {
		return false;
	}

	$paid_levels = array_filter( array( cbcommerce_level_id( 'pro' ), cbcommerce_level_id( 'pro_annual' ), cbcommerce_level_id( 'lifetime' ) ) );
	return ! empty( $paid_levels ) && pmpro_hasMembershipLevel( $paid_levels, $user_id );
}

/**
 * Return a privacy-preserving request fingerprint for short-lived abuse limits.
 */
function cbcommerce_request_fingerprint( $scope, $identity = '' ) {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$ip_hash = hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) );
	$subject = '-account' === substr( $scope, -8 ) ? strtolower( $identity ) : strtolower( $identity ) . '|' . $ip_hash;
	return 'cbcommerce_' . md5( $scope . '|' . $subject );
}

/**
 * Read an abuse-limit state directly so concurrent PHP workers do not reuse a
 * stale per-process option cache while performing compare-and-swap updates.
 */
function cbcommerce_get_rate_limit_state( $key ) {
	global $wpdb;

	$raw = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
			$key
		)
	); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	return null === $raw ? null : maybe_unserialize( $raw );
}

function cbcommerce_rate_limit_reached( $scope, $identity, $limit, $window ) {
	global $wpdb;

	$key       = cbcommerce_request_fingerprint( $scope, $identity );
	$lock_name = 'cbcommerce_rate_' . md5( $key );
	$acquired  = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 1)', $lock_name ) );

	/* Fail closed when the counter cannot be updated safely. */
	if ( 1 !== $acquired ) {
		return true;
	}

	try {
		$now    = time();
		$limit  = max( 1, absint( $limit ) );
		$window = max( 1, absint( $window ) );
		$state  = cbcommerce_get_rate_limit_state( $key );

		if ( ! is_array( $state ) || empty( $state['expires'] ) || absint( $state['expires'] ) <= $now ) {
			$state = array(
				'count'   => 0,
				'expires' => $now + $window,
			);
		}

		$count = isset( $state['count'] ) ? absint( $state['count'] ) : 0;
		if ( $count >= $limit ) {
			return true;
		}

		$state['count'] = $count + 1;
		update_option( $key, $state, false );
		return false;
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock_name ) );
	}
}

function cbcommerce_allowed_interests() {
	return array(
		'ai-coding'     => __( 'AI & coding', 'codesblock-commerce' ),
		'interviews'    => __( 'Interview preparation', 'codesblock-commerce' ),
		'career-growth' => __( 'Career growth', 'codesblock-commerce' ),
	);
}

function cbcommerce_sanitize_interests( $value ) {
	$requested = is_array( $value ) ? array_map( 'sanitize_key', wp_unslash( $value ) ) : array();
	return array_values( array_intersect( $requested, array_keys( cbcommerce_allowed_interests() ) ) );
}

/**
 * Add or restore a consented subscriber in MailPoet.
 */
function cbcommerce_mailpoet_list_id() {
	if ( ! class_exists( 'MailPoet\\API\\API' ) ) {
		return 0;
	}

	try {
		$api       = MailPoet\API\API::MP( 'v1' );
		$lists     = $api->getLists();
		$list_id   = absint( get_option( 'cbcommerce_mailpoet_list_id', 0 ) );
		$list_ids  = array_map( 'absint', wp_list_pluck( $lists, 'id' ) );

		if ( $list_id && in_array( $list_id, $list_ids, true ) ) {
			return $list_id;
		}

		$list_id = 0;

		foreach ( $lists as $list ) {
			if ( ! empty( $list['id'] ) && isset( $list['name'] ) && 'Newsletter mailing list' === $list['name'] ) {
				$list_id = absint( $list['id'] );
				break;
			}
		}

		if ( ! $list_id && ! empty( $lists[0]['id'] ) ) {
			$list_id = absint( $lists[0]['id'] );
		}

		if ( $list_id ) {
			update_option( 'cbcommerce_mailpoet_list_id', $list_id, false );
		}
		return $list_id;
	} catch ( Throwable $error ) {
		return 0;
	}
}

function cbcommerce_subscribe_mailpoet( $email, $first_name = '', $interests = array(), $source = 'CodesBlock website' ) {
	if ( ! class_exists( 'MailPoet\\API\\API' ) ) {
		return new WP_Error( 'mailing_unavailable', __( 'Newsletter service is temporarily unavailable.', 'codesblock-commerce' ) );
	}

	try {
		$api     = MailPoet\API\API::MP( 'v1' );
		$list_id = cbcommerce_mailpoet_list_id();
		if ( ! $list_id ) {
			return new WP_Error( 'mailing_list_missing', __( 'Newsletter list is not configured yet.', 'codesblock-commerce' ) );
		}

		try {
			$subscriber = $api->getSubscriber( $email );
			$api->subscribeToList( $subscriber['id'], $list_id, array( 'send_confirmation_email' => true ) );
		} catch ( Throwable $existing_error ) {
			$api->addSubscriber(
				array(
					'email'      => $email,
					'first_name' => $first_name,
				),
				array( $list_id ),
				array( 'send_confirmation_email' => true )
			);
		}

		foreach ( cbcommerce_sanitize_interests( $interests ) as $interest ) {
			$labels = cbcommerce_allowed_interests();
			if ( isset( $labels[ $interest ] ) ) {
				$api->tagSubscriber( $email, 'Interest: ' . $labels[ $interest ] );
			}
		}
		$api->tagSubscriber( $email, 'Source: ' . sanitize_text_field( $source ) );
		return true;
	} catch ( Throwable $error ) {
		return new WP_Error( 'mailing_error', __( 'We could not save your subscription. Please try again.', 'codesblock-commerce' ) );
	}
}

/**
 * Create a private, unique WordPress login from an email address.
 * Members sign in with email, so the generated username never adds signup work.
 */
function cbcommerce_username_from_email( $email ) {
	$local_part = sanitize_user( strtok( $email, '@' ), true );
	$base       = strlen( $local_part ) >= 3 ? $local_part : 'member';
	$username   = $base;

	for ( $suffix = 2; username_exists( $username ) && $suffix < 1000; $suffix++ ) {
		$username = $base . $suffix;
	}

	if ( username_exists( $username ) ) {
		$username = 'member' . wp_rand( 100000, 999999 );
	}

	return $username;
}

function cbcommerce_ajax_register() {
	check_ajax_referer( 'cbcommerce_member', 'nonce' );

	if ( is_user_logged_in() && ! cbcommerce_user_can_access_admin() ) {
		wp_send_json_error( array( 'message' => __( 'You are already signed in.', 'codesblock-commerce' ) ), 409 );
	}

	if ( ! empty( $_POST['company'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Unable to complete registration.', 'codesblock-commerce' ) ), 400 );
	}

	$email          = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$username_input = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
	$username       = sanitize_user( $username_input, true );
	$first_name     = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$password       = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
	$interests      = cbcommerce_sanitize_interests( isset( $_POST['interests'] ) ? $_POST['interests'] : array() );
	$consent        = isset( $_POST['marketing_consent'] ) && '1' === (string) $_POST['marketing_consent'];

	$register_ip_limited      = cbcommerce_rate_limit_reached( 'register-ip', '', 10, HOUR_IN_SECONDS );
	$register_account_limited = $email ? cbcommerce_rate_limit_reached( 'register-account', $email, 5, HOUR_IN_SECONDS ) : false;
	if ( $register_ip_limited || $register_account_limited ) {
		wp_send_json_error( array( 'message' => __( 'Too many attempts. Please wait before trying again.', 'codesblock-commerce' ) ), 429 );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Enter a valid email address.', 'codesblock-commerce' ) ), 422 );
	}
	if ( $username_input && ( $username !== $username_input || strlen( $username ) < 3 || ! validate_username( $username ) ) ) {
		wp_send_json_error( array( 'message' => __( 'Choose a username with at least 3 letters or numbers.', 'codesblock-commerce' ) ), 422 );
	}
	if ( strlen( $password ) < 10 ) {
		wp_send_json_error( array( 'message' => __( 'Use at least 10 characters for your password.', 'codesblock-commerce' ) ), 422 );
	}
	if ( email_exists( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'We could not create an account with those details. Try signing in or continue with Google.', 'codesblock-commerce' ) ), 409 );
	}
	if ( ! $username ) {
		$username = cbcommerce_username_from_email( $email );
	}
	if ( username_exists( $username ) ) {
		wp_send_json_error( array( 'message' => __( 'We could not create an account with those details. Try signing in or continue with Google.', 'codesblock-commerce' ) ), 409 );
	}

	$user_id = wp_insert_user(
		array(
			'user_login'   => $username,
			'user_email'   => $email,
			'user_pass'    => $password,
			'first_name'   => mb_substr( $first_name, 0, 50 ),
			'display_name' => $first_name ? mb_substr( $first_name, 0, 50 ) : $username,
			'role'         => 'subscriber',
		)
	);

	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => __( 'We could not create your account. Please try again.', 'codesblock-commerce' ) ), 500 );
	}

	update_user_meta( $user_id, 'codesblock_learning_interests', $interests );
	update_user_meta( $user_id, 'codesblock_signup_source', 'member-modal' );
	update_user_meta( $user_id, 'codesblock_marketing_consent', $consent ? 'yes' : 'no' );
	if ( $consent ) {
		update_user_meta( $user_id, 'codesblock_marketing_consent_at', gmdate( 'c' ) );
		cbcommerce_subscribe_mailpoet( $email, $first_name, $interests, 'Member modal' );
	}

	$starter_id = cbcommerce_level_id( 'starter' );
	if ( $starter_id && function_exists( 'pmpro_changeMembershipLevel' ) ) {
		pmpro_changeMembershipLevel( $starter_id, $user_id );
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true, is_ssl() );

	$redirect = cbcommerce_member_home_url();
	wp_send_json_success(
		array(
			'message'  => __( 'Your account is ready. Welcome to CodesBlock!', 'codesblock-commerce' ),
			'redirect' => esc_url_raw( $redirect ),
		)
	);
}
add_action( 'wp_ajax_nopriv_cbcommerce_register', 'cbcommerce_ajax_register' );
add_action( 'wp_ajax_cbcommerce_register', 'cbcommerce_ajax_register' );

function cbcommerce_ajax_login() {
	check_ajax_referer( 'cbcommerce_member', 'nonce' );

	if ( is_user_logged_in() && ! cbcommerce_user_can_access_admin() ) {
		wp_send_json_error( array( 'message' => __( 'You are already signed in.', 'codesblock-commerce' ) ), 409 );
	}

	$identity = isset( $_POST['identity'] ) ? sanitize_text_field( wp_unslash( $_POST['identity'] ) ) : '';
	$password = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
	$remember = ! empty( $_POST['remember'] );

	$ip_limited      = cbcommerce_rate_limit_reached( 'login-ip', '', 30, 15 * MINUTE_IN_SECONDS );
	$account_limited = cbcommerce_rate_limit_reached( 'login-account', strtolower( $identity ), 10, 15 * MINUTE_IN_SECONDS );
	if ( $ip_limited || $account_limited ) {
		wp_send_json_error( array( 'message' => __( 'Too many attempts. Please wait 15 minutes and try again.', 'codesblock-commerce' ) ), 429 );
	}

	$user = wp_signon(
		array(
			'user_login'    => $identity,
			'user_password' => $password,
			'remember'      => $remember,
		),
		is_ssl()
	);

	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array( 'message' => __( 'The email or password is incorrect.', 'codesblock-commerce' ) ), 401 );
	}

	$redirect = cbcommerce_member_home_url();
	wp_send_json_success( array( 'message' => __( 'Signed in successfully.', 'codesblock-commerce' ), 'redirect' => $redirect ) );
}
add_action( 'wp_ajax_nopriv_cbcommerce_login', 'cbcommerce_ajax_login' );
add_action( 'wp_ajax_cbcommerce_login', 'cbcommerce_ajax_login' );

function cbcommerce_ajax_newsletter() {
	check_ajax_referer( 'cbcommerce_member', 'nonce' );

	if ( ! empty( $_POST['company'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Unable to save this subscription.', 'codesblock-commerce' ) ), 400 );
	}

	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$newsletter_ip_limited      = cbcommerce_rate_limit_reached( 'newsletter-ip', '', 10, HOUR_IN_SECONDS );
	$newsletter_account_limited = $email ? cbcommerce_rate_limit_reached( 'newsletter-account', $email, 3, DAY_IN_SECONDS ) : false;
	if ( $newsletter_ip_limited || $newsletter_account_limited ) {
		wp_send_json_error( array( 'message' => __( 'Too many attempts. Please try again later.', 'codesblock-commerce' ) ), 429 );
	}
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Enter a valid email address.', 'codesblock-commerce' ) ), 422 );
	}

	$result = cbcommerce_subscribe_mailpoet( $email, $first_name, array(), 'Newsletter form' );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ), 500 );
	}

	wp_send_json_success( array( 'message' => __( 'Check your inbox to confirm your subscription.', 'codesblock-commerce' ) ) );
}
add_action( 'wp_ajax_nopriv_cbcommerce_newsletter', 'cbcommerce_ajax_newsletter' );
add_action( 'wp_ajax_cbcommerce_newsletter', 'cbcommerce_ajax_newsletter' );

/**
 * Mark new social registrations so profile fields remain attributable.
 */
function cbcommerce_social_registration_meta( $user_id, $provider_id, $is_register ) {
	if ( $is_register ) {
		update_user_meta( $user_id, 'codesblock_signup_source', 'social-' . sanitize_key( $provider_id ) );
		$starter_id = cbcommerce_level_id( 'starter' );
		if ( $starter_id && function_exists( 'pmpro_changeMembershipLevel' ) ) {
			pmpro_changeMembershipLevel( $starter_id, $user_id );
		}

		if ( 'yes' === get_user_meta( $user_id, 'codesblock_marketing_consent', true ) ) {
			$user      = get_userdata( $user_id );
			$interests = (array) get_user_meta( $user_id, 'codesblock_learning_interests', true );
			if ( $user ) {
				cbcommerce_subscribe_mailpoet( $user->user_email, $user->first_name, $interests, 'Social registration' );
			}
		}
	}
}
foreach ( array( 'google', 'facebook', 'github', 'apple' ) as $cbcommerce_provider ) {
	add_action( 'nsl_' . $cbcommerce_provider . '_link_user', 'cbcommerce_social_registration_meta', 10, 3 );
}

function cbcommerce_social_flow_page( $page_id ) {
	$custom_page_id = absint( get_option( 'cbcommerce_social_flow_page_id', 0 ) );
	return $custom_page_id ? $custom_page_id : $page_id;
}
add_filter( 'nsl_register_flow_page', 'cbcommerce_social_flow_page' );

function cbcommerce_nsl_redirect_frontend() {
	return cbcommerce_member_home_url();
}
add_filter( 'nsl_login_redirect_url', 'cbcommerce_nsl_redirect_frontend', 100 );
add_filter( 'nsl_register_redirect_url', 'cbcommerce_nsl_redirect_frontend', 100 );

function cbcommerce_nsl_error_redirect_frontend() {
	return cbcommerce_member_home_url() . '#signin';
}
add_filter( 'nsl_disabled_login_redirect_url', 'cbcommerce_nsl_error_redirect_frontend', 100 );
add_filter( 'nsl_disabled_register_redirect_url', 'cbcommerce_nsl_error_redirect_frontend', 100 );
add_filter( 'nsl_autolink_error_redirect_url', 'cbcommerce_nsl_error_redirect_frontend', 100 );


function cbcommerce_social_require_profile( $required ) {
	return true;
}
add_filter( 'nsl_registration_require_extra_input', 'cbcommerce_social_require_profile' );

function cbcommerce_social_profile_form() {
	?>
	<div class="cb-social-complete">
		<p class="cb-social-kicker"><?php esc_html_e( 'One last step', 'codesblock-commerce' ); ?></p>
		<h2><?php esc_html_e( 'Personalize your CodesBlock account', 'codesblock-commerce' ); ?></h2>
		<p><?php esc_html_e( 'Choose what you are working toward so recommendations stay relevant.', 'codesblock-commerce' ); ?></p>
		<fieldset>
			<legend><?php esc_html_e( 'Learning interests', 'codesblock-commerce' ); ?></legend>
			<?php foreach ( cbcommerce_allowed_interests() as $key => $label ) : ?>
				<label><input type="checkbox" name="cbcommerce_social_interests[]" value="<?php echo esc_attr( $key ); ?>"> <span><?php echo esc_html( $label ); ?></span></label>
			<?php endforeach; ?>
		</fieldset>
		<label class="cb-social-consent"><input type="checkbox" name="cbcommerce_social_marketing" value="1"> <span><?php esc_html_e( 'Send me the weekly newsletter and tailored course updates. I can unsubscribe anytime.', 'codesblock-commerce' ); ?></span></label>
		<?php wp_nonce_field( 'cbcommerce_social_profile', 'cbcommerce_social_profile_nonce' ); ?>
	</div>
	<?php
}
add_action( 'nsl_registration_form_end', 'cbcommerce_social_profile_form' );

function cbcommerce_validate_social_profile( $user_data, $errors ) {
	if ( ! isset( $_POST['submit'] ) ) {
		return $user_data;
	}

	if ( ! isset( $_POST['cbcommerce_social_profile_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cbcommerce_social_profile_nonce'] ) ), 'cbcommerce_social_profile' ) ) {
		$errors->add( 'cbcommerce_social_nonce', __( 'Your session expired. Please try social sign in again.', 'codesblock-commerce' ) );
		return $user_data;
	}

	$interests = cbcommerce_sanitize_interests( isset( $_POST['cbcommerce_social_interests'] ) ? $_POST['cbcommerce_social_interests'] : array() );
	$consent   = ! empty( $_POST['cbcommerce_social_marketing'] );
	$meta      = isset( $user_data['meta_input'] ) && is_array( $user_data['meta_input'] ) ? $user_data['meta_input'] : array();
	$meta['codesblock_learning_interests']    = $interests;
	$meta['codesblock_marketing_consent']     = $consent ? 'yes' : 'no';
	$meta['codesblock_marketing_consent_at']  = $consent ? gmdate( 'c' ) : '';
	$user_data['meta_input']                   = $meta;
	return $user_data;
}
add_filter( 'nsl_registration_validate_extra_input', 'cbcommerce_validate_social_profile', 10, 2 );

function cbcommerce_noindex_social_flow( $robots ) {
	if ( is_page( absint( get_option( 'cbcommerce_social_flow_page_id', 0 ) ) ) ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'cbcommerce_noindex_social_flow' );

function cbcommerce_growth_profile_fields( $user ) {
	if ( ! current_user_can( 'edit_user', $user->ID ) ) {
		return;
	}

	$selected = (array) get_user_meta( $user->ID, 'codesblock_learning_interests', true );
	$consent  = 'yes' === get_user_meta( $user->ID, 'codesblock_marketing_consent', true );
	?>
	<h2><?php esc_html_e( 'CodesBlock communication preferences', 'codesblock-commerce' ); ?></h2>
	<?php wp_nonce_field( 'cbcommerce_growth_profile_' . $user->ID, 'cbcommerce_growth_profile_nonce' ); ?>
	<table class="form-table" role="presentation">
		<tr>
			<th><?php esc_html_e( 'Learning interests', 'codesblock-commerce' ); ?></th>
			<td>
				<?php foreach ( cbcommerce_allowed_interests() as $key => $label ) : ?>
					<label style="display:block;margin-bottom:6px;"><input type="checkbox" name="cbcommerce_interests[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $selected, true ) ); ?>> <?php echo esc_html( $label ); ?></label>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Email marketing', 'codesblock-commerce' ); ?></th>
			<td><label><input type="checkbox" name="cbcommerce_marketing_consent" value="1" <?php checked( $consent ); ?>> <?php esc_html_e( 'Weekly newsletter and tailored course updates', 'codesblock-commerce' ); ?></label></td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'cbcommerce_growth_profile_fields' );
add_action( 'edit_user_profile', 'cbcommerce_growth_profile_fields' );

function cbcommerce_save_growth_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['cbcommerce_growth_profile_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cbcommerce_growth_profile_nonce'] ) ), 'cbcommerce_growth_profile_' . $user_id ) ) {
		return;
	}

	$interests = cbcommerce_sanitize_interests( isset( $_POST['cbcommerce_interests'] ) ? $_POST['cbcommerce_interests'] : array() );
	$consent   = ! empty( $_POST['cbcommerce_marketing_consent'] );
	update_user_meta( $user_id, 'codesblock_learning_interests', $interests );
	update_user_meta( $user_id, 'codesblock_marketing_consent', $consent ? 'yes' : 'no' );
	if ( $consent ) {
		if ( ! get_user_meta( $user_id, 'codesblock_marketing_consent_at', true ) ) {
			update_user_meta( $user_id, 'codesblock_marketing_consent_at', gmdate( 'c' ) );
		}
		$user = get_userdata( $user_id );
		if ( $user ) {
			cbcommerce_subscribe_mailpoet( $user->user_email, $user->first_name, $interests, 'User profile' );
		}
	}
}
add_action( 'personal_options_update', 'cbcommerce_save_growth_profile_fields' );
add_action( 'edit_user_profile_update', 'cbcommerce_save_growth_profile_fields' );

function cbcommerce_security_headers() {
	if ( is_admin() || headers_sent() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'X-Frame-Options: SAMEORIGIN' );
}
add_action( 'send_headers', 'cbcommerce_security_headers' );

function cbcommerce_social_login_shortcode() {
	if ( is_user_logged_in() ) {
		return '';
	}

	ob_start();
	?>
	<section class="cb-social-login" aria-labelledby="cb-social-title">
		<p class="cb-social-kicker"><?php esc_html_e( 'Fast, secure sign in', 'codesblock-commerce' ); ?></p>
		<h2 id="cb-social-title"><?php esc_html_e( 'Continue with an account you trust', 'codesblock-commerce' ); ?></h2>
		<p><?php esc_html_e( 'We only request the basic profile details needed to create your learning account.', 'codesblock-commerce' ); ?></p>
		<div class="cb-social-providers">
			<?php
			$redirect_url = cbcommerce_member_home_url();
			$provider_buttons = shortcode_exists( 'nextend_social_login' ) ? do_shortcode( '[nextend_social_login redirect="' . esc_url( $redirect_url ) . '"]' ) : '';
			if ( false !== strpos( $provider_buttons, 'nsl-button' ) ) {
				echo $provider_buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted plugin shortcode output.
			} else {
				$login_url = function_exists( 'pmpro_url' ) ? pmpro_url( 'login' ) : wp_login_url();
				printf( '<a class="cb-signin-fallback" href="%1$s">%2$s</a>', esc_url( $login_url ), esc_html__( 'Continue with email', 'codesblock-commerce' ) );
			}
			?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'codesblock_social_login', 'cbcommerce_social_login_shortcode' );

function cbcommerce_checkout_social_login() {
	if ( ! is_user_logged_in() ) {
		echo do_shortcode( '[codesblock_social_login]' );
	}
}

function cbcommerce_assets() {
	$pmpro_page_ids = array_filter(
		array_map(
			'intval',
			array(
				get_option( 'pmpro_account_page_id' ),
				get_option( 'pmpro_checkout_page_id' ),
				get_option( 'pmpro_levels_page_id' ),
				get_option( 'pmpro_login_page_id' ),
				get_option( 'cbcommerce_social_flow_page_id' ),
			)
		)
	);
	if ( is_page( $pmpro_page_ids ) || is_singular( array( 'course', 'post' ) ) ) {
		wp_enqueue_style( 'codesblock-commerce', plugin_dir_url( __FILE__ ) . 'assets/frontend.css', array(), CBCOMMERCE_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'cbcommerce_assets', 20 );

/**
 * Add a concise, branded orientation layer above PMPro's functional forms.
 */
function cbcommerce_member_page_intro( $content ) {
	if ( is_admin() || ! is_main_query() || ! in_the_loop() ) {
		return $content;
	}

	$checkout_page_id = absint( get_option( 'pmpro_checkout_page_id' ) );
	$levels_page_id   = absint( get_option( 'pmpro_levels_page_id' ) );
	if ( ! is_page( array_filter( array( $checkout_page_id, $levels_page_id ) ) ) ) {
		return $content;
	}

	if ( is_page( $checkout_page_id ) ) {
		ob_start();
		?>
		<section class="cb-checkout-intro" aria-labelledby="cb-checkout-intro-title">
			<div class="cb-checkout-intro-copy">
				<span class="cb-checkout-eyebrow"><?php esc_html_e( 'Secure enrollment', 'codesblock-commerce' ); ?></span>
				<h1 id="cb-checkout-intro-title"><?php esc_html_e( 'Choose your pass. Pay your way.', 'codesblock-commerce' ); ?></h1>
				<p><?php esc_html_e( 'Your price and access period stay visible before payment. India checkout supports UPI, cards, and netbanking once the gateway is connected.', 'codesblock-commerce' ); ?></p>
			</div>
			<div class="cb-checkout-steps" aria-label="<?php esc_attr_e( 'Checkout progress', 'codesblock-commerce' ); ?>">
				<span class="is-active"><b>1</b><?php esc_html_e( 'Account', 'codesblock-commerce' ); ?></span>
				<span><b>2</b><?php esc_html_e( 'Review', 'codesblock-commerce' ); ?></span>
				<span><b>3</b><?php esc_html_e( 'Pay', 'codesblock-commerce' ); ?></span>
			</div>
			<div class="cb-checkout-methods" aria-label="<?php esc_attr_e( 'Accepted payment methods', 'codesblock-commerce' ); ?>">
				<span class="cb-method-upi"><b>UPI</b><small><?php esc_html_e( 'GPay · PhonePe · BHIM', 'codesblock-commerce' ); ?></small></span>
				<span><b><?php esc_html_e( 'Cards', 'codesblock-commerce' ); ?></b><small><?php esc_html_e( 'Visa · Mastercard · RuPay', 'codesblock-commerce' ); ?></small></span>
				<span><b><?php esc_html_e( 'Netbanking', 'codesblock-commerce' ); ?></b><small><?php esc_html_e( 'Major Indian banks', 'codesblock-commerce' ); ?></small></span>
			</div>
			<p class="cb-checkout-security"><span aria-hidden="true">&#10003;</span><?php esc_html_e( 'Payment details are handled by the secure gateway, not stored by CodesBlock.', 'codesblock-commerce' ); ?></p>
		</section>
		<?php
		return ob_get_clean() . $content;
	}

	ob_start();
	?>
	<section class="cb-levels-intro" aria-labelledby="cb-levels-intro-title">
		<span class="cb-checkout-eyebrow"><?php esc_html_e( 'Simple access passes', 'codesblock-commerce' ); ?></span>
		<h1 id="cb-levels-intro-title"><?php esc_html_e( 'Start free, then unlock the full library when it fits.', 'codesblock-commerce' ); ?></h1>
		<p><?php esc_html_e( 'Paid passes use one upfront payment with no surprise auto-renewal. Renew manually only if you want to continue.', 'codesblock-commerce' ); ?></p>
	</section>
	<?php
	return ob_get_clean() . $content;
}
add_filter( 'the_content', 'cbcommerce_member_page_intro', 20 );

/**
 * Return the front-end destination used after member authentication.
 */
function cbcommerce_member_home_url() {
	$learning_page_id = absint( get_option( 'cbcommerce_learning_page_id', 0 ) );
	if ( $learning_page_id && 'publish' === get_post_status( $learning_page_id ) ) {
		return get_permalink( $learning_page_id );
	}

	return home_url( '/my-learning/' );
}

/**
 * Return the front-end member dashboard URL.
 */
function cbcommerce_member_account_url() {
	return cbcommerce_member_home_url() . '#membership';
}

/**
 * Keep membership navigation inside the consolidated learner dashboard.
 */
function cbcommerce_membership_manage_url() {
	return cbcommerce_member_account_url();
}

/**
 * PMPro still needs its generated account page internally, but it is no longer
 * a learner-facing destination. Forward normal visits to My Learning instead.
 */
function cbcommerce_redirect_legacy_member_dashboard() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	$account_page_id = absint( get_option( 'pmpro_account_page_id', 0 ) );
	if ( ! $account_page_id || ! is_page( $account_page_id ) ) {
		return;
	}

	$request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';
	if ( ! in_array( $request_method, array( 'GET', 'HEAD' ), true ) ) {
		return;
	}

	wp_safe_redirect( cbcommerce_member_account_url(), 301, 'CodesBlock Commerce' );
	exit;
}
add_action( 'wp', 'cbcommerce_redirect_legacy_member_dashboard', -1000 );
add_action( 'template_redirect', 'cbcommerce_redirect_legacy_member_dashboard', -1000 );

/**
 * Return the front-end profile editor when PMPro provides one.
 */
function cbcommerce_member_profile_url() {
	return cbcommerce_member_home_url() . '#profile';
}

/**
 * Return the underlying profile-editing URL.
 */
function cbcommerce_profile_edit_url() {
	if ( function_exists( 'pmpro_url' ) ) {
		$profile_url = pmpro_url( 'member_profile_edit' );
		if ( $profile_url ) {
			return $profile_url;
		}
	}

	return cbcommerce_member_home_url() . '#profile';
}

/**
 * Administrators are identified by capability, never by a hard-coded username.
 */
function cbcommerce_user_can_access_admin( $user = null ) {
	$user = $user instanceof WP_User ? $user : wp_get_current_user();
	return $user->exists() && user_can( $user, 'manage_options' );
}

/**
 * Front-end members have a WordPress session but are not administrators.
 */
function cbcommerce_is_frontend_member( $user = null ) {
	$user = $user instanceof WP_User ? $user : wp_get_current_user();
	return $user->exists() && ! cbcommerce_user_can_access_admin( $user );
}

/**
 * Return sanitized course progress records for a member.
 */
function cbcommerce_get_user_course_progress( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	$stored  = (array) get_user_meta( $user_id, 'codesblock_course_progress', true );
	$records = array();

	foreach ( $stored as $course_id => $record ) {
		$course_id = absint( $course_id );
		if ( ! $course_id || 'course' !== get_post_type( $course_id ) ) {
			continue;
		}

		$percent    = is_array( $record ) && isset( $record['percent'] ) ? absint( $record['percent'] ) : absint( $record );
		$updated_at = is_array( $record ) && isset( $record['updated_at'] ) ? absint( $record['updated_at'] ) : 0;
		$records[ $course_id ] = array(
			'percent'    => min( 100, $percent ),
			'updated_at' => $updated_at,
		);
	}

	uasort(
		$records,
		function ( $first, $second ) {
			return $second['updated_at'] <=> $first['updated_at'];
		}
	);

	return $records;
}

function cbcommerce_get_course_progress( $course_id, $user_id = 0 ) {
	$records = cbcommerce_get_user_course_progress( $user_id );
	return isset( $records[ absint( $course_id ) ] ) ? absint( $records[ absint( $course_id ) ]['percent'] ) : 0;
}

function cbcommerce_user_can_track_course( $course_id, $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
	$user    = get_userdata( $user_id );
	if ( ! $user || ! cbcommerce_is_frontend_member( $user ) || 'course' !== get_post_type( $course_id ) || 'publish' !== get_post_status( $course_id ) ) {
		return false;
	}

	$price   = trim( (string) get_post_meta( $course_id, '_course_price', true ) );
	$is_free = '' === $price || 'free' === strtolower( $price );
	return $is_free || cbcommerce_user_has_paid_access( $user_id );
}

function cbcommerce_set_course_progress( $course_id, $percent, $user_id = 0 ) {
	$user_id   = $user_id ? absint( $user_id ) : get_current_user_id();
	$course_id = absint( $course_id );
	$percent   = min( 100, absint( $percent ) );
	$records   = cbcommerce_get_user_course_progress( $user_id );

	if ( 0 === $percent ) {
		unset( $records[ $course_id ] );
	} else {
		$records[ $course_id ] = array(
			'percent'    => $percent,
			'updated_at' => time(),
		);
	}

	update_user_meta( $user_id, 'codesblock_course_progress', $records );
	return $percent;
}

function cbcommerce_ajax_update_course_progress() {
	check_ajax_referer( 'cbcommerce_member', 'nonce' );

	$course_id = isset( $_POST['course_id'] ) ? absint( $_POST['course_id'] ) : 0;
	$percent   = isset( $_POST['progress'] ) ? absint( $_POST['progress'] ) : 0;
	if ( ! cbcommerce_user_can_track_course( $course_id ) ) {
		wp_send_json_error( array( 'message' => __( 'This course cannot be updated from your account.', 'codesblock-commerce' ) ), 403 );
	}

	$percent = cbcommerce_set_course_progress( $course_id, $percent );
	wp_send_json_success(
		array(
			'message' => __( 'Learning progress saved.', 'codesblock-commerce' ),
			'percent' => $percent,
			'status'  => 100 === $percent ? __( 'Completed', 'codesblock-commerce' ) : ( $percent ? __( 'In progress', 'codesblock-commerce' ) : __( 'Not started', 'codesblock-commerce' ) ),
		)
	);
}
add_action( 'wp_ajax_cbcommerce_update_course_progress', 'cbcommerce_ajax_update_course_progress' );

/**
 * Record the first started state in the existing progress store. This does not
 * create a second enrollment table and never overwrites saved progress.
 */
function cbcommerce_ajax_start_course() {
	check_ajax_referer( 'cbcommerce_member', 'nonce' );

	$course_id = isset( $_POST['course_id'] ) ? absint( $_POST['course_id'] ) : 0;
	if ( ! cbcommerce_user_can_track_course( $course_id ) ) {
		wp_send_json_error( array( 'message' => __( 'This course is not available from your account.', 'codesblock-commerce' ) ), 403 );
	}

	$progress = cbcommerce_get_course_progress( $course_id );
	if ( 0 === $progress ) {
		$progress = cbcommerce_set_course_progress( $course_id, 1 );
	}

	wp_send_json_success( array( 'message' => __( 'Course started. Your learning progress is saved.', 'codesblock-commerce' ), 'percent' => $progress ) );
}
add_action( 'wp_ajax_cbcommerce_start_course', 'cbcommerce_ajax_start_course' );

/**
 * Return the unfiltered WordPress administrator login URL.
 */
function cbcommerce_admin_login_url( $redirect_to = '' ) {
	$url = site_url( 'wp-login.php', 'login' );
	if ( $redirect_to ) {
		$url = add_query_arg( 'redirect_to', $redirect_to, $url );
	}

	return $url;
}

/**
 * Detect redirects into WordPress administration.
 */
function cbcommerce_is_admin_destination( $redirect_to ) {
	if ( ! $redirect_to ) {
		return false;
	}

	$redirect_path = (string) wp_parse_url( $redirect_to, PHP_URL_PATH );
	$admin_path    = (string) wp_parse_url( admin_url(), PHP_URL_PATH );
	if ( ! $redirect_path || ! $admin_path ) {
		return false;
	}

	return 0 === strpos( trailingslashit( $redirect_path ), trailingslashit( $admin_path ) );
}

/**
 * PMPro owns normal member login URLs, but WordPress administration must use
 * the native WordPress login. This also covers the admin session-expiry check,
 * which calls wp_login_url() without a redirect target before opening its
 * re-authentication iframe.
 */
function cbcommerce_preserve_admin_login_url( $login_url, $redirect_to, $force_reauth ) {
	$is_admin_request = is_admin()
		&& ! wp_doing_ajax()
		&& ! wp_doing_cron()
		&& ! ( defined( 'REST_REQUEST' ) && REST_REQUEST );

	if ( ! $is_admin_request && ! cbcommerce_is_admin_destination( $redirect_to ) ) {
		return $login_url;
	}

	$login_url = cbcommerce_admin_login_url( $redirect_to );
	if ( $force_reauth ) {
		$login_url = add_query_arg( 'reauth', '1', $login_url );
	}

	return $login_url;
}
add_filter( 'login_url', 'cbcommerce_preserve_admin_login_url', PHP_INT_MAX, 3 );

/**
 * Identify a normal WordPress administrator login request.
 *
 * PMPro marks its front-end login form, and Nextend marks social callbacks.
 * Those customer flows still process through wp-login.php internally and must
 * not be mistaken for an administrator login attempt.
 */
function cbcommerce_is_admin_login_request() {
	global $pagenow;

	if ( 'wp-login.php' !== $pagenow || ! empty( $_REQUEST['pmpro_login_form_used'] ) ) {
		return false;
	}

	foreach ( array( 'loginSocial', 'loginFacebook', 'loginGoogle', 'loginTwitter' ) as $social_key ) {
		if ( ! empty( $_REQUEST[ $social_key ] ) ) {
			return false;
		}
	}

	if ( isset( $_GET['interim_login'] ) && 'nsl' === sanitize_key( wp_unslash( $_GET['interim_login'] ) ) ) {
		return false;
	}

	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : 'login';
	return 'login' === $action;
}

/**
 * Reject customer credentials submitted through the private admin login form.
 */
function cbcommerce_limit_admin_authentication( $user, $username, $password ) {
	if ( ! cbcommerce_is_admin_login_request() || is_wp_error( $user ) || ! $user instanceof WP_User ) {
		return $user;
	}

	if ( cbcommerce_user_can_access_admin( $user ) ) {
		return $user;
	}

	return new WP_Error(
		'cbcommerce_admin_login_denied',
		esc_html__( 'This sign-in is not available for this account.', 'codesblock-commerce' )
	);
}
add_filter( 'authenticate', 'cbcommerce_limit_admin_authentication', 100, 3 );

/**
 * Keep the WordPress administrator login form admin-only and password-only.
 * This does not change social login buttons rendered on CodesBlock pages.
 */
function cbcommerce_secure_admin_login_screen() {
	if ( ! cbcommerce_is_admin_login_request() ) {
		return;
	}

	if ( is_user_logged_in() && cbcommerce_is_frontend_member() ) {
		wp_clear_auth_cookie();
		wp_set_current_user( 0 );
	}

	remove_action( 'login_form', 'NextendSocialLogin::addLoginFormButtons' );
	remove_filter( 'login_form_bottom', 'NextendSocialLogin::filterAddEmbeddedLoginFormButtons' );
	remove_filter( 'login_url', 'pmpro_login_url_filter', 50 );
	remove_action( 'wp_login_failed', 'pmpro_login_failed', 10 );

	if ( class_exists( 'NextendSocialLogin' ) && is_callable( array( 'NextendSocialLogin', 'removeLoginFormAssets' ) ) ) {
		NextendSocialLogin::removeLoginFormAssets();
	}
}
add_action( 'login_init', 'cbcommerce_secure_admin_login_screen', 1 );

function cbcommerce_member_flow_redirect( $requested_redirect_to ) {
	$requested_redirect_to = wp_validate_redirect( $requested_redirect_to, '' );
	if ( ! $requested_redirect_to ) {
		return '';
	}

	$requested_args = wp_parse_args( (string) wp_parse_url( $requested_redirect_to, PHP_URL_QUERY ) );
	$course_id      = absint( $requested_args['cb_course_start'] ?? 0 );
	if ( $course_id && 'course' === get_post_type( $course_id ) && 'publish' === get_post_status( $course_id ) ) {
		return add_query_arg( 'cb_course_start', $course_id, get_permalink( $course_id ) );
	}

	$checkout_path = (string) wp_parse_url( function_exists( 'pmpro_url' ) ? pmpro_url( 'checkout' ) : '', PHP_URL_PATH );
	$requested_path = (string) wp_parse_url( $requested_redirect_to, PHP_URL_PATH );
	$paid_levels    = array_filter( array( cbcommerce_level_id( 'pro' ), cbcommerce_level_id( 'pro_annual' ), cbcommerce_level_id( 'lifetime' ) ) );
	if ( $checkout_path && untrailingslashit( $requested_path ) === untrailingslashit( $checkout_path ) && in_array( absint( $requested_args['level'] ?? 0 ), $paid_levels, true ) ) {
		return $requested_redirect_to;
	}

	return '';
}

function cbcommerce_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
	if ( $user instanceof WP_User && ! cbcommerce_user_can_access_admin( $user ) ) {
		$course_flow_redirect = cbcommerce_member_flow_redirect( $requested_redirect_to );
		return $course_flow_redirect ? $course_flow_redirect : cbcommerce_member_home_url();
	}
	return $redirect_to;
}
add_filter( 'login_redirect', 'cbcommerce_login_redirect', 100, 3 );

/**
 * Keep administrator logout inside the private administrator authentication
 * flow. Member logout continues to use the public PMPro destination.
 */
function cbcommerce_logout_redirect( $redirect_to, $requested_redirect_to, $user ) {
	return home_url( '/' );
}
add_filter( 'logout_redirect', 'cbcommerce_logout_redirect', 100, 3 );

/**
 * A member entering /wp-admin is signed out of the member session and shown
 * the normal WordPress administrator login. WordPress request endpoints remain
 * available for front-end forms and integrations.
 */
function cbcommerce_restrict_member_admin() {
	if ( ! is_user_logged_in() || cbcommerce_user_can_access_admin() ) {
		return;
	}

	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	global $pagenow;
	if ( in_array( $pagenow, array( 'admin-post.php', 'async-upload.php' ), true ) ) {
		return;
	}

	wp_clear_auth_cookie();
	wp_set_current_user( 0 );
	wp_safe_redirect( cbcommerce_admin_login_url( admin_url() ) );
	exit;
}
add_action( 'admin_init', 'cbcommerce_restrict_member_admin', 1 );

function cbcommerce_hide_member_admin_bar( $show ) {
	if ( is_user_logged_in() && ! cbcommerce_user_can_access_admin() ) {
		return false;
	}

	return $show;
}
add_filter( 'show_admin_bar', 'cbcommerce_hide_member_admin_bar', 100 );

function cbcommerce_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'cbcommerce_external_setup_complete' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->base, array( 'dashboard', 'plugins', 'toplevel_page_pmpro-dashboard' ), true ) ) {
		return;
	}
	$mailpoet_ready = class_exists( 'MailPoet\\API\\API' ) && cbcommerce_mailpoet_list_id();
	$google_ready   = class_exists( 'NextendSocialLogin' ) && NextendSocialLogin::isProviderEnabled( 'google' );
	$pmpro_ready    = function_exists( 'pmpro_url' ) && cbcommerce_level_id( 'starter' ) && cbcommerce_level_id( 'pro' );
	$payments_ready = cbcommerce_payments_ready();
	$india_bridge   = defined( 'KNITPAY_VERSION' );
	$notice_class   = $mailpoet_ready && $google_ready && $pmpro_ready && $payments_ready ? 'notice-success' : 'notice-warning';
	?>
	<div class="notice <?php echo esc_attr( $notice_class ); ?>">
		<p><strong><?php esc_html_e( 'CodesBlock integration status:', 'codesblock-commerce' ); ?></strong></p>
		<ul style="list-style:disc;margin-left:20px;">
			<li><?php echo esc_html( $mailpoet_ready ? __( 'MailPoet: newsletter list connected with confirmation email enabled.', 'codesblock-commerce' ) : __( 'MailPoet: choose or create a newsletter list.', 'codesblock-commerce' ) ); ?></li>
			<li><?php echo esc_html( $pmpro_ready ? __( 'PMPro: member pages and Starter/Pro plans connected.', 'codesblock-commerce' ) : __( 'PMPro: membership pages or plans still need setup.', 'codesblock-commerce' ) ); ?></li>
			<li><?php echo esc_html( $google_ready ? __( 'Nextend: Google login is enabled in the public member UI.', 'codesblock-commerce' ) : __( 'Nextend: Google login still needs to be tested and enabled.', 'codesblock-commerce' ) ); ?></li>
			<li><?php echo esc_html( $india_bridge ? __( 'India payments: Knit Pay is active and ready for a Razorpay configuration.', 'codesblock-commerce' ) : __( 'India payments: install the Knit Pay bridge before configuring Razorpay.', 'codesblock-commerce' ) ); ?></li>
			<li><?php echo esc_html( $payments_ready ? __( 'Payments: a configured gateway is selected. Complete UPI/card sandbox tests before launch.', 'codesblock-commerce' ) : __( 'Payments: add the Razorpay configuration and select Knit Pay in PMPro; paid calls to action stay disabled until then.', 'codesblock-commerce' ) ); ?></li>
		</ul>
	</div>
	<?php
}
add_action( 'admin_notices', 'cbcommerce_admin_notice' );
