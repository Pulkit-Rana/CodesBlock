<?php
/**
 * Template Name: Crack the System Design Interview Landing
 * Template Post Type: course, page
 *
 * @package CodesBlock
 */

get_header();

$cb_post_id         = get_the_ID();
$cb_price           = get_post_meta( $cb_post_id, '_course_price', true );
$cb_is_free         = ( 'free' === strtolower( (string) $cb_price ) || '' === trim( (string) $cb_price ) );
$cb_lessons          = function_exists( 'cbcore_get_course_lessons' ) ? cbcore_get_course_lessons( $cb_post_id ) : array();
$cb_published_lessons = count( $cb_lessons );
$cb_outline          = function_exists( 'codesblock_get_course_outline' ) ? codesblock_get_course_outline( $cb_post_id ) : array();
$cb_roadmap_steps    = 0;

foreach ( $cb_outline as $cb_outline_module ) {
	$cb_roadmap_steps += isset( $cb_outline_module['lessons'] ) && is_array( $cb_outline_module['lessons'] ) ? count( $cb_outline_module['lessons'] ) : 0;
}

$cb_user_has_access = function_exists( 'codesblock_user_can_view_protected_content' )
	? codesblock_user_can_view_protected_content( $cb_post_id )
	: $cb_is_free;

$cb_first_lesson    = function_exists( 'cbcore_get_first_course_lesson' ) ? cbcore_get_first_course_lesson( $cb_post_id ) : null;
$cb_start_href      = ( $cb_first_lesson && $cb_user_has_access ) ? get_permalink( $cb_first_lesson ) : '#start';
$cb_billing_url     = function_exists( 'cbcommerce_checkout_url' ) ? cbcommerce_checkout_url( 'pro' ) : '#start';

$cb_render_course_cta = static function( $classes = '' ) use ( $cb_first_lesson, $cb_user_has_access, $cb_is_free ) {
	$classes = trim( 'btn primary ' . $classes );
	if ( is_user_logged_in() && $cb_user_has_access && $cb_first_lesson ) {
		echo '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( get_permalink( $cb_first_lesson ) ) . '"><span>' . esc_html__( 'Continue course', 'codesblock' ) . '</span><span aria-hidden="true">&rarr;</span></a>';
		return;
	}

	if ( is_user_logged_in() && ! $cb_is_free ) {
		echo '<button class="' . esc_attr( $classes ) . ' js-open-paywall" type="button" aria-haspopup="dialog" aria-controls="paywall-overlay"><span>' . esc_html__( 'View access passes', 'codesblock' ) . '</span><span aria-hidden="true">&rarr;</span></button>';
		return;
	}

	echo '<button class="' . esc_attr( $classes ) . ' js-open-member" type="button" data-member-view="register" aria-haspopup="dialog" aria-controls="member-overlay"><span>' . esc_html__( 'Create free account', 'codesblock' ) . '</span><span aria-hidden="true">&rarr;</span></button>';
};
?>

<style>
/* Scoped to landing main to preserve CodesBlock header and footer */
:root {
  --cb-bg: #f8fafc;
  --cb-surface: #ffffff;
  --cb-ink: #0f172a;
  --cb-ink-secondary: #334155;
  --cb-muted: #64748b;
  --cb-line: #e2e8f0;
  --cb-line-subtle: #f1f5f9;
  --cb-blue: #2563eb;
  --cb-blue-hover: #1d4ed8;
  --cb-navy: #0b1120;
  --cb-navy-card: #151f32;
  --cb-navy-card-border: #22324d;
  --cb-soft-blue: #eff6ff;
  --cb-soft-blue-border: #bfdbfe;
  --cb-yellow: #f59e0b;
  --cb-yellow-light: #fef3c7;
  --cb-green: #10b981;
  --cb-green-light: #ecfdf5;
  --cb-green-dark: #047857;
  --cb-purple: #8b5cf6;
  --cb-purple-light: #f5f3ff;
  --cb-cyan: #06b6d4;
  --cb-cyan-light: #ecfeff;
  --cb-rose: #f43f5e;
  --cb-rose-light: #fff1f2;
  --cb-max: 1200px;
  --cb-radius: 18px;
  --cb-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.08), 0 2px 8px -2px rgba(15, 23, 42, 0.04);
  --cb-shadow-lg: 0 25px 65px -12px rgba(37, 99, 235, 0.18);
}

#cb-landing-main {
  background: var(--cb-bg);
  color: var(--cb-ink);
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  line-height: 1.58;
  -webkit-font-smoothing: antialiased;
  overflow-x: hidden;
  position: relative;
}

#cb-landing-main * {
  box-sizing: border-box;
}

#cb-landing-main a {
  text-decoration: none;
  color: inherit;
  transition: color 0.2s ease, background 0.2s ease, border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

#cb-landing-main button {
  font: inherit;
}

#cb-landing-main .wrap {
  width: min(var(--cb-max), calc(100% - 44px));
  margin-inline: auto;
}

#cb-landing-main .tiny {
  font-size: 12px;
  color: var(--cb-muted);
}

/* Promo Banner Ribbon */
#cb-landing-main .promo-ribbon {
  background: linear-gradient(90deg, #0b1120 0%, #1e293b 50%, #0f172a 100%);
  color: #ffffff;
  padding: 10px 20px;
  font-size: 12.5px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 14px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  position: relative;
  z-index: 10;
}
#cb-landing-main .promo-ribbon-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(245, 158, 11, 0.2);
  color: #fbbf24;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 99px;
  font-size: 11px;
  border: 1px solid rgba(245, 158, 11, 0.4);
  letter-spacing: 0.02em;
}
#cb-landing-main .promo-code-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: rgba(255, 255, 255, 0.12);
  border: 1px dashed rgba(251, 191, 36, 0.6);
  padding: 2px 8px;
  border-radius: 6px;
  color: #fef08a;
  font-weight: 800;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 12px;
  cursor: pointer;
}
#cb-landing-main .promo-code-pill:hover {
  background: rgba(255, 255, 255, 0.2);
}

/* Buttons */
#cb-landing-main .btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  border: 1px solid var(--cb-line);
  border-radius: 12px;
  background: #ffffff;
  color: var(--cb-ink);
  font-size: 13.5px;
  font-weight: 750;
  cursor: pointer;
  text-decoration: none;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
}
#cb-landing-main .btn:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
}
#cb-landing-main .primary {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  border-color: #2563eb;
  color: #ffffff;
  box-shadow: 0 8px 24px -4px rgba(37, 99, 235, 0.45);
}
#cb-landing-main .primary:hover {
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
  color: #ffffff;
  box-shadow: 0 12px 30px -4px rgba(37, 99, 235, 0.55);
}
#cb-landing-main .btn-lg {
  padding: 15px 26px;
  font-size: 14.5px;
  border-radius: 13px;
}
#cb-landing-main .darkbtn {
  background: #ffffff;
  color: #0f172a;
  border-color: #ffffff;
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.25);
}
#cb-landing-main .darkbtn:hover {
  background: #f1f5f9;
  color: #020617;
  transform: translateY(-2px);
  box-shadow: 0 14px 34px rgba(0, 0, 0, 0.35);
}
#cb-landing-main .ghost-dark {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff;
  border-color: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
}
#cb-landing-main .ghost-dark:hover {
  background: rgba(255, 255, 255, 0.16);
  border-color: rgba(255, 255, 255, 0.35);
  color: #ffffff;
}

/* Eyebrow & Badges */
#cb-landing-main .eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 11.5px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.09em;
  color: #1e40af;
  background: linear-gradient(90deg, rgba(239, 246, 255, 0.95), rgba(245, 243, 255, 0.9));
  border: 1px solid rgba(191, 219, 254, 0.8);
  padding: 6px 13px;
  border-radius: 99px;
  margin: 0;
  box-shadow: 0 2px 6px rgba(37, 99, 235, 0.06);
}
#cb-landing-main .eyebrow-beacon {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #2563eb;
  position: relative;
}
#cb-landing-main .eyebrow-beacon:after {
  content: "";
  position: absolute;
  inset: -3px;
  border-radius: 50%;
  border: 2px solid #3b82f6;
  opacity: 0.7;
  animation: cbBeaconPulse 2s cubic-bezier(0, 0.2, 0.8, 1) infinite;
}
@keyframes cbBeaconPulse {
  0% { transform: scale(0.6); opacity: 0.9; }
  100% { transform: scale(2.2); opacity: 0; }
}

/* Gradient text utility */
#cb-landing-main .gradient-text {
  background: linear-gradient(135deg, #1d4ed8 0%, #6366f1 45%, #0ea5e9 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
#cb-landing-main .ai-gradient-text {
  background: linear-gradient(135deg, #38bdf8 0%, #818cf8 50%, #c084fc 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Hero Section */
#cb-landing-main .hero {
  padding: 68px 0 46px;
  position: relative;
  background-color: #f8fafc;
  background-image:
    radial-gradient(circle at 1px 1px, rgba(15, 23, 42, 0.035) 1px, transparent 0),
    radial-gradient(ellipse 70% 50% at 50% -10%, rgba(37, 99, 235, 0.14), transparent 70%),
    radial-gradient(ellipse 55% 45% at 90% 25%, rgba(245, 158, 11, 0.10), transparent 60%),
    radial-gradient(ellipse 50% 50% at 10% 65%, rgba(139, 92, 246, 0.08), transparent 60%);
  background-size: 28px 28px, 100% 100%, 100% 100%, 100% 100%;
}
#cb-landing-main .hero-grid {
  display: grid;
  grid-template-columns: 1.04fr 0.96fr;
  gap: 52px;
  align-items: center;
}
#cb-landing-main .hero h1 {
  font-size: clamp(44px, 5.8vw, 74px);
  line-height: 1.02;
  letter-spacing: -0.055em;
  margin: 20px 0 20px;
  max-width: 760px;
  font-weight: 850;
  color: var(--cb-ink);
}
#cb-landing-main .hero .lead {
  font-size: 18.5px;
  line-height: 1.6;
  color: #475569;
  max-width: 680px;
  margin: 0 0 20px;
}
#cb-landing-main .hero .lead strong {
  color: #0f172a;
  font-weight: 700;
}
#cb-landing-main .hero .promise {
  font-size: 15px;
  font-weight: 750;
  max-width: 680px;
  border-left: 4px solid var(--cb-yellow);
  background: linear-gradient(90deg, rgba(254, 243, 199, 0.45), transparent);
  padding: 12px 18px;
  border-radius: 0 12px 12px 0;
  margin: 24px 0;
  color: #1e293b;
}
#cb-landing-main .hero-cta {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-top: 28px;
}
#cb-landing-main .micro {
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
  margin-top: 16px;
  font-size: 12.5px;
  color: #64748b;
}
#cb-landing-main .micro span {
  display: flex;
  gap: 7px;
  align-items: center;
  font-weight: 600;
}
#cb-landing-main .tick {
  width: 17px;
  height: 17px;
  border-radius: 50%;
  background: #dcfce7;
  color: #15803d;
  display: grid;
  place-items: center;
  font-size: 11px;
  font-weight: 900;
  border: 1px solid #bbf7d0;
}

/* Hero Stats */
#cb-landing-main .hero-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  margin-top: 36px;
  padding: 18px 0;
  border-top: 1px solid rgba(226, 232, 240, 0.9);
  border-bottom: 1px solid rgba(226, 232, 240, 0.9);
  background: rgba(255, 255, 255, 0.4);
  border-radius: 12px;
}
#cb-landing-main .hero-stats div {
  padding: 8px 16px 8px 12px;
}
#cb-landing-main .hero-stats div + div {
  border-left: 1px solid var(--cb-line);
}
#cb-landing-main .hero-stats b {
  display: block;
  font-size: 18px;
  font-weight: 850;
  color: #0f172a;
  letter-spacing: -0.02em;
}
#cb-landing-main .hero-stats span {
  font-size: 11.5px;
  color: var(--cb-muted);
  line-height: 1.4;
  display: block;
  margin-top: 2px;
}

/* Course Window Preview */
#cb-landing-main .course-window {
  background: #ffffff;
  border: 1px solid rgba(203, 213, 225, 0.8);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 24px 70px -15px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(37, 99, 235, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
#cb-landing-main .course-window:hover {
  box-shadow: 0 30px 80px -15px rgba(37, 99, 235, 0.2), 0 0 0 1px rgba(37, 99, 235, 0.12);
}
#cb-landing-main .windowtop {
  height: 44px;
  border-bottom: 1px solid var(--cb-line);
  display: flex;
  align-items: center;
  padding: 0 16px;
  gap: 7px;
  background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}
#cb-landing-main .windowtop i {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}
#cb-landing-main .windowtop i:nth-child(1) { background: #ff5f56; border: 1px solid #e0443e; }
#cb-landing-main .windowtop i:nth-child(2) { background: #ffbd2e; border: 1px solid #dea123; }
#cb-landing-main .windowtop i:nth-child(3) { background: #27c93f; border: 1px solid #1aab29; }
#cb-landing-main .windowtop small {
  margin-left: 10px;
  color: #64748b;
  font-size: 11.5px;
  font-weight: 600;
}

#cb-landing-main .demo {
  display: grid;
  grid-template-columns: 1.22fr 0.78fr;
  min-height: 480px;
}
#cb-landing-main .lesson {
  padding: 26px;
  background: #ffffff;
}
#cb-landing-main .lesson small {
  font-weight: 800;
  color: #3b82f6;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-size: 9.5px;
  display: block;
}
#cb-landing-main .lesson h3 {
  font-size: 21px;
  line-height: 1.25;
  letter-spacing: -0.02em;
  margin: 10px 0 12px;
  color: var(--cb-ink);
  font-weight: 800;
}
#cb-landing-main .lesson p {
  font-size: 13.5px;
  color: #475569;
  line-height: 1.6;
  margin: 0;
}
#cb-landing-main .highlight {
  background: linear-gradient(transparent 52%, rgba(254, 240, 138, 0.85) 52%);
  padding: 0 3px;
  font-weight: 600;
  color: #0f172a;
}
#cb-landing-main .toolrow {
  display: flex;
  gap: 7px;
  flex-wrap: wrap;
  margin: 16px 0;
}
#cb-landing-main .tool {
  font-size: 10.5px;
  font-weight: 750;
  padding: 7px 11px;
  border: 1px solid var(--cb-line);
  border-radius: 8px;
  background: #ffffff;
  color: #475569;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
#cb-landing-main .tool:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}
#cb-landing-main .tool.on {
  background: #eff6ff;
  border-color: #93c5fd;
  color: #1d4ed8;
  box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12);
}

/* Architecture Flow Preview */
#cb-landing-main .arch {
  margin-top: 18px;
  background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 16px 12px;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.02);
}
#cb-landing-main .flow {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  flex-wrap: wrap;
}
#cb-landing-main .node {
  padding: 7px 10px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  background: #ffffff;
  font-size: 9.5px;
  font-weight: 800;
  color: #1e293b;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
#cb-landing-main .node.ai {
  background: linear-gradient(135deg, #eff6ff 0%, #f5f3ff 100%);
  border-color: #818cf8;
  color: #3730a3;
  position: relative;
  box-shadow: 0 2px 8px rgba(99, 102, 241, 0.16);
}
#cb-landing-main .node.ai:before {
  content: "";
  display: inline-block;
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #6366f1;
  margin-right: 5px;
  animation: cbNodeGlow 1.8s ease-in-out infinite alternate;
}
@keyframes cbNodeGlow {
  0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.6); }
  100% { box-shadow: 0 0 0 5px rgba(99, 102, 241, 0); }
}
#cb-landing-main .arrow {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 700;
}
#cb-landing-main .followup-box {
  margin-top: 14px;
  padding: 10px 12px;
  background: #fffbeb;
  border-left: 3px solid #f59e0b;
  border-radius: 0 8px 8px 0;
  font-size: 11.5px;
  color: #92400e;
  line-height: 1.5;
}

/* Tutor Panel in Preview */
#cb-landing-main .tutor {
  background: linear-gradient(180deg, #0b1120 0%, #111a28 100%);
  color: #ffffff;
  padding: 22px;
  display: flex;
  flex-direction: column;
  border-left: 1px solid #1e293b;
}
#cb-landing-main .tutor-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  font-weight: 800;
}
#cb-landing-main .orb {
  width: 24px;
  height: 24px;
  border-radius: 8px;
  background: linear-gradient(135deg, #38bdf8 0%, #818cf8 50%, #fbbf24 100%);
  display: inline-block;
  vertical-align: middle;
  margin-right: 8px;
  box-shadow: 0 0 12px rgba(129, 140, 248, 0.6);
  animation: cbOrbRotate 6s linear infinite alternate;
}
@keyframes cbOrbRotate {
  0% { transform: scale(1) rotate(0deg); }
  100% { transform: scale(1.08) rotate(15deg); }
}
#cb-landing-main .voice-tag {
  font-size: 9.5px;
  color: #a5f3fc;
  background: rgba(6, 182, 212, 0.15);
  border: 1px solid rgba(6, 182, 212, 0.35);
  padding: 4px 9px;
  border-radius: 99px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-weight: 700;
}
#cb-landing-main .voice-tag:before {
  content: "";
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #22d3ee;
}
#cb-landing-main .chat {
  margin-top: 16px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 12px 14px;
}
#cb-landing-main .chat small {
  color: #94a3b8;
  font-size: 9.5px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 700;
}
#cb-landing-main .chat p {
  font-size: 11.5px;
  color: #e2e8f0;
  margin: 6px 0 0;
  line-height: 1.55;
}

/* Animated Voice Wave */
#cb-landing-main .wave {
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  margin: 16px 0;
}
#cb-landing-main .wave i {
  width: 3.5px;
  background: linear-gradient(180deg, #60a5fa 0%, #38bdf8 100%);
  border-radius: 4px;
  display: inline-block;
  animation: cbWaveMotion 1.4s ease-in-out infinite;
}
#cb-landing-main .wave i:nth-child(1) { height: 10px; animation-delay: 0.1s; }
#cb-landing-main .wave i:nth-child(2) { height: 18px; animation-delay: 0.3s; }
#cb-landing-main .wave i:nth-child(3) { height: 32px; animation-delay: 0.2s; }
#cb-landing-main .wave i:nth-child(4) { height: 20px; animation-delay: 0.45s; }
#cb-landing-main .wave i:nth-child(5) { height: 38px; animation-delay: 0.15s; }
#cb-landing-main .wave i:nth-child(6) { height: 26px; animation-delay: 0.4s; }
#cb-landing-main .wave i:nth-child(7) { height: 14px; animation-delay: 0.25s; }
#cb-landing-main .wave i:nth-child(8) { height: 30px; animation-delay: 0.35s; }

@keyframes cbWaveMotion {
  0%, 100% { transform: scaleY(0.4); opacity: 0.6; }
  50% { transform: scaleY(1.15); opacity: 1; }
}

#cb-landing-main .talk {
  margin-top: auto;
  background: #ffffff;
  color: #0f172a;
  border-radius: 10px;
  padding: 11px;
  text-align: center;
  font-size: 11px;
  font-weight: 850;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
  cursor: pointer;
}
#cb-landing-main .talk:hover {
  background: #f1f5f9;
}
#cb-landing-main .talk-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #ef4444;
  animation: cbDotBlink 1.5s ease infinite;
}
@keyframes cbDotBlink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.3; }
}

/* Floating Anchor Navigation Bar */
#cb-landing-main .anchor {
  padding: 16px 0 0;
}
#cb-landing-main .anchor-inner {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  padding: 8px 12px;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(226, 232, 240, 0.9);
  border-radius: 14px;
  box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
}
#cb-landing-main .anchor a {
  font-size: 12px;
  font-weight: 700;
  background: #ffffff;
  border: 1px solid var(--cb-line);
  border-radius: 9px;
  padding: 7px 13px;
  color: #475569;
}
#cb-landing-main .anchor a:hover {
  color: #1d4ed8;
  border-color: #93c5fd;
  background: #eff6ff;
  transform: translateY(-1px);
}

/* Sections General */
#cb-landing-main section {
  padding: 88px 0;
  position: relative;
}
#cb-landing-main .surface {
  background: #ffffff;
  border-top: 1px solid var(--cb-line);
  border-bottom: 1px solid var(--cb-line);
}
#cb-landing-main .section-head {
  max-width: 820px;
  margin-bottom: 38px;
}
#cb-landing-main .section-head.center {
  text-align: center;
  margin-inline: auto;
}
#cb-landing-main .kicker {
  font-size: 11.5px;
  font-weight: 850;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--cb-blue);
  margin-bottom: 12px;
  display: inline-block;
}
#cb-landing-main .section-head h2 {
  font-size: clamp(34px, 4.8vw, 56px);
  line-height: 1.05;
  letter-spacing: -0.045em;
  margin: 0 0 16px;
  font-weight: 850;
  color: var(--cb-ink);
}
#cb-landing-main .section-head p {
  font-size: 16.5px;
  color: #475569;
  line-height: 1.62;
  margin: 0;
}
#cb-landing-main .accent {
  color: var(--cb-blue);
}

/* Section: The Course Thesis (#difference) */
#cb-landing-main .split-story {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 22px;
}
#cb-landing-main .story-card {
  border: 1px solid var(--cb-line);
  border-radius: 20px;
  background: #ffffff;
  padding: 30px;
  box-shadow: var(--cb-shadow);
  border-top: 4px solid #3b82f6;
  position: relative;
  transition: transform 0.25s ease;
}
#cb-landing-main .story-card:hover {
  transform: translateY(-2px);
}
#cb-landing-main .story-card.dark {
  background: linear-gradient(145deg, #0b1120 0%, #151f32 100%);
  color: #ffffff;
  border-color: #1e293b;
  border-top: 4px solid #8b5cf6;
  box-shadow: 0 20px 50px -10px rgba(11, 17, 32, 0.4);
}
#cb-landing-main .story-card .label {
  font-size: 10.5px;
  text-transform: uppercase;
  letter-spacing: 0.09em;
  font-weight: 850;
  color: #64748b;
}
#cb-landing-main .story-card.dark .label {
  color: #94a3b8;
}
#cb-landing-main .story-card h3 {
  font-size: 25px;
  letter-spacing: -0.025em;
  margin: 12px 0 18px;
  font-weight: 800;
  color: var(--cb-ink);
}
#cb-landing-main .story-card.dark h3 {
  color: #ffffff;
}
#cb-landing-main .stack {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
#cb-landing-main .stack span {
  font-size: 11.5px;
  padding: 8px 11px;
  border-radius: 9px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  font-weight: 750;
  color: #334155;
}
#cb-landing-main .story-card.dark .stack span {
  background: #1e293b;
  border-color: #334155;
  color: #e2e8f0;
}

#cb-landing-main .bridge {
  grid-column: 1 / -1;
  background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 50%, #fefce8 100%);
  border: 1px solid #bfdbfe;
  border-radius: 20px;
  padding: 32px 34px;
  display: grid;
  grid-template-columns: 1.35fr 0.65fr;
  gap: 28px;
  align-items: center;
  box-shadow: 0 12px 30px -5px rgba(37, 99, 235, 0.08);
}
#cb-landing-main .bridge blockquote {
  font-size: clamp(22px, 2.9vw, 32px);
  line-height: 1.24;
  letter-spacing: -0.035em;
  margin: 0;
  font-weight: 800;
  color: #0f172a;
}
#cb-landing-main .bridge blockquote span {
  color: var(--cb-blue);
  text-decoration: underline;
  text-decoration-color: rgba(37, 99, 235, 0.3);
  text-underline-offset: 4px;
}
#cb-landing-main .bridge p {
  font-size: 14px;
  line-height: 1.6;
  color: #334155;
  margin: 0;
  font-weight: 500;
}

/* Section: S.C.A.L.E Method (#method) */
#cb-landing-main .compare {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 14px;
}
#cb-landing-main .compare .col {
  border: 1px solid var(--cb-line);
  border-radius: 16px;
  padding: 22px 18px;
  min-height: 180px;
  background: #ffffff;
  box-shadow: var(--cb-shadow);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  position: relative;
  overflow: hidden;
}
#cb-landing-main .compare .col:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.12);
}
#cb-landing-main .compare .col:nth-child(1) {
  border-top: 4px solid #2563eb;
  background: linear-gradient(180deg, #eff6ff 0%, #ffffff 45%);
}
#cb-landing-main .compare .col:nth-child(2) {
  border-top: 4px solid #10b981;
  background: linear-gradient(180deg, #ecfdf5 0%, #ffffff 45%);
}
#cb-landing-main .compare .col:nth-child(3) {
  border-top: 4px solid #8b5cf6;
  background: linear-gradient(180deg, #f5f3ff 0%, #ffffff 45%);
}
#cb-landing-main .compare .col:nth-child(4) {
  border-top: 4px solid #f43f5e;
  background: linear-gradient(180deg, #fff1f2 0%, #ffffff 45%);
}
#cb-landing-main .compare .col:nth-child(5) {
  border-top: 4px solid #f59e0b;
  background: linear-gradient(180deg, #fffbeb 0%, #ffffff 45%);
}
#cb-landing-main .compare .n {
  font-size: 11px;
  font-weight: 900;
  color: #94a3b8;
  letter-spacing: 0.05em;
}
#cb-landing-main .compare strong {
  display: block;
  font-size: 32px;
  font-weight: 900;
  margin: 10px 0 4px;
  letter-spacing: -0.04em;
}
#cb-landing-main .compare .col:nth-child(1) strong { color: #2563eb; }
#cb-landing-main .compare .col:nth-child(2) strong { color: #10b981; }
#cb-landing-main .compare .col:nth-child(3) strong { color: #8b5cf6; }
#cb-landing-main .compare .col:nth-child(4) strong { color: #f43f5e; }
#cb-landing-main .compare .col:nth-child(5) strong { color: #f59e0b; }
#cb-landing-main .compare b {
  font-size: 14px;
  font-weight: 800;
  color: var(--cb-ink);
  display: block;
}
#cb-landing-main .compare p {
  font-size: 12px;
  line-height: 1.55;
  color: #64748b;
  margin: 8px 0 0;
}

/* Method Sub-grid */
#cb-landing-main .method-grid {
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 24px;
  align-items: stretch;
}
#cb-landing-main .method-card {
  border: 1px solid var(--cb-line);
  border-radius: 20px;
  background: #ffffff;
  padding: 30px;
  box-shadow: var(--cb-shadow);
}
#cb-landing-main .method-card h3 {
  font-size: 23px;
  margin: 0 0 10px;
  letter-spacing: -0.02em;
  font-weight: 800;
  color: var(--cb-ink);
}
#cb-landing-main .method-card p {
  font-size: 13.5px;
  color: #475569;
  line-height: 1.6;
  margin: 0;
}
#cb-landing-main .evolve {
  margin-top: 24px;
  display: grid;
  gap: 12px;
}
#cb-landing-main .evolve-row {
  display: grid;
  grid-template-columns: 110px 1fr;
  gap: 14px;
  align-items: center;
}
#cb-landing-main .evolve-row small {
  font-size: 10.5px;
  font-weight: 850;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
#cb-landing-main .evolve-path {
  display: flex;
  align-items: center;
  gap: 7px;
  flex-wrap: wrap;
}
#cb-landing-main .evolve-path span {
  font-size: 10.5px;
  font-weight: 750;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  background: #f8fafc;
  padding: 7px 9px;
  color: #1e293b;
}
#cb-landing-main .evolve-path .new {
  background: linear-gradient(135deg, #eff6ff 0%, #f5f3ff 100%);
  border-color: #818cf8;
  color: #3730a3;
  font-weight: 800;
}

#cb-landing-main .pressure {
  background: linear-gradient(145deg, #0b1120 0%, #151f32 100%);
  color: #ffffff;
  border-color: #1e293b;
  box-shadow: 0 20px 50px -10px rgba(11, 17, 32, 0.4);
}
#cb-landing-main .pressure h3 {
  color: #ffffff;
}
#cb-landing-main .pressure p {
  color: #94a3b8;
}
#cb-landing-main .pressure .question {
  margin-top: 18px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  border-radius: 14px;
  padding: 15px;
}
#cb-landing-main .question small {
  color: #38bdf8;
  text-transform: uppercase;
  font-size: 9.5px;
  letter-spacing: 0.08em;
  font-weight: 850;
}
#cb-landing-main .question p {
  color: #f1f5f9;
  margin: 6px 0 0;
  font-size: 13px;
  line-height: 1.5;
  font-weight: 600;
}
#cb-landing-main .score {
  margin-top: 20px;
  display: grid;
  gap: 11px;
}
#cb-landing-main .score-row {
  display: grid;
  grid-template-columns: 90px 1fr 32px;
  gap: 10px;
  align-items: center;
  font-size: 11px;
  font-weight: 600;
}
#cb-landing-main .score-row i {
  height: 7px;
  background: #1e293b;
  border-radius: 99px;
  overflow: hidden;
  display: block;
}
#cb-landing-main .score-row i span {
  display: block;
  height: 100%;
  border-radius: 99px;
  background: linear-gradient(90deg, #3b82f6 0%, #38bdf8 100%);
}
#cb-landing-main .score-row b {
  text-align: right;
  font-size: 11.5px;
  color: #f8fafc;
}

/* Section: Modern AI System Design (#ai-era) */
#cb-landing-main .ai-era {
  background:
    radial-gradient(ellipse 80% 50% at 50% 0%, rgba(30, 58, 138, 0.3), transparent 70%),
    radial-gradient(ellipse 60% 50% at 85% 85%, rgba(124, 58, 237, 0.18), transparent 65%),
    linear-gradient(180deg, #070d18 0%, #0b1322 100%);
  color: #ffffff;
  border-top: 1px solid #1e293b;
  border-bottom: 1px solid #1e293b;
}
#cb-landing-main .ai-era .section-head h2 {
  color: #ffffff;
}
#cb-landing-main .ai-era .section-head p {
  color: #94a3b8;
}
#cb-landing-main .ai-era .kicker {
  color: #38bdf8;
}
#cb-landing-main .ai-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}
#cb-landing-main .ai-card {
  background: #111a2a;
  border: 1px solid #1e2e46;
  border-radius: 18px;
  padding: 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
  display: flex;
  flex-direction: column;
}
#cb-landing-main .ai-card:hover {
  transform: translateY(-4px);
  border-color: #3b82f6;
  box-shadow: 0 16px 40px -10px rgba(37, 99, 235, 0.35);
}
#cb-landing-main .ai-card .tag {
  display: inline-flex;
  align-self: flex-start;
  font-size: 9.5px;
  font-weight: 900;
  letter-spacing: 0.08em;
  padding: 5px 9px;
  border-radius: 7px;
  background: rgba(37, 99, 235, 0.18);
  color: #93c5fd;
  border: 1px solid rgba(59, 130, 246, 0.3);
}
#cb-landing-main .ai-card:nth-child(1) .tag { background: rgba(6, 182, 212, 0.18); color: #67e8f9; border-color: rgba(6, 182, 212, 0.35); }
#cb-landing-main .ai-card:nth-child(2) .tag { background: rgba(59, 130, 246, 0.18); color: #93c5fd; border-color: rgba(59, 130, 246, 0.35); }
#cb-landing-main .ai-card:nth-child(3) .tag { background: rgba(139, 92, 246, 0.18); color: #c4b5fd; border-color: rgba(139, 92, 246, 0.35); }
#cb-landing-main .ai-card:nth-child(4) .tag { background: rgba(16, 185, 129, 0.18); color: #6ee7b7; border-color: rgba(16, 185, 129, 0.35); }
#cb-landing-main .ai-card:nth-child(5) .tag { background: rgba(245, 158, 11, 0.18); color: #fde68a; border-color: rgba(245, 158, 11, 0.35); }
#cb-landing-main .ai-card:nth-child(6) .tag { background: rgba(244, 63, 94, 0.18); color: #fda4af; border-color: rgba(244, 63, 94, 0.35); }

#cb-landing-main .ai-card h3 {
  font-size: 19px;
  margin: 14px 0 8px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.015em;
}
#cb-landing-main .ai-card p {
  font-size: 12.5px;
  color: #94a3b8;
  line-height: 1.55;
  margin: 0;
  flex-grow: 1;
}
#cb-landing-main .mini-flow {
  margin-top: 18px;
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
  padding-top: 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}
#cb-landing-main .mini-flow span {
  font-size: 9.5px;
  padding: 5px 8px;
  border-radius: 6px;
  border: 1px solid #28374a;
  background: #0b121e;
  color: #cbd5e1;
  font-weight: 750;
}
#cb-landing-main .mini-flow i {
  font-style: normal;
  color: #64748b;
  font-size: 10px;
}

/* Section: Curriculum Learning Path (#curriculum) */
#cb-landing-main .curriculum-layout {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 320px;
  gap: 32px;
  align-items: start;
}
#cb-landing-main .phases {
  display: grid;
  gap: 12px;
}
#cb-landing-main .phase {
  border: 1px solid var(--cb-line);
  background: #ffffff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
#cb-landing-main .phase[open] {
  border-color: #93c5fd;
  box-shadow: 0 8px 24px -6px rgba(37, 99, 235, 0.12);
}
#cb-landing-main .phase summary {
  list-style: none;
  cursor: pointer;
  padding: 20px;
  display: grid;
  grid-template-columns: 36px 1fr 24px;
  gap: 14px;
  align-items: start;
  user-select: none;
}
#cb-landing-main .phase summary::-webkit-details-marker {
  display: none;
}
#cb-landing-main .phase-no {
  font-size: 11px;
  font-weight: 900;
  color: #2563eb;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 8px;
  display: grid;
  place-items: center;
  width: 32px;
  height: 32px;
}
#cb-landing-main .phase-title b {
  font-size: 16px;
  color: var(--cb-ink);
  font-weight: 800;
  display: block;
  line-height: 1.3;
}
#cb-landing-main .phase-title span {
  display: block;
  font-size: 12px;
  color: #64748b;
  margin-top: 4px;
  line-height: 1.4;
}
#cb-landing-main .phase summary:after {
  content: "+";
  font-size: 20px;
  font-weight: 600;
  color: #64748b;
  text-align: right;
  line-height: 1;
}
#cb-landing-main .phase[open] summary:after {
  content: "\2013";
  color: #2563eb;
}
#cb-landing-main .phase-content {
  border-top: 1px solid #f1f5f9;
  padding: 20px 20px 24px 70px;
  background: #fbfcfe;
}
#cb-landing-main .phase-content p {
  font-size: 13px;
  color: #475569;
  margin: 0 0 16px;
}
#cb-landing-main .topic-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}
#cb-landing-main .topic {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 9px 12px;
  font-size: 11.5px;
  font-weight: 600;
  color: #334155;
  box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}

/* Curriculum Sidebar Card (#start) */
#cb-landing-main .side {
  position: sticky;
  top: 96px;
  border: 1px solid var(--cb-line);
  background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  border-radius: 20px;
  padding: 26px;
  box-shadow: 0 16px 40px -10px rgba(15, 23, 42, 0.08);
  border-top: 4px solid var(--cb-blue);
}
#cb-landing-main .side h3 {
  font-size: 22px;
  margin: 10px 0 6px;
  font-weight: 850;
  color: var(--cb-ink);
  letter-spacing: -0.02em;
}
#cb-landing-main .side p {
  font-size: 13px;
  color: #64748b;
  margin: 0;
  line-height: 1.5;
}
#cb-landing-main .side-list {
  display: grid;
  gap: 11px;
  margin: 22px 0;
}
#cb-landing-main .side-list span {
  font-size: 12px;
  display: flex;
  gap: 8px;
  align-items: start;
  color: #334155;
  font-weight: 600;
}
#cb-landing-main .side-list i {
  font-style: normal;
  color: #10b981;
  font-weight: 900;
}
#cb-landing-main .side .btn {
  width: 100%;
}
#cb-landing-main .side small {
  display: block;
  text-align: center;
  color: #94a3b8;
  margin-top: 10px;
  font-size: 10px;
}

/* Section: Architecture Labs (#labs) */
#cb-landing-main .labs {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}
#cb-landing-main .lab {
  border: 1px solid var(--cb-line);
  background: #ffffff;
  border-radius: 18px;
  padding: 25px;
  box-shadow: var(--cb-shadow);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  display: flex;
  flex-direction: column;
}
#cb-landing-main .lab:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.12);
  border-color: #93c5fd;
}
#cb-landing-main .lab .meta {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-weight: 900;
  color: #2563eb;
  background: #eff6ff;
  border: 1px solid #dbeafe;
  padding: 4px 8px;
  border-radius: 6px;
  display: inline-block;
  align-self: flex-start;
}
#cb-landing-main .lab h3 {
  font-size: 19px;
  margin: 12px 0 8px;
  font-weight: 800;
  color: var(--cb-ink);
  letter-spacing: -0.015em;
}
#cb-landing-main .lab p {
  font-size: 12.5px;
  color: #475569;
  line-height: 1.55;
  margin: 0;
  flex-grow: 1;
}
#cb-landing-main .labflow {
  margin-top: 18px;
  display: flex;
  gap: 6px;
  align-items: center;
  flex-wrap: wrap;
  padding-top: 14px;
  border-top: 1px solid #f1f5f9;
}
#cb-landing-main .labflow span {
  font-size: 9.5px;
  border: 1px solid #cbd5e1;
  background: #f8fafc;
  border-radius: 6px;
  padding: 5px 8px;
  font-weight: 750;
  color: #334155;
}
#cb-landing-main .labflow i {
  font-style: normal;
  color: #94a3b8;
  font-size: 10px;
}

/* Section: AI Tutor in Lesson (#tutor) */
#cb-landing-main .tutor-section {
  background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  border-top: 1px solid var(--cb-line);
  border-bottom: 1px solid var(--cb-line);
}
#cb-landing-main .tutor-grid {
  display: grid;
  grid-template-columns: 0.82fr 1.18fr;
  gap: 46px;
  align-items: center;
}
#cb-landing-main .feature-list {
  display: grid;
  gap: 16px;
  margin-top: 28px;
}
#cb-landing-main .f {
  display: grid;
  grid-template-columns: 34px 1fr;
  gap: 12px;
}
#cb-landing-main .f i {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  display: grid;
  place-items: center;
  font-style: normal;
  font-size: 11px;
  font-weight: 900;
  color: #1d4ed8;
}
#cb-landing-main .f b {
  font-size: 14px;
  font-weight: 800;
  color: var(--cb-ink);
  display: block;
}
#cb-landing-main .f p {
  font-size: 12px;
  color: #64748b;
  margin: 3px 0 0;
  line-height: 1.5;
}

/* Tutor Product Board Preview */
#cb-landing-main .product-board {
  border: 1px solid var(--cb-line);
  border-radius: 22px;
  background: #f1f5f9;
  padding: 18px;
  box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.1);
}
#cb-landing-main .board-head {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  color: #64748b;
  margin-bottom: 14px;
  font-weight: 600;
  padding: 0 4px;
}
#cb-landing-main .board {
  display: grid;
  grid-template-columns: 1fr 0.74fr;
  gap: 12px;
}
#cb-landing-main .reading,
#cb-landing-main .assistant {
  border: 1px solid #cbd5e1;
  border-radius: 14px;
  background: #ffffff;
  padding: 18px;
}
#cb-landing-main .reading h4 {
  margin: 0 0 10px;
  font-size: 16px;
  font-weight: 800;
  color: var(--cb-ink);
}
#cb-landing-main .reading p {
  font-size: 11.5px;
  color: #475569;
  line-height: 1.6;
}
#cb-landing-main .selection {
  background: #fef08a;
  padding: 2px 4px;
  border-radius: 3px;
  color: #0f172a;
  font-weight: 600;
}
#cb-landing-main .pop {
  display: inline-flex;
  gap: 5px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 5px 7px;
  background: #ffffff;
  margin-top: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
#cb-landing-main .pop span {
  font-size: 9.5px;
  font-weight: 800;
  padding: 3px 6px;
  border-radius: 5px;
  color: #334155;
  background: #f8fafc;
  cursor: pointer;
}
#cb-landing-main .pop span:first-child {
  background: #eff6ff;
  color: #1d4ed8;
}
#cb-landing-main .assistant {
  background: #0f172a;
  color: #ffffff;
  border-color: #0f172a;
  display: flex;
  flex-direction: column;
}
#cb-landing-main .assistant small {
  color: #38bdf8;
  font-size: 9px;
  text-transform: uppercase;
  font-weight: 800;
  letter-spacing: 0.06em;
}
#cb-landing-main .assistant p {
  font-size: 11px;
  color: #e2e8f0;
  line-height: 1.55;
  margin: 6px 0 0;
}
#cb-landing-main .assistant .voicebox {
  margin-top: 12px;
  border: 1px solid #334155;
  background: #1e293b;
  border-radius: 8px;
  padding: 9px;
  text-align: center;
  font-size: 10px;
  font-weight: 750;
  color: #ffffff;
  cursor: pointer;
}
#cb-landing-main .bookmark {
  margin-top: auto;
  border-top: 1px solid #334155;
  padding-top: 10px;
  color: #94a3b8;
  font-size: 9.5px;
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Section: What You Leave With (Outcomes) */
#cb-landing-main .outcome {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 22px;
}
#cb-landing-main .outcome-card {
  border: 1px solid var(--cb-line);
  background: #ffffff;
  border-radius: 20px;
  padding: 30px;
  box-shadow: var(--cb-shadow);
}
#cb-landing-main .outcome-card h3 {
  font-size: 22px;
  margin: 0 0 16px;
  font-weight: 850;
  color: var(--cb-ink);
  letter-spacing: -0.02em;
}
#cb-landing-main .outcome-card ul {
  padding: 0;
  margin: 0;
  list-style: none;
  display: grid;
  gap: 12px;
}
#cb-landing-main .outcome-card li {
  font-size: 13px;
  color: #334155;
  display: flex;
  gap: 10px;
  line-height: 1.5;
}
#cb-landing-main .outcome-card li:before {
  content: "\2713";
  color: #10b981;
  font-weight: 900;
  font-size: 14px;
}
#cb-landing-main .outcome-card.emphasis {
  background: linear-gradient(145deg, #eff6ff 0%, #f5f3ff 100%);
  border-color: #bfdbfe;
  box-shadow: 0 16px 40px -10px rgba(37, 99, 235, 0.12);
}
#cb-landing-main .outcome-card.emphasis li:before {
  color: #2563eb;
}

/* Section: Final CTA (.final) */
#cb-landing-main .final {
  padding: 96px 0;
}
#cb-landing-main .final-card {
  background:
    radial-gradient(circle at 15% 20%, rgba(37, 99, 235, 0.35), transparent 50%),
    radial-gradient(circle at 85% 80%, rgba(245, 158, 11, 0.22), transparent 50%),
    linear-gradient(135deg, #0b1120 0%, #151f32 50%, #0b1120 100%);
  color: #ffffff;
  border-radius: 28px;
  padding: 56px 52px;
  display: grid;
  grid-template-columns: 1.25fr 0.75fr;
  gap: 40px;
  align-items: center;
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: 0 30px 80px -15px rgba(11, 17, 32, 0.6);
  position: relative;
  overflow: hidden;
}
#cb-landing-main .final-card h2 {
  font-size: clamp(34px, 4.6vw, 56px);
  line-height: 1.04;
  letter-spacing: -0.045em;
  margin: 0 0 16px;
  font-weight: 850;
  color: #ffffff;
}
#cb-landing-main .final-card p {
  color: #cbd5e1;
  font-size: 16px;
  line-height: 1.6;
  margin: 0;
  max-width: 650px;
}
#cb-landing-main .final-actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
#cb-landing-main .final-actions small {
  text-align: center;
  color: #94a3b8;
  font-size: 11px;
}

/* Sticky Mobile Bar */
#cb-landing-main .sticky-mobile {
  display: none;
}

/* Responsive Media Queries */
@media (max-width: 990px) {
  #cb-landing-main .hero-grid,
  #cb-landing-main .method-grid,
  #cb-landing-main .tutor-grid {
    grid-template-columns: 1fr;
  }
  #cb-landing-main .hero-grid {
    gap: 38px;
  }
  #cb-landing-main .hero h1 {
    max-width: 850px;
  }
  #cb-landing-main .course-window {
    max-width: 720px;
    margin-inline: auto;
  }
  #cb-landing-main .compare {
    grid-template-columns: repeat(3, 1fr);
  }
  #cb-landing-main .ai-grid,
  #cb-landing-main .labs {
    grid-template-columns: repeat(2, 1fr);
  }
  #cb-landing-main .curriculum-layout {
    grid-template-columns: 1fr;
  }
  #cb-landing-main .side {
    position: static;
  }
  #cb-landing-main .final-card {
    grid-template-columns: 1fr;
  }
  #cb-landing-main .bridge {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 680px) {
  #cb-landing-main {
    padding-bottom: 74px;
  }
  #cb-landing-main .wrap {
    width: min(100% - 28px, var(--cb-max));
  }
  #cb-landing-main .promo-ribbon {
    font-size: 11px;
    flex-wrap: wrap;
    gap: 8px;
    padding: 8px 12px;
  }
  #cb-landing-main .hero {
    padding-top: 40px;
  }
  #cb-landing-main .hero h1 {
    font-size: 42px;
  }
  #cb-landing-main .hero .lead {
    font-size: 16px;
  }
  #cb-landing-main .hero-stats {
    grid-template-columns: 1fr 1fr;
  }
  #cb-landing-main .hero-stats div:nth-child(3) {
    border-left: 0;
    padding-left: 12px;
  }
  #cb-landing-main .demo {
    grid-template-columns: 1fr;
  }
  #cb-landing-main .tutor {
    min-height: 290px;
  }
  #cb-landing-main .split-story,
  #cb-landing-main .outcome {
    grid-template-columns: 1fr;
  }
  #cb-landing-main .bridge {
    grid-column: auto;
  }
  #cb-landing-main .compare {
    grid-template-columns: 1fr;
  }
  #cb-landing-main .ai-grid,
  #cb-landing-main .labs {
    grid-template-columns: 1fr;
  }
  #cb-landing-main .topic-grid {
    grid-template-columns: 1fr;
  }
  #cb-landing-main .phase-content {
    padding-left: 20px;
  }
  #cb-landing-main .board {
    grid-template-columns: 1fr;
  }
  #cb-landing-main .final-card {
    padding: 34px 24px;
  }
  #cb-landing-main .final {
    padding-bottom: 60px;
  }

  /* Sticky Mobile Bar Active */
  #cb-landing-main .sticky-mobile {
    display: flex;
    position: fixed;
    z-index: 90;
    left: 12px;
    right: 12px;
    bottom: 12px;
    border: 1px solid rgba(203, 213, 225, 0.9);
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-radius: 14px;
    padding: 10px 14px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.16);
    align-items: center;
    gap: 12px;
  }
  #cb-landing-main .sticky-mobile div {
    flex: 1;
  }
  #cb-landing-main .sticky-mobile b {
    display: block;
    font-size: 12px;
    color: #0f172a;
  }
  #cb-landing-main .sticky-mobile span {
    font-size: 10px;
    color: var(--cb-muted);
  }
  #cb-landing-main .sticky-mobile .btn {
    padding: 9px 14px;
    font-size: 12px;
  }
}
</style>

<div id="cb-landing-main">

  <!-- Hero Section -->
  <section class="hero">
    <div class="wrap hero-grid">
      <div>
        <div class="eyebrow">
          <span class="eyebrow-beacon"></span>
          System Design &middot; AI-native &middot; Interview practice
        </div>
        <h1>Crack the <span class="gradient-text">System Design</span> Interview.</h1>
        <p class="lead">
          Learn to design scalable systems from first principles, explain the trade-offs behind them, and extend the same thinking to
          <strong>RAG, vector search, AI gateways, agents and realtime voice.</strong>
        </p>
        <p class="promise">
          Learn the systems interviewers have always asked&mdash;and the ones they're beginning to ask now.
        </p>
        <div class="hero-cta">
          <?php $cb_render_course_cta( 'btn-lg' ); ?>
          <a class="btn btn-lg" href="#curriculum">Explore curriculum &darr;</a>
        </div>
        <div class="micro">
          <span><i class="tick">&#10003;</i>Free account to begin</span>
          <span><i class="tick">&#10003;</i>Voice AI tutor</span>
          <span><i class="tick">&#10003;</i>Architecture labs</span>
          <span><i class="tick">&#10003;</i>AI mock interviews</span>
        </div>
        <div class="hero-stats">
          <div>
            <b><?php echo esc_html( $cb_roadmap_steps ); ?> steps</b>
            <span>in the complete learning roadmap</span>
          </div>
          <div>
            <b>6 featured labs</b>
            <span>classic and modern architecture prompts</span>
          </div>
          <div>
            <b><?php echo esc_html( $cb_published_lessons ); ?> lesson live</b>
            <span>with the roadmap being built in public</span>
          </div>
          <div>
            <b>1 method</b>
            <span>clarify &rarr; design &rarr; break &rarr; defend</span>
          </div>
        </div>
      </div>

      <!-- Course Window Preview -->
      <div class="course-window" aria-label="Course learning experience preview">
        <div class="windowtop">
          <i></i><i></i><i></i>
          <small>Course workspace preview &middot; AI gateway &amp; model routing</small>
        </div>
        <div class="demo">
          <div class="lesson">
            <small>Why direct model calls fail at scale</small>
            <h3>Your checkout service should not depend directly on one model provider.</h3>
            <p>
              At small scale, <span class="highlight">Backend &rarr; Model API</span> looks fine. In production it creates a single point of failure, inconsistent policy enforcement, weak cost visibility and provider lock-in.
            </p>
            <div class="toolrow">
              <span class="tool on">Ask AI</span>
              <span class="tool">Explain trade-off</span>
              <span class="tool">Bookmark</span>
            </div>
            <div class="arch">
              <div class="flow">
                <span class="node">Service</span>
                <span class="arrow">&rarr;</span>
                <span class="node ai">AI Gateway</span>
                <span class="arrow">&rarr;</span>
                <span class="node">Router</span>
                <span class="arrow">&rarr;</span>
                <span class="node">Model A</span>
                <span class="node">Model B</span>
              </div>
            </div>
            <div class="followup-box">
              <strong>Interview follow-up:</strong> What changes when p99 model latency rises from 800 ms to 8 s?
            </div>
          </div>
          <aside class="tutor">
            <div class="tutor-head">
              <span><i class="orb"></i>AI Tutor</span>
              <span class="voice-tag">Voice ready</span>
            </div>
            <div class="chat">
              <small>Selected from lesson</small>
              <p>&ldquo;Backend &rarr; Model API creates a single point of failure.&rdquo;</p>
            </div>
            <div class="chat">
              <small>Tutor</small>
              <p>Think of the model like any external dependency&mdash;but slower, more expensive and less deterministic. Add routing, timeouts, fallbacks, observability and cost controls before it becomes critical-path infrastructure.</p>
            </div>
            <div class="wave" aria-label="Audio voice wave simulation">
              <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
            </div>
            <div class="talk">
              <span class="talk-dot"></span>
              Talk to your tutor
            </div>
          </aside>
        </div>
      </div>
    </div>

    <!-- Quick Navigation Pill Bar -->
    <div class="wrap anchor">
      <div class="anchor-inner">
        <a href="#difference">Why this course</a>
        <a href="#method">How you'll learn</a>
        <a href="#ai-era">AI system design</a>
        <a href="#curriculum">Curriculum</a>
        <a href="#labs">Design labs</a>
        <a href="#tutor">AI tutor</a>
      </div>
    </div>
  </section>

  <!-- Section: The Course Thesis (#difference) -->
  <section class="surface" id="difference">
    <div class="wrap">
      <div class="section-head">
        <div class="kicker">The course thesis</div>
        <h2>The old stack is not disappearing. <span class="accent">It is getting a new layer.</span></h2>
        <p>Strong AI products still need APIs, databases, caching, queues, security, reliability and observability. Modern engineers also need to reason about retrieval quality, token cost, model latency, tool execution and probabilistic failure.</p>
      </div>
      <div class="split-story">
        <article class="story-card">
          <div class="label">The stack you already know</div>
          <h3>Traditional distributed systems</h3>
          <div class="stack">
            <span>APIs</span>
            <span>Load Balancers</span>
            <span>Redis</span>
            <span>Kafka</span>
            <span>SQL / NoSQL</span>
            <span>CDN</span>
            <span>Queues</span>
            <span>Microservices</span>
            <span>Observability</span>
          </div>
        </article>
        <article class="story-card dark">
          <div class="label">The layer appearing on top</div>
          <h3>AI-native production systems</h3>
          <div class="stack">
            <span>LLM Gateway</span>
            <span>Embeddings</span>
            <span>Vector Search</span>
            <span>RAG</span>
            <span>Reranking</span>
            <span>Agents</span>
            <span>MCP</span>
            <span>Evals</span>
            <span>Realtime Voice</span>
          </div>
        </article>
        <div class="bridge">
          <blockquote>
            &ldquo;I already know APIs, databases, Redis, Kafka and microservices. <span>What changes</span> when my application has an LLM, RAG pipeline, vector search, AI agent or voice model in the architecture?&rdquo;
          </blockquote>
          <p>That question is the bridge this course is built around. You learn traditional system design deeply first, then reuse those mental models when AI makes latency variable, output probabilistic, context stateful and each request materially expensive.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Section: CodesBlock Method (#method) -->
  <section id="method">
    <div class="wrap">
      <div class="section-head center">
        <div class="kicker">CodesBlock method</div>
        <h2>Most courses show the diagram. You learn to <span class="accent">arrive at it.</span></h2>
        <p>Every design problem follows the same reasoning loop until it becomes automatic under interview pressure.</p>
      </div>
      <div class="compare">
        <div class="col">
          <div class="n">01</div>
          <strong>S</strong>
          <b>Scope</b>
          <p>Clarify users, requirements, constraints and what is explicitly out of scope.</p>
        </div>
        <div class="col">
          <div class="n">02</div>
          <strong>C</strong>
          <b>Calculate</b>
          <p>Estimate QPS, storage, bandwidth, latency and cost before choosing components.</p>
        </div>
        <div class="col">
          <div class="n">03</div>
          <strong>A</strong>
          <b>Architect</b>
          <p>Draw APIs, data flow, services, storage and the system's critical path.</p>
        </div>
        <div class="col">
          <div class="n">04</div>
          <strong>L</strong>
          <b>Load-test</b>
          <p>Break the design with hotspots, failures, retries, scale and bad assumptions.</p>
        </div>
        <div class="col">
          <div class="n">05</div>
          <strong>E</strong>
          <b>Explain</b>
          <p>Defend trade-offs, alternatives and what you would change at 10&times; scale.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Section: Architecture Evolve & Pressure Testing -->
  <section class="surface">
    <div class="wrap method-grid">
      <article class="method-card">
        <div class="kicker">Architecture evolves</div>
        <h3>Start simple. Add complexity only when a requirement forces it.</h3>
        <p>You should be able to explain why every box exists. The course repeatedly starts from a naive design and lets constraints force the next architectural decision.</p>
        <div class="evolve">
          <div class="evolve-row">
            <small>v1</small>
            <div class="evolve-path">
              <span>Client</span>
              <span class="arrow">&rarr;</span>
              <span>Server</span>
              <span class="arrow">&rarr;</span>
              <span>Database</span>
            </div>
          </div>
          <div class="evolve-row">
            <small>scale</small>
            <div class="evolve-path">
              <span>Client</span>
              <span class="arrow">&rarr;</span>
              <span>LB</span>
              <span class="arrow">&rarr;</span>
              <span>Services</span>
              <span class="arrow">&rarr;</span>
              <span>Cache</span>
              <span class="arrow">&rarr;</span>
              <span>DB</span>
            </div>
          </div>
          <div class="evolve-row">
            <small>ai layer</small>
            <div class="evolve-path">
              <span>Services</span>
              <span class="arrow">&rarr;</span>
              <span class="new">AI Gateway</span>
              <span class="arrow">&rarr;</span>
              <span class="new">RAG / Tools</span>
              <span class="arrow">&rarr;</span>
              <span class="new">Models</span>
            </div>
          </div>
        </div>
      </article>
      <article class="method-card pressure">
        <div class="kicker" style="color:#38bdf8">Pressure testing</div>
        <h3>Your first answer is only the beginning.</h3>
        <p>The AI interviewer keeps changing one constraint so you practise adapting instead of memorizing finished solutions.</p>
        <div class="question">
          <small>Interviewer</small>
          <p>&ldquo;Your architecture works. Now one customer has 80 million followers. What breaks first?&rdquo;</p>
        </div>
        <div class="question">
          <small>Follow-up</small>
          <p>&ldquo;Now the model provider is degraded and p99 latency is 12 seconds. Keep the core product usable.&rdquo;</p>
        </div>
        <div class="score">
          <div class="score-row">
            <span>Scalability</span>
            <i><span style="width:84%"></span></i>
            <b>8.4</b>
          </div>
          <div class="score-row">
            <span>Reliability</span>
            <i><span style="width:62%"></span></i>
            <b>6.2</b>
          </div>
          <div class="score-row">
            <span>Cost</span>
            <i><span style="width:48%"></span></i>
            <b>4.8</b>
          </div>
        </div>
      </article>
    </div>
  </section>

  <!-- Section: Modern AI System Design (#ai-era) -->
  <section class="ai-era" id="ai-era">
    <div class="wrap">
      <div class="section-head">
        <div class="kicker">Modern system design</div>
        <h2>What changes when the dependency becomes <span class="ai-gradient-text">intelligent?</span></h2>
        <p>The course treats AI as architecture, not magic. Each topic connects back to concepts backend engineers already understand: APIs, queues, caching, state, failure isolation, access control, cost and observability.</p>
      </div>
      <div class="ai-grid">
        <article class="ai-card">
          <span class="tag">RAG</span>
          <h3>Retrieval systems</h3>
          <p>Ingestion, chunking, embeddings, vector indexes, hybrid search, reranking, freshness, permissions and context assembly.</p>
          <div class="mini-flow">
            <span>Docs</span><i>&rarr;</i><span>Embed</span><i>&rarr;</i><span>Retrieve</span><i>&rarr;</i><span>Rerank</span><i>&rarr;</i><span>LLM</span>
          </div>
        </article>
        <article class="ai-card">
          <span class="tag">GATEWAY</span>
          <h3>Model routing &amp; reliability</h3>
          <p>Centralize policy, rate limits, provider routing, fallbacks, streaming, semantic caching, token budgets and model outages.</p>
          <div class="mini-flow">
            <span>App</span><i>&rarr;</i><span>Gateway</span><i>&rarr;</i><span>Router</span><i>&rarr;</i><span>Models</span>
          </div>
        </article>
        <article class="ai-card">
          <span class="tag">AGENTS</span>
          <h3>Agents meet microservices</h3>
          <p>Tool schemas, agent state, durable jobs, permissions, idempotent actions, human approval, MCP and bounded autonomy.</p>
          <div class="mini-flow">
            <span>Agent</span><i>&rarr;</i><span>Tools</span><i>&rarr;</i><span>Services</span><i>&#8634;</i><span>State</span>
          </div>
        </article>
        <article class="ai-card">
          <span class="tag">EVALS</span>
          <h3>Quality becomes observable</h3>
          <p>Golden datasets, retrieval metrics, traces, regressions, online feedback, human review and evaluation gates.</p>
          <div class="mini-flow">
            <span>Trace</span><i>&rarr;</i><span>Eval</span><i>&rarr;</i><span>Score</span><i>&rarr;</i><span>Release</span>
          </div>
        </article>
        <article class="ai-card">
          <span class="tag">SECURITY</span>
          <h3>New failure surfaces</h3>
          <p>Prompt injection, poisoned retrieval, data leakage, tool abuse, excessive agency and authorization boundaries.</p>
          <div class="mini-flow">
            <span>Input</span><i>&rarr;</i><span>Policy</span><i>&rarr;</i><span>Tool</span><i>&rarr;</i><span>Verify</span>
          </div>
        </article>
        <article class="ai-card">
          <span class="tag">VOICE</span>
          <h3>Realtime AI</h3>
          <p>WebRTC, streaming audio, speech pipelines, barge-in, session state, tool calls and end-to-end latency budgets.</p>
          <div class="mini-flow">
            <span>Audio</span><i>&rarr;</i><span>Model</span><i>&rarr;</i><span>Tools</span><i>&rarr;</i><span>Audio</span>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- Section: Full Curriculum (#curriculum) -->
  <section id="curriculum">
    <div class="wrap">
      <div class="section-head">
        <div class="kicker">Full learning path</div>
        <h2>From a single server to <span class="accent">production AI architecture.</span></h2>
        <p>One progression instead of two disconnected courses. Open any phase below to inspect the syllabus.</p>
      </div>
      <div class="curriculum-layout">
        <div class="phases">
          <details class="phase" open>
            <summary>
              <span class="phase-no">01</span>
              <span class="phase-title">
                <b>Think like a system designer</b>
                <span>interview structure &middot; requirements &middot; estimation &middot; APIs &middot; trade-offs</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>Build the interview operating system before memorizing architecture.</p>
              <div class="topic-grid">
                <span class="topic">What system design interviews actually test</span>
                <span class="topic">Functional vs non-functional requirements</span>
                <span class="topic">QPS, storage, bandwidth and latency estimates</span>
                <span class="topic">API contracts and core entities</span>
                <span class="topic">Critical-path thinking</span>
                <span class="topic">SCALE-45 framework</span>
              </div>
            </div>
          </details>

          <details class="phase">
            <summary>
              <span class="phase-no">02</span>
              <span class="phase-title">
                <b>Scale &mdash; from one server to millions</b>
                <span>networking &middot; load balancing &middot; caching &middot; databases &middot; queues</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>Learn each building block as an answer to a specific bottleneck.</p>
              <div class="topic-grid">
                <span class="topic">HTTP, TCP/UDP, DNS, WebSockets and gRPC</span>
                <span class="topic">Load balancers, gateways and stateless services</span>
                <span class="topic">Redis, cache-aside, TTL, invalidation and stampede</span>
                <span class="topic">SQL/NoSQL, indexes, replication and sharding</span>
                <span class="topic">Kafka, queues, pub/sub, workers and backpressure</span>
                <span class="topic">CDNs, object storage and media delivery</span>
              </div>
            </div>
          </details>

          <details class="phase">
            <summary>
              <span class="phase-no">03</span>
              <span class="phase-title">
                <b>Distributed reality</b>
                <span>consistency &middot; failure &middot; concurrency &middot; reliability &middot; operations</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>Move beyond diagrams into the problems production systems actually have.</p>
              <div class="topic-grid">
                <span class="topic">CAP, consistency models and replication lag</span>
                <span class="topic">Quorums, leader election and consensus intuition</span>
                <span class="topic">Retries, timeouts, circuit breakers and bulkheads</span>
                <span class="topic">Idempotency, duplicate delivery and saga workflows</span>
                <span class="topic">Hot keys, partitions, skew and rate limiting</span>
                <span class="topic">Metrics, logs, traces, SLIs/SLOs and recovery</span>
              </div>
            </div>
          </details>

          <details class="phase">
            <summary>
              <span class="phase-no">04</span>
              <span class="phase-title">
                <b>Classic architecture labs</b>
                <span>feeds &middot; chat &middot; booking &middot; payments &middot; video &middot; collaboration</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>Apply the same interview framework repeatedly until it becomes automatic.</p>
              <div class="topic-grid">
                <span class="topic">URL shortener + rate limiter</span>
                <span class="topic">Chat, presence and notifications</span>
                <span class="topic">News feed and fan-out</span>
                <span class="topic">Ticket booking and concurrency</span>
                <span class="topic">Payments and idempotent workflows</span>
                <span class="topic">File sync, video streaming and nearby search</span>
              </div>
            </div>
          </details>

          <details class="phase">
            <summary>
              <span class="phase-no">05</span>
              <span class="phase-title">
                <b>AI becomes a backend dependency</b>
                <span>models &middot; gateways &middot; context &middot; routing &middot; cost &middot; fallbacks</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>Understand why an LLM behaves differently from a normal service dependency.</p>
              <div class="topic-grid">
                <span class="topic">Tokens, context windows and streaming</span>
                <span class="topic">Structured output and tool calling</span>
                <span class="topic">AI gateways, provider routing and fallback</span>
                <span class="topic">Token-based rate limits and cost budgets</span>
                <span class="topic">Prompt/context versioning and state</span>
                <span class="topic">Semantic caching and graceful degradation</span>
              </div>
            </div>
          </details>

          <details class="phase">
            <summary>
              <span class="phase-no">06</span>
              <span class="phase-title">
                <b>Production RAG &amp; semantic search</b>
                <span>ingestion &middot; embeddings &middot; vector indexes &middot; reranking &middot; permissions</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>Go far beyond &ldquo;vector DB + LLM&rdquo;. Design the complete information pipeline.</p>
              <div class="topic-grid">
                <span class="topic">Parsing, chunking and embedding pipelines</span>
                <span class="topic">HNSW and ANN intuition</span>
                <span class="topic">Vector vs lexical vs hybrid retrieval</span>
                <span class="topic">Reranking and context assembly</span>
                <span class="topic">Freshness, access control and multitenancy</span>
                <span class="topic">RAG quality, latency and failure modes</span>
              </div>
            </div>
          </details>

          <details class="phase">
            <summary>
              <span class="phase-no">07</span>
              <span class="phase-title">
                <b>Agents meet microservices</b>
                <span>tools &middot; MCP &middot; memory &middot; workflows &middot; safety &middot; durability</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>Keep deterministic services deterministic; let the agent operate through controlled interfaces.</p>
              <div class="topic-grid">
                <span class="topic">Workflow vs agent: when not to use autonomy</span>
                <span class="topic">Tool schemas, registries and discovery</span>
                <span class="topic">Short-term memory, long-term memory and checkpoints</span>
                <span class="topic">Retries, idempotent tool calls and durable execution</span>
                <span class="topic">MCP architecture and service integration</span>
                <span class="topic">Human approval, permissions and agent budgets</span>
              </div>
            </div>
          </details>

          <details class="phase">
            <summary>
              <span class="phase-no">08</span>
              <span class="phase-title">
                <b>Production AI reliability</b>
                <span>evals &middot; tracing &middot; security &middot; cost &middot; observability</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>The chapter that turns an AI demo into an operable production system.</p>
              <div class="topic-grid">
                <span class="topic">Golden sets, offline/online evals and regression tests</span>
                <span class="topic">Prompt, retrieval and tool-call tracing</span>
                <span class="topic">Hallucination and quality failure handling</span>
                <span class="topic">Prompt injection and poisoned retrieval</span>
                <span class="topic">Cost-per-request, routing and caching</span>
                <span class="topic">Provider outages, queues and degradation</span>
              </div>
            </div>
          </details>

          <details class="phase">
            <summary>
              <span class="phase-no">09</span>
              <span class="phase-title">
                <b>Realtime voice architecture</b>
                <span>WebRTC &middot; streaming &middot; interruption &middot; latency &middot; sessions</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>Use CodesBlock's own voice tutor as a system-design case study.</p>
              <div class="topic-grid">
                <span class="topic">STT &rarr; LLM &rarr; TTS vs speech-to-speech</span>
                <span class="topic">WebRTC and WebSockets</span>
                <span class="topic">Voice activity detection and barge-in</span>
                <span class="topic">Conversation/session state</span>
                <span class="topic">Tool use during voice conversations</span>
                <span class="topic">Latency budgets and concurrent-call scaling</span>
              </div>
            </div>
          </details>

          <details class="phase">
            <summary>
              <span class="phase-no">10</span>
              <span class="phase-title">
                <b>Pressure test &amp; capstone</b>
              <span>constraint ladders &middot; architecture report &middot; guided review path</span>
              </span>
            </summary>
            <div class="phase-content">
              <p>Finish by designing systems from a blank canvas and defending them under changing requirements.</p>
              <div class="topic-grid">
                <span class="topic">Classic architecture mock</span>
                <span class="topic">Senior reliability mock</span>
                <span class="topic">RAG architecture mock</span>
                <span class="topic">Agent architecture mock</span>
                <span class="topic">Voice architecture mock</span>
                <span class="topic">Capstone: design, break and defend</span>
              </div>
            </div>
          </details>
        </div>

        <!-- Sticky Enrollment Card (#start / #about-heading) -->
        <aside class="side" id="start">
          <div class="eyebrow" style="margin-bottom: 8px;">Start free</div>
          <h3>See the course before you commit.</h3>
          <p>Open the first lessons, explore the learning workspace and experience the engineering rigor first-hand.</p>
          <div class="side-list">
            <span><i>&#10003;</i> <?php echo esc_html( $cb_roadmap_steps ); ?>-step learning roadmap</span>
            <span><i>&#10003;</i> Six featured architecture labs</span>
            <span><i>&#10003;</i> Voice AI tutor in-context</span>
            <span><i>&#10003;</i> Highlights, notes and bookmarks</span>
            <span><i>&#10003;</i> Constraint-changing practice</span>
          </div>
          <?php $cb_render_course_cta(); ?>
          <small><?php echo esc_html( $cb_published_lessons ? sprintf( _n( '%d published lesson is ready to explore.', '%d published lessons are ready to explore.', $cb_published_lessons, 'codesblock' ), $cb_published_lessons ) : __( 'The first lesson is being prepared.', 'codesblock' ) ); ?></small>
        </aside>
      </div>
    </div>
  </section>

  <!-- Section: Architecture Labs (#labs) -->
  <section class="surface" id="labs">
    <div class="wrap">
      <div class="section-head">
        <div class="kicker">Architecture labs</div>
        <h2>Design systems where one changed assumption <span class="accent">changes the answer.</span></h2>
        <p>Classic questions establish the fundamentals. Modern labs then add retrieval, models, agents, realtime media and AI-specific failure modes.</p>
      </div>
      <div class="labs">
        <article class="lab">
          <div class="meta">Lab 04 &middot; Scale</div>
          <h3>Design a high-traffic feed</h3>
          <p>Fan-out, hot users, ranking, pagination, caching and eventual consistency.</p>
          <div class="labflow">
            <span>Post</span><i>&rarr;</i><span>Fan-out</span><i>&rarr;</i><span>Feed cache</span><i>&rarr;</i><span>User</span>
          </div>
        </article>

        <article class="lab">
          <div class="meta">Lab 07 &middot; Reliability</div>
          <h3>Design payment processing</h3>
          <p>Idempotency, ledgers, webhooks, reconciliation and partial failure recovery.</p>
          <div class="labflow">
            <span>Pay</span><i>&rarr;</i><span>Ledger</span><i>&rarr;</i><span>PSP</span><i>&rarr;</i><span>Reconcile</span>
          </div>
        </article>

        <article class="lab">
          <div class="meta">Lab 11 &middot; Retrieval</div>
          <h3>Design enterprise RAG</h3>
          <p>Permissions, ingestion freshness, hybrid retrieval, reranking and citations.</p>
          <div class="labflow">
            <span>Docs</span><i>&rarr;</i><span>Index</span><i>&rarr;</i><span>Retrieve</span><i>&rarr;</i><span>Answer</span>
          </div>
        </article>

        <article class="lab">
          <div class="meta">Lab 13 &middot; Platform</div>
          <h3>Design an AI gateway</h3>
          <p>Provider abstraction, routing, token limits, fallback, cost and observability.</p>
          <div class="labflow">
            <span>App</span><i>&rarr;</i><span>Gateway</span><i>&rarr;</i><span>Router</span><i>&rarr;</i><span>Models</span>
          </div>
        </article>

        <article class="lab">
          <div class="meta">Lab 16 &middot; Agents</div>
          <h3>Design a coding agent</h3>
          <p>Repo indexing, tool execution, sandboxes, task state, human review and evals.</p>
          <div class="labflow">
            <span>Repo</span><i>&rarr;</i><span>Agent</span><i>&rarr;</i><span>Tools</span><i>&rarr;</i><span>Sandbox</span>
          </div>
        </article>

        <article class="lab">
          <div class="meta">Lab 18 &middot; Realtime</div>
          <h3>Design a voice AI interviewer</h3>
          <p>Streaming media, interruptions, session memory, model latency and scoring.</p>
          <div class="labflow">
            <span>Voice</span><i>&rarr;</i><span>Realtime</span><i>&rarr;</i><span>Tools</span><i>&rarr;</i><span>Report</span>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- Section: Built into the Lesson / AI Tutor (#tutor) -->
  <section class="tutor-section" id="tutor">
    <div class="wrap tutor-grid">
      <div>
        <div class="kicker">Built into the lesson</div>
        <div class="section-head" style="margin-bottom:0">
          <h2>Your AI tutor should help you think&mdash;not replace the thinking.</h2>
          <p>Read first. Form an answer. Then use AI at the exact point you get stuck, without leaving the course.</p>
        </div>
        <div class="feature-list">
          <div class="f">
            <i>01</i>
            <div>
              <b>Highlight &rarr; ask</b>
              <p>Select a sentence or trade-off and explain it in context.</p>
            </div>
          </div>
          <div class="f">
            <i>02</i>
            <div>
              <b>Talk instead of type</b>
              <p>Use voice to ask follow-ups while you work through the architecture.</p>
            </div>
          </div>
          <div class="f">
            <i>03</i>
            <div>
              <b>Save useful thinking</b>
              <p>Turn highlights into bookmarks, notes and revision prompts.</p>
            </div>
          </div>
          <div class="f">
            <i>04</i>
            <div>
              <b>Ask the tutor to attack your answer</b>
              <p>Generate interviewer follow-ups instead of simply revealing the solution.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="product-board">
        <div class="board-head">
          <span>course / ai-gateway / lesson 04</span>
          <span>Example learning workspace</span>
        </div>
        <div class="board">
          <div class="reading">
            <h4>Fallback without cascading failure</h4>
            <p>
              If the primary model exceeds its latency budget, the gateway can route to a smaller fallback model.
              <span class="selection">But a fallback that receives the same traffic spike can fail for the same reason.</span>
            </p>
            <div class="pop">
              <span>Ask AI</span>
              <span>Explain</span>
              <span>Bookmark</span>
            </div>
            <div class="arch" style="margin-top:14px">
              <div class="flow">
                <span class="node">API</span>
                <span class="arrow">&rarr;</span>
                <span class="node ai">Gateway</span>
                <span class="arrow">&rarr;</span>
                <span class="node">Primary</span>
                <span class="node">Fallback</span>
              </div>
            </div>
          </div>
          <div class="assistant">
            <small>AI tutor &middot; selected text</small>
            <p>A fallback is useful only if it has independent capacity or a different failure profile. Otherwise it can turn one overloaded dependency into two.</p>
            <div class="voicebox">&#9679; Ask with voice</div>
            <div class="bookmark">&#9733; Saved note: fallback isolation + separate quota</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section: What you leave with (Outcomes) -->
  <section>
    <div class="wrap">
      <div class="section-head center">
        <div class="kicker">What you leave with</div>
        <h2>Not a folder of diagrams. A <span class="accent">repeatable way to reason.</span></h2>
      </div>
      <div class="outcome">
        <article class="outcome-card">
          <h3>Interview readiness</h3>
          <ul>
            <li>Turn vague prompts into a structured 45-minute conversation.</li>
            <li>Estimate scale before selecting infrastructure.</li>
            <li>Explain database, caching, messaging and consistency trade-offs.</li>
            <li>Handle &ldquo;what if?&rdquo; follow-ups without freezing.</li>
            <li>Communicate an architecture at junior, mid and senior depth.</li>
          </ul>
        </article>
        <article class="outcome-card emphasis">
          <h3>Modern architecture literacy</h3>
          <ul>
            <li>Recognize when RAG is useful&mdash;and when it is not.</li>
            <li>Reason about vector retrieval, reranking and permissions.</li>
            <li>Integrate models behind reliable backend services.</li>
            <li>Design bounded agents that safely call microservices.</li>
            <li>Discuss AI cost, evals, security, tracing and graceful degradation.</li>
          </ul>
        </article>
      </div>
    </div>
  </section>

  <!-- Section: Final CTA Banner -->
  <section class="final">
    <div class="wrap final-card">
      <div>
        <div class="kicker" style="color:#38bdf8">Crack the interview. Build the system.</div>
        <h2>Learn the old rules. Then learn what changed.</h2>
        <p>From databases, Redis, Kafka and microservices to RAG, model gateways, agents and realtime voice&mdash;build the architectural judgment to explain, adapt and defend your design.</p>
      </div>
      <div class="final-actions">
        <?php $cb_render_course_cta( 'darkbtn btn-lg' ); ?>
        <a class="btn ghost-dark btn-lg" href="#curriculum">Review curriculum &darr;</a>
        <small>Start with the learning workspace</small>
      </div>
    </div>
  </section>

  <!-- Sticky Mobile Bar -->
  <div class="sticky-mobile">
    <div>
      <b>Crack System Design</b>
      <span>Start with free lessons</span>
    </div>
    <?php $cb_render_course_cta(); ?>
  </div>

</div><!-- /#cb-landing-main -->

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Interactive tool buttons in lesson preview
  var tools = document.querySelectorAll('#cb-landing-main .tool');
  tools.forEach(function(tool) {
    tool.addEventListener('click', function() {
      tools.forEach(function(t) { t.classList.remove('on'); });
      this.classList.add('on');
    });
  });

  // Smooth scroll for anchor navigation
  var anchorLinks = document.querySelectorAll('#cb-landing-main .anchor a, #cb-landing-main a[href^="#"]');
  anchorLinks.forEach(function(link) {
    link.addEventListener('click', function(e) {
      var href = this.getAttribute('href');
      if (href && href.startsWith('#') && href.length > 1) {
        var target = document.querySelector(href);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });
});
</script>

<?php
get_footer();
