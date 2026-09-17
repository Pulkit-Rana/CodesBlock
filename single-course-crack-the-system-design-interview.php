<?php
/**
 * Template Name: System Design Landing
 * Template Post Type: course, page
 *
 * @package CodesBlock
 */

get_header();
?>

<style>
/* CSS scoped to landing main to preserve original header/footer */
:root {
  --cb-bg:#f7f7f3;--cb-surface:#fff;--cb-ink:#111820;--cb-muted:#5e6975;--cb-line:#dde3e7;
  --cb-blue:#1768d5;--cb-blue2:#0e4ea8;--cb-navy:#111a23;--cb-soft-blue:#edf5ff;
  --cb-yellow:#f4d15a;--cb-green:#dff2e8;--cb-orange:#fff1d6;--cb-red:#ffe8e5;
  --cb-max:1180px;--cb-radius:18px;--cb-shadow:0 18px 48px rgba(17,24,32,.08);
}
#cb-landing-main {
  background: var(--cb-bg);
  color: var(--cb-ink);
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  line-height: 1.55;
  -webkit-font-smoothing: antialiased;
  padding-bottom: 0;
}
#cb-landing-main * { box-sizing: border-box; }
#cb-landing-main a { text-decoration: none; color: inherit; }
#cb-landing-main button { font: inherit; }
#cb-landing-main .wrap { width: min(var(--cb-max), calc(100% - 40px)); margin-inline: auto; }
#cb-landing-main .tiny { font-size: 12px; color: var(--cb-muted); }

/* Buttons */
#cb-landing-main .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 16px; border: 1px solid var(--cb-line); border-radius: 10px; background: #fff; color: var(--cb-ink); font-size: 13px; font-weight: 750; cursor: pointer; text-decoration: none; }
#cb-landing-main .btn:hover { border-color: #bfc9d1; }
#cb-landing-main .primary { background: var(--cb-blue); border-color: var(--cb-blue); color: #fff; box-shadow: 0 8px 18px rgba(23,104,213,.16); }
#cb-landing-main .primary:hover { background: var(--cb-blue2); color: #fff; }
#cb-landing-main .btn-lg { padding: 14px 20px; font-size: 14px; }
#cb-landing-main .darkbtn { background: #fff; color: #111; border-color: #fff; }
#cb-landing-main .ghost-dark { background: transparent; color: #fff; border-color: #34414d; }

#cb-landing-main .eyebrow { display: inline-flex; align-items: center; gap: 7px; font-size: 11px; font-weight: 850; text-transform: uppercase; letter-spacing: .09em; color: #3f5b72; margin: 0; }
#cb-landing-main .eyebrow:before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: var(--cb-blue); }

/* hero */
#cb-landing-main .hero { padding: 70px 0 42px; background: var(--cb-bg); }
#cb-landing-main .hero-grid { display: grid; grid-template-columns: 1.04fr .96fr; gap: 58px; align-items: center; }
#cb-landing-main .hero h1 { font-size: clamp(46px, 6vw, 76px); line-height: .98; letter-spacing: -.058em; margin: 18px 0 20px; max-width: 760px; font-family: inherit; font-weight: 800; color: var(--cb-ink); }
#cb-landing-main .hero h1 span { color: var(--cb-blue); }
#cb-landing-main .hero .lead { font-size: 19px; color: #49545f; max-width: 700px; margin: 0 0 22px; font-weight: 400; }
#cb-landing-main .hero .lead strong { color: var(--cb-ink); }
#cb-landing-main .hero .promise { font-size: 15px; font-weight: 780; max-width: 700px; border-left: 3px solid var(--cb-yellow); padding-left: 14px; margin: 22px 0; color: #27313b; }
#cb-landing-main .hero-cta { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 26px; }
#cb-landing-main .micro { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 14px; font-size: 12px; color: var(--cb-muted); }
#cb-landing-main .micro span { display: flex; gap: 6px; align-items: center; }
#cb-landing-main .tick { width: 16px; height: 16px; border-radius: 50%; background: var(--cb-green); display: grid; place-items: center; font-size: 10px; font-weight: 900; color: #187548; }

/* course window */
#cb-landing-main .course-window { background: #fff; border: 1px solid var(--cb-line); border-radius: 22px; overflow: hidden; box-shadow: var(--cb-shadow); }
#cb-landing-main .windowtop { height: 42px; border-bottom: 1px solid var(--cb-line); display: flex; align-items: center; padding: 0 14px; gap: 6px; background: #fafbf9; }
#cb-landing-main .windowtop i { width: 8px; height: 8px; border-radius: 50%; background: #c9cfd4; }
#cb-landing-main .windowtop small { margin-left: 8px; color: #7a838c; display: block; font-size: 11px; }
#cb-landing-main .demo { display: grid; grid-template-columns: 1.23fr .77fr; min-height: 470px; }
#cb-landing-main .lesson { padding: 24px; }
#cb-landing-main .lesson small { font-weight: 800; color: #6f7b86; text-transform: uppercase; letter-spacing: .08em; font-size: 9px; display: block; }
#cb-landing-main .lesson h3 { font-size: 22px; line-height: 1.17; margin: 8px 0 12px; font-family: inherit; color: var(--cb-ink); }
#cb-landing-main .lesson p { font-size: 13px; color: #4c5863; margin: 0; }
#cb-landing-main .highlight { background: linear-gradient(transparent 56%, rgba(244,209,90,.66) 56%); }
#cb-landing-main .toolrow { display: flex; gap: 6px; flex-wrap: wrap; margin: 13px 0; }
#cb-landing-main .tool { font-size: 10px; font-weight: 750; padding: 7px 8px; border: 1px solid var(--cb-line); border-radius: 7px; background: #fff; }
#cb-landing-main .tool.on { background: var(--cb-soft-blue); border-color: #b6d2f7; color: #0f55b3; }
#cb-landing-main .arch { margin-top: 18px; background: #f6f8fa; border: 1px solid #e1e6ea; border-radius: 13px; padding: 15px; }
#cb-landing-main .flow { display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap; }
#cb-landing-main .node { padding: 7px 8px; border: 1px solid #ccd5dc; border-radius: 7px; background: #fff; font-size: 9px; font-weight: 800; }
#cb-landing-main .node.ai { background: #edf5ff; border-color: #a9c9f3; }
#cb-landing-main .arrow { font-size: 11px; color: #8794a0; }

/* tutor */
#cb-landing-main .tutor { background: #111a23; color: #fff; padding: 18px; display: flex; flex-direction: column; }
#cb-landing-main .tutor-head { display: flex; justify-content: space-between; align-items: center; font-size: 12px; font-weight: 800; }
#cb-landing-main .orb { width: 23px; height: 23px; border-radius: 7px; background: linear-gradient(135deg, #6cb0ff, #f3d05b); display: inline-block; vertical-align: middle; margin-right: 7px; }
#cb-landing-main .voice-tag { font-size: 9px; color: #cdd6dd; border: 1px solid #35434e; padding: 5px 7px; border-radius: 99px; }
#cb-landing-main .chat { margin-top: 18px; background: #192630; border: 1px solid #2d3b47; border-radius: 12px; padding: 12px; }
#cb-landing-main .chat small { color: #93a4b2; font-size: 9px; display:block; }
#cb-landing-main .chat p { font-size: 11px; color: #e7edf2; margin: 6px 0 0; }
#cb-landing-main .wave { height: 45px; display: flex; align-items: center; justify-content: center; gap: 3px; margin: 14px 0; }
#cb-landing-main .wave i { width: 3px; background: #7eb6ff; border-radius: 4px; }
#cb-landing-main .wave i:nth-child(1) { height: 9px; }
#cb-landing-main .wave i:nth-child(2) { height: 17px; }
#cb-landing-main .wave i:nth-child(3) { height: 29px; }
#cb-landing-main .wave i:nth-child(4) { height: 18px; }
#cb-landing-main .wave i:nth-child(5) { height: 36px; }
#cb-landing-main .wave i:nth-child(6) { height: 24px; }
#cb-landing-main .wave i:nth-child(7) { height: 14px; }
#cb-landing-main .wave i:nth-child(8) { height: 31px; }
#cb-landing-main .talk { margin-top: auto; background: #fff; color: #111; border-radius: 9px; padding: 10px; text-align: center; font-size: 10px; font-weight: 850; }

/* hero stats */
#cb-landing-main .hero-stats { display: grid; grid-template-columns: repeat(4, 1fr); margin-top: 28px; border-top: 1px solid var(--cb-line); border-bottom: 1px solid var(--cb-line); }
#cb-landing-main .hero-stats div { padding: 15px 14px 15px 0; }
#cb-landing-main .hero-stats div+div { padding-left: 15px; border-left: 1px solid var(--cb-line); }
#cb-landing-main .hero-stats b { display: block; font-size: 16px; color: var(--cb-ink); }
#cb-landing-main .hero-stats span { font-size: 11px; color: var(--cb-muted); }

/* anchor */
#cb-landing-main .anchor { padding: 14px 0 0; }
#cb-landing-main .anchor-inner { display: flex; gap: 7px; flex-wrap: wrap; }
#cb-landing-main .anchor a { font-size: 11px; background: #fff; border: 1px solid var(--cb-line); border-radius: 8px; padding: 7px 10px; color: #53606b; }

/* sections */
#cb-landing-main section { padding: 82px 0; }
#cb-landing-main .surface { background: #fff; border-top: 1px solid var(--cb-line); border-bottom: 1px solid var(--cb-line); }
#cb-landing-main .section-head { max-width: 780px; margin-bottom: 34px; }
#cb-landing-main .section-head.center { text-align: center; margin-inline: auto; }
#cb-landing-main .kicker { font-size: 11px; font-weight: 850; letter-spacing: .09em; text-transform: uppercase; color: var(--cb-blue); margin-bottom: 10px; }
#cb-landing-main .section-head h2 { font-size: clamp(33px, 4.6vw, 54px); line-height: 1.04; letter-spacing: -.047em; margin: 0 0 14px; font-family: inherit; color: var(--cb-ink); }
#cb-landing-main .section-head p { font-size: 16px; color: var(--cb-muted); margin: 0; }
#cb-landing-main .accent { color: var(--cb-blue); }

/* split story */
#cb-landing-main .split-story { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
#cb-landing-main .story-card { border: 1px solid var(--cb-line); border-radius: 18px; background: #fff; padding: 26px; }
#cb-landing-main .story-card.dark { background: var(--cb-navy); color: #fff; border-color: var(--cb-navy); }
#cb-landing-main .story-card .label { font-size: 10px; text-transform: uppercase; letter-spacing: .09em; font-weight: 850; color: #74818d; }
#cb-landing-main .story-card.dark .label { color: #9fb0bd; }
#cb-landing-main .story-card h3 { font-size: 24px; letter-spacing: -.025em; margin: 11px 0 16px; font-family: inherit; color: inherit; }
#cb-landing-main .stack { display: flex; gap: 7px; flex-wrap: wrap; }
#cb-landing-main .stack span { font-size: 11px; padding: 7px 9px; border-radius: 8px; border: 1px solid var(--cb-line); background: #f7f8f9; font-weight: 700; }
#cb-landing-main .story-card.dark .stack span { background: #1d2a35; border-color: #34424e; color: #dfe7ed; }
#cb-landing-main .bridge { grid-column: 1/-1; background: #f1f5f8; border: 1px solid #dce4e9; border-radius: 18px; padding: 26px 28px; display: grid; grid-template-columns: 1.35fr .65fr; gap: 24px; align-items: center; }
#cb-landing-main .bridge blockquote { font-size: clamp(22px, 3vw, 34px); line-height: 1.18; letter-spacing: -.035em; margin: 0; font-weight: 780; color: var(--cb-ink); }
#cb-landing-main .bridge blockquote span { color: var(--cb-blue); }
#cb-landing-main .bridge p { font-size: 13px; color: var(--cb-muted); margin: 0; }

/* compare */
#cb-landing-main .compare { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
#cb-landing-main .compare .col { background: #fff; border: 1px solid var(--cb-line); border-radius: 14px; padding: 17px; min-height: 165px; }
#cb-landing-main .compare .n { font-size: 10px; font-weight: 900; color: #82909b; }
#cb-landing-main .compare strong { display: block; color: var(--cb-blue); font-size: 28px; margin: 9px 0 4px; font-weight: 800; }
#cb-landing-main .compare b { font-size: 13px; display:block; color: var(--cb-ink); }
#cb-landing-main .compare p { font-size: 11px; color: var(--cb-muted); margin: 7px 0 0; }

/* method grid */
#cb-landing-main .method-grid { display: grid; grid-template-columns: 1.05fr .95fr; gap: 22px; align-items: stretch; }
#cb-landing-main .method-card { border: 1px solid var(--cb-line); border-radius: 18px; background: #fff; padding: 25px; }
#cb-landing-main .method-card h3 { font-size: 22px; margin: 0 0 8px; font-family: inherit; color: var(--cb-ink); }
#cb-landing-main .method-card p { font-size: 13px; color: var(--cb-muted); margin: 0; }
#cb-landing-main .evolve { margin-top: 20px; display: grid; gap: 10px; }
#cb-landing-main .evolve-row { display: grid; grid-template-columns: 120px 1fr; gap: 12px; align-items: center; }
#cb-landing-main .evolve-row small { font-size: 10px; font-weight: 850; color: #6c7882; text-transform: uppercase; display: block; }
#cb-landing-main .evolve-path { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
#cb-landing-main .evolve-path span { font-size: 10px; font-weight: 750; border: 1px solid #d4dce2; border-radius: 7px; background: #f8fafb; padding: 7px 8px; }
#cb-landing-main .evolve-path .new { background: #edf5ff; border-color: #b9d4f7; color: #0c55b5; }
#cb-landing-main .pressure { background: var(--cb-navy); color: #fff; }
#cb-landing-main .pressure h3 { color: #fff; }
#cb-landing-main .pressure p { color: #aebbc5; }
#cb-landing-main .pressure .question { margin-top: 18px; border: 1px solid #33424e; background: #18252f; border-radius: 12px; padding: 13px; }
#cb-landing-main .question small { color: #8fa2b1; text-transform: uppercase; font-size: 9px; letter-spacing: .08em; font-weight: 850; display:block; }
#cb-landing-main .question p { color: #edf3f7; margin: 5px 0 0; font-size: 13px; }
#cb-landing-main .score { margin-top: 16px; display: grid; gap: 9px; }
#cb-landing-main .score-row { display: grid; grid-template-columns: 90px 1fr 28px; gap: 8px; align-items: center; font-size: 10px; }
#cb-landing-main .score-row i { height: 6px; background: #2e3d49; border-radius: 99px; overflow: hidden; display:block; }
#cb-landing-main .score-row i span { display: block; height: 100%; background: #79b4ff; }
#cb-landing-main .score-row b { text-align: right; font-size: 10px; }

/* ai-era */
#cb-landing-main .ai-era { background: var(--cb-navy); color: #fff; }
#cb-landing-main .ai-era h2 { color: #fff; }
#cb-landing-main .ai-era .section-head p { color: #aebbc5; }
#cb-landing-main .ai-era .kicker { color: #85bbff; }
#cb-landing-main .ai-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
#cb-landing-main .ai-card { background: #17232d; border: 1px solid #2c3a46; border-radius: 16px; padding: 21px; }
#cb-landing-main .ai-card .tag { display: inline-flex; font-size: 9px; font-weight: 900; letter-spacing: .07em; padding: 5px 7px; border-radius: 6px; background: #21394e; color: #9ec9ff; }
#cb-landing-main .ai-card h3 { font-size: 18px; margin: 13px 0 7px; font-family: inherit; color: #fff; }
#cb-landing-main .ai-card p { font-size: 12px; color: #adbac4; margin: 0; }
#cb-landing-main .mini-flow { margin-top: 16px; display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }
#cb-landing-main .mini-flow span { font-size: 9px; padding: 5px 6px; border-radius: 6px; border: 1px solid #364653; background: #111a23; }
#cb-landing-main .mini-flow i { font-style: normal; color: #7690a4; font-size: 9px; }

/* curriculum */
#cb-landing-main .curriculum-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 28px; align-items: start; }
#cb-landing-main .phases { display: grid; gap: 10px; }
#cb-landing-main .phase { border: 1px solid var(--cb-line); background: #fff; border-radius: 14px; overflow: hidden; }
#cb-landing-main .phase summary { list-style: none; cursor: pointer; padding: 18px; display: grid; grid-template-columns: 34px 1fr 20px; gap: 11px; align-items: start; }
#cb-landing-main .phase summary::-webkit-details-marker { display: none; }
#cb-landing-main .phase-no { font-size: 10px; font-weight: 900; color: #7a8792; padding-top: 4px; }
#cb-landing-main .phase-title b { font-size: 15px; display:block; color: var(--cb-ink); }
#cb-landing-main .phase-title span { display: block; font-size: 11px; color: var(--cb-muted); margin-top: 4px; }
#cb-landing-main .phase summary:after { content: "+"; font-size: 18px; color: #80909c; }
#cb-landing-main .phase[open] summary:after { content: "G«Ù"; }
#cb-landing-main .phase-content { border-top: 1px solid var(--cb-line); padding: 18px 18px 20px 63px; }
#cb-landing-main .phase-content p { font-size: 12px; color: var(--cb-muted); margin: 0 0 14px; }
#cb-landing-main .topic-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
#cb-landing-main .topic { background: #f7f9fa; border: 1px solid #e3e8eb; border-radius: 7px; padding: 8px; font-size: 10px; display:block; color: var(--cb-ink); }
#cb-landing-main .side { position: sticky; top: 92px; border: 1px solid var(--cb-line); background: #fff; border-radius: 17px; padding: 21px; box-shadow: 0 12px 32px rgba(16,24,32,.05); }
#cb-landing-main .side h3 { font-size: 21px; margin: 7px 0; font-family: inherit; color: var(--cb-ink); }
#cb-landing-main .side p { font-size: 12px; color: var(--cb-muted); }
#cb-landing-main .side-list { display: grid; gap: 9px; margin: 18px 0; }
#cb-landing-main .side-list span { font-size: 11px; display: flex; gap: 7px; align-items: start; }
#cb-landing-main .side-list i { font-style: normal; color: #187547; }
#cb-landing-main .side .btn { width: 100%; }
#cb-landing-main .side small { display: block; text-align: center; color: var(--cb-muted); margin-top: 8px; font-size: 9px; }

/* labs */
#cb-landing-main .labs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
#cb-landing-main .lab { border: 1px solid var(--cb-line); background: #fff; border-radius: 16px; padding: 20px; }
#cb-landing-main .lab .meta { font-size: 9px; text-transform: uppercase; letter-spacing: .08em; font-weight: 900; color: #6f7d88; display:block; }
#cb-landing-main .lab h3 { font-size: 18px; margin: 10px 0 7px; font-family: inherit; color: var(--cb-ink); }
#cb-landing-main .lab p { font-size: 12px; color: var(--cb-muted); margin: 0; }
#cb-landing-main .labflow { margin-top: 15px; display: flex; gap: 5px; align-items: center; flex-wrap: wrap; }
#cb-landing-main .labflow span { font-size: 9px; border: 1px solid #d8e0e5; background: #f7f9fa; border-radius: 6px; padding: 5px 6px; }
#cb-landing-main .labflow i { font-style: normal; color: #85939e; font-size: 9px; }

/* tutor section */
#cb-landing-main .tutor-section { background: #fff; border-top: 1px solid var(--cb-line); border-bottom: 1px solid var(--cb-line); }
#cb-landing-main .tutor-grid { display: grid; grid-template-columns: .8fr 1.2fr; gap: 38px; align-items: center; }
#cb-landing-main .feature-list { display: grid; gap: 13px; margin-top: 24px; }
#cb-landing-main .f { display: grid; grid-template-columns: 30px 1fr; gap: 10px; }
#cb-landing-main .f i { width: 27px; height: 27px; border-radius: 8px; background: #edf4fb; display: grid; place-items: center; font-style: normal; font-size: 10px; font-weight: 900; color: #0f5aba; }
#cb-landing-main .f b { font-size: 13px; display:block; color: var(--cb-ink); }
#cb-landing-main .f p { font-size: 11px; color: var(--cb-muted); margin: 3px 0 0; }
#cb-landing-main .product-board { border: 1px solid var(--cb-line); border-radius: 19px; background: #f6f8f9; padding: 17px; box-shadow: var(--cb-shadow); }
#cb-landing-main .board-head { display: flex; justify-content: space-between; font-size: 10px; color: #6c7984; margin-bottom: 12px; }
#cb-landing-main .board { display: grid; grid-template-columns: 1fr .72fr; gap: 11px; }
#cb-landing-main .reading, #cb-landing-main .assistant { border: 1px solid #dbe2e6; border-radius: 12px; background: #fff; padding: 15px; }
#cb-landing-main .reading h4 { margin: 0 0 8px; font-size: 15px; font-family: inherit; color: var(--cb-ink); }
#cb-landing-main .reading p { font-size: 10px; color: #53616c; }
#cb-landing-main .selection { background: #fff2b5; padding: 1px 2px; }
#cb-landing-main .pop { display: inline-flex; gap: 4px; border: 1px solid #d8e0e5; border-radius: 7px; padding: 5px; background: #fff; }
#cb-landing-main .pop span { font-size: 8px; font-weight: 800; padding: 3px 4px; }
#cb-landing-main .assistant { background: #121b24; color: #fff; border-color: #121b24; }
#cb-landing-main .assistant small { color: #94a5b3; font-size: 8px; display:block; }
#cb-landing-main .assistant p { font-size: 10px; color: #e6edf2; }
#cb-landing-main .assistant .voicebox { margin-top: 10px; border: 1px solid #32414c; background: #19252f; border-radius: 9px; padding: 9px; text-align: center; font-size: 9px; }
#cb-landing-main .bookmark { margin-top: 9px; border-top: 1px solid #32414c; padding-top: 9px; color: #abb9c4; font-size: 8px; display:block; }

/* outcome */
#cb-landing-main .outcome { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
#cb-landing-main .outcome-card { border: 1px solid var(--cb-line); background: #fff; border-radius: 17px; padding: 24px; }
#cb-landing-main .outcome-card h3 { font-size: 21px; margin: 0 0 13px; font-family: inherit; color: var(--cb-ink); }
#cb-landing-main .outcome-card ul { padding: 0; margin: 0; list-style: none; display: grid; gap: 9px; }
#cb-landing-main .outcome-card li { font-size: 12px; color: #4e5b66; display: flex; gap: 8px; }
#cb-landing-main .outcome-card li:before { content: "G£Ù"; color: #187548; font-weight: 900; }
#cb-landing-main .outcome-card.emphasis { background: #edf5ff; border-color: #c8dcf5; }
#cb-landing-main .outcome-card.emphasis li:before { color: #0d5bbd; }

/* final */
#cb-landing-main .final { padding: 92px 0; }
#cb-landing-main .final-card { background: var(--cb-navy); color: #fff; border-radius: 23px; padding: 48px; display: grid; grid-template-columns: 1.2fr .8fr; gap: 34px; align-items: center; }
#cb-landing-main .final-card h2 { font-size: clamp(34px, 4.5vw, 54px); line-height: 1.02; letter-spacing: -.045em; margin: 0 0 14px; font-family: inherit; color: #fff; }
#cb-landing-main .final-card p { color: #b6c2cb; margin: 0; max-width: 650px; }
#cb-landing-main .final-actions { display: flex; flex-direction: column; gap: 9px; }
#cb-landing-main .final-actions small { text-align: center; color: #8fa1ae; font-size: 9px; display:block; }

/* media queries */
@media(max-width:980px){
  #cb-landing-main .hero-grid, #cb-landing-main .method-grid, #cb-landing-main .tutor-grid { grid-template-columns: 1fr; }
  #cb-landing-main .hero-grid { gap: 34px; }
  #cb-landing-main .hero h1 { max-width: 850px; }
  #cb-landing-main .course-window { max-width: 720px; }
  #cb-landing-main .compare { grid-template-columns: repeat(3, 1fr); }
  #cb-landing-main .ai-grid, #cb-landing-main .labs { grid-template-columns: repeat(2, 1fr); }
  #cb-landing-main .curriculum-layout { grid-template-columns: 1fr; }
  #cb-landing-main .side { position: static; }
  #cb-landing-main .final-card { grid-template-columns: 1fr; }
  #cb-landing-main .bridge { grid-template-columns: 1fr; }
}
@media(max-width:680px){
  #cb-landing-main .wrap { width: min(100% - 26px, var(--cb-max)); }
  #cb-landing-main .hero { padding-top: 45px; }
  #cb-landing-main .hero h1 { font-size: 48px; }
  #cb-landing-main .hero .lead { font-size: 16px; }
  #cb-landing-main .hero-stats { grid-template-columns: 1fr 1fr; }
  #cb-landing-main .hero-stats div:nth-child(3) { border-left: 0; padding-left: 0; }
  #cb-landing-main .demo { grid-template-columns: 1fr; }
  #cb-landing-main .tutor { min-height: 290px; }
  #cb-landing-main .split-story, #cb-landing-main .outcome { grid-template-columns: 1fr; }
  #cb-landing-main .bridge { grid-column: auto; }
  #cb-landing-main .compare { grid-template-columns: 1fr; }
  #cb-landing-main .ai-grid, #cb-landing-main .labs { grid-template-columns: 1fr; }
  #cb-landing-main .topic-grid { grid-template-columns: 1fr; }
  #cb-landing-main .phase-content { padding-left: 18px; }
  #cb-landing-main .board { grid-template-columns: 1fr; }
  #cb-landing-main .final-card { padding: 30px 22px; }
  #cb-landing-main .final { padding-bottom: 95px; }
}
</style>

<div id="cb-landing-main">
<section class="hero">
  <div class="wrap hero-grid">
    <div>
      <div class="eyebrow">System Design -+ AI-native -+ Interview practice</div>
      <h1>Crack the <span>System Design</span> Interview.</h1>
      <p class="lead">Learn to design scalable systems from first principles, explain the trade-offs behind them, and extend the same thinking to <strong>RAG, vector search, AI gateways, agents and realtime voice.</strong></p>
      <p class="promise">Learn the systems interviewers have always askedG«ˆand the ones they're beginning to ask now.</p>
      <div class="hero-cta"><a class="btn primary btn-lg" href="#start">Start learning free GÂ∆</a><a class="btn btn-lg" href="#curriculum">Explore curriculum</a></div>
      <div class="micro"><span><i class="tick">G£Ù</i>No card required</span><span><i class="tick">G£Ù</i>Voice AI tutor</span><span><i class="tick">G£Ù</i>Architecture labs</span><span><i class="tick">G£Ù</i>AI mock interviews</span></div>
      <div class="hero-stats"><div><b>90+ lessons</b><span>from fundamentals to AI systems</span></div><div><b>18 design labs</b><span>classic + modern architectures</span></div><div><b>8 mock interviews</b><span>constraint-changing practice</span></div><div><b>1 method</b><span>clarify GÂ∆ design GÂ∆ break GÂ∆ defend</span></div></div>
    </div>

    <div class="course-window" aria-label="Course learning experience preview">
      <div class="windowtop"><i></i><i></i><i></i><small>Lesson 42 -+ AI gateway & model routing</small></div>
      <div class="demo">
        <div class="lesson"><small>Why direct model calls fail at scale</small><h3>Your checkout service should not depend directly on one model provider.</h3><p>At small scale, <span class="highlight">Backend GÂ∆ Model API</span> looks fine. In production it creates a single point of failure, inconsistent policy enforcement, weak cost visibility and provider lock-in.</p><div class="toolrow"><span class="tool on">Ask AI</span><span class="tool">Explain trade-off</span><span class="tool">Bookmark</span></div><div class="arch"><div class="flow"><span class="node">Service</span><span class="arrow">GÂ∆</span><span class="node ai">AI Gateway</span><span class="arrow">GÂ∆</span><span class="node">Router</span><span class="arrow">GÂ∆</span><span class="node">Model A</span><span class="node">Model B</span></div></div><p class="tiny" style="margin-top:12px">Interview follow-up: What changes when p99 model latency rises from 800 ms to 8 s?</p></div>
        <aside class="tutor"><div class="tutor-head"><span><i class="orb"></i>AI Tutor</span><span class="voice-tag">Voice ready</span></div><div class="chat"><small>Selected from lesson</small><p>G«£Backend GÂ∆ Model API creates a single point of failure.G«•</p></div><div class="chat"><small>Tutor</small><p>Think of the model like any external dependencyG«ˆbut slower, more expensive and less deterministic. Add routing, timeouts, fallbacks, observability and cost controls before it becomes critical-path infrastructure.</p></div><div class="wave"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div><div class="talk">G˘≈ Talk to your tutor</div></aside>
      </div>
    </div>
  </div>
  <div class="wrap anchor"><div class="anchor-inner"><a href="#difference">Why this course</a><a href="#method">How you'll learn</a><a href="#ai-era">AI system design</a><a href="#curriculum">Curriculum</a><a href="#labs">Design labs</a><a href="#tutor">AI tutor</a></div></div>
</section>

<section class="surface" id="difference">
  <div class="wrap">
    <div class="section-head"><div class="kicker">The course thesis</div><h2>The old stack is not disappearing. <span class="accent">It is getting a new layer.</span></h2><p>Strong AI products still need APIs, databases, caching, queues, security, reliability and observability. Modern engineers also need to reason about retrieval quality, token cost, model latency, tool execution and probabilistic failure.</p></div>
    <div class="split-story">
      <article class="story-card"><div class="label">The stack you already know</div><h3>Traditional distributed systems</h3><div class="stack"><span>APIs</span><span>Load Balancers</span><span>Redis</span><span>Kafka</span><span>SQL / NoSQL</span><span>CDN</span><span>Queues</span><span>Microservices</span><span>Observability</span></div></article>
      <article class="story-card dark"><div class="label">The layer appearing on top</div><h3>AI-native production systems</h3><div class="stack"><span>LLM Gateway</span><span>Embeddings</span><span>Vector Search</span><span>RAG</span><span>Reranking</span><span>Agents</span><span>MCP</span><span>Evals</span><span>Realtime Voice</span></div></article>
      <div class="bridge"><blockquote>G«£I already know APIs, databases, Redis, Kafka and microservices. <span>What changes</span> when my application has an LLM, RAG pipeline, vector search, AI agent or voice model in the architecture?G«•</blockquote><p>That question is the bridge this course is built around. You learn traditional system design deeply first, then reuse those mental models when AI makes latency variable, output probabilistic, context stateful and each request materially expensive.</p></div>
    </div>
  </div>
</section>

<section id="method">
  <div class="wrap">
    <div class="section-head center"><div class="kicker">CodesBlock method</div><h2>Most courses show the diagram. You learn to <span class="accent">arrive at it.</span></h2><p>Every design problem follows the same reasoning loop until it becomes automatic under interview pressure.</p></div>
    <div class="compare"><div class="col"><div class="n">01</div><strong>S</strong><b>Scope</b><p>Clarify users, requirements, constraints and what is explicitly out of scope.</p></div><div class="col"><div class="n">02</div><strong>C</strong><b>Calculate</b><p>Estimate QPS, storage, bandwidth, latency and cost before choosing components.</p></div><div class="col"><div class="n">03</div><strong>A</strong><b>Architect</b><p>Draw APIs, data flow, services, storage and the system's critical path.</p></div><div class="col"><div class="n">04</div><strong>L</strong><b>Load-test</b><p>Break the design with hotspots, failures, retries, scale and bad assumptions.</p></div><div class="col"><div class="n">05</div><strong>E</strong><b>Explain</b><p>Defend trade-offs, alternatives and what you would change at 10+˘ scale.</p></div></div>
  </div>
</section>

<section class="surface">
  <div class="wrap method-grid">
    <article class="method-card"><div class="kicker">Architecture evolves</div><h3>Start simple. Add complexity only when a requirement forces it.</h3><p>You should be able to explain why every box exists. The course repeatedly starts from a naive design and lets constraints force the next architectural decision.</p><div class="evolve"><div class="evolve-row"><small>v1</small><div class="evolve-path"><span>Client</span><span>GÂ∆</span><span>Server</span><span>GÂ∆</span><span>Database</span></div></div><div class="evolve-row"><small>scale</small><div class="evolve-path"><span>Client</span><span>GÂ∆</span><span>LB</span><span>GÂ∆</span><span>Services</span><span>GÂ∆</span><span>Cache</span><span>GÂ∆</span><span>DB</span></div></div><div class="evolve-row"><small>ai layer</small><div class="evolve-path"><span>Services</span><span>GÂ∆</span><span class="new">AI Gateway</span><span>GÂ∆</span><span class="new">RAG / Tools</span><span>GÂ∆</span><span class="new">Models</span></div></div></div></article>
    <article class="method-card pressure"><div class="kicker" style="color:#8ec0ff">Pressure testing</div><h3>Your first answer is only the beginning.</h3><p>The AI interviewer keeps changing one constraint so you practise adapting instead of memorizing finished solutions.</p><div class="question"><small>Interviewer</small><p>G«£Your architecture works. Now one customer has 80 million followers. What breaks first?G«•</p></div><div class="question"><small>Then</small><p>G«£Now the model provider is degraded and p99 latency is 12 seconds. Keep the core product usable.G«•</p></div><div class="score"><div class="score-row"><span>Scalability</span><i><span style="width:84%"></span></i><b>8.4</b></div><div class="score-row"><span>Reliability</span><i><span style="width:62%"></span></i><b>6.2</b></div><div class="score-row"><span>Cost</span><i><span style="width:48%"></span></i><b>4.8</b></div></div></article>
  </div>
</section>

<section class="ai-era" id="ai-era">
  <div class="wrap">
    <div class="section-head"><div class="kicker">Modern system design</div><h2>What changes when the dependency becomes <span style="color:#8ec0ff">intelligent?</span></h2><p>The course treats AI as architecture, not magic. Each topic connects back to concepts backend engineers already understand: APIs, queues, caching, state, failure isolation, access control, cost and observability.</p></div>
    <div class="ai-grid">
      <article class="ai-card"><span class="tag">RAG</span><h3>Retrieval systems</h3><p>Ingestion, chunking, embeddings, vector indexes, hybrid search, reranking, freshness, permissions and context assembly.</p><div class="mini-flow"><span>Docs</span><i>GÂ∆</i><span>Embed</span><i>GÂ∆</i><span>Retrieve</span><i>GÂ∆</i><span>Rerank</span><i>GÂ∆</i><span>LLM</span></div></article>
      <article class="ai-card"><span class="tag">GATEWAY</span><h3>Model routing & reliability</h3><p>Centralize policy, rate limits, provider routing, fallbacks, streaming, semantic caching, token budgets and model outages.</p><div class="mini-flow"><span>App</span><i>GÂ∆</i><span>Gateway</span><i>GÂ∆</i><span>Router</span><i>GÂ∆</i><span>Models</span></div></article>
      <article class="ai-card"><span class="tag">AGENTS</span><h3>Agents meet microservices</h3><p>Tool schemas, agent state, durable jobs, permissions, idempotent actions, human approval, MCP and bounded autonomy.</p><div class="mini-flow"><span>Agent</span><i>GÂ∆</i><span>Tools</span><i>GÂ∆</i><span>Services</span><i>GÂ¶</i><span>State</span></div></article>
      <article class="ai-card"><span class="tag">EVALS</span><h3>Quality becomes observable</h3><p>Golden datasets, retrieval metrics, traces, regressions, online feedback, human review and evaluation gates.</p><div class="mini-flow"><span>Trace</span><i>GÂ∆</i><span>Eval</span><i>GÂ∆</i><span>Score</span><i>GÂ∆</i><span>Release</span></div></article>
      <article class="ai-card"><span class="tag">SECURITY</span><h3>New failure surfaces</h3><p>Prompt injection, poisoned retrieval, data leakage, tool abuse, excessive agency and authorization boundaries.</p><div class="mini-flow"><span>Input</span><i>GÂ∆</i><span>Policy</span><i>GÂ∆</i><span>Tool</span><i>GÂ∆</i><span>Verify</span></div></article>
      <article class="ai-card"><span class="tag">VOICE</span><h3>Realtime AI</h3><p>WebRTC, streaming audio, speech pipelines, barge-in, session state, tool calls and end-to-end latency budgets.</p><div class="mini-flow"><span>Audio</span><i>GÂ∆</i><span>Model</span><i>GÂ∆</i><span>Tools</span><i>GÂ∆</i><span>Audio</span></div></article>
    </div>
  </div>
</section>

<section id="curriculum">
  <div class="wrap">
    <div class="section-head"><div class="kicker">Full learning path</div><h2>From a single server to <span class="accent">production AI architecture.</span></h2><p>One progression instead of two disconnected courses. Open a phase to see the material.</p></div>
    <div class="curriculum-layout">
      <div class="phases">
        <details class="phase" open><summary><span class="phase-no">01</span><span class="phase-title"><b>Think like a system designer</b><span>interview structure -+ requirements -+ estimation -+ APIs -+ trade-offs</span></span></summary><div class="phase-content"><p>Build the interview operating system before memorizing architecture.</p><div class="topic-grid"><span class="topic">What system design interviews actually test</span><span class="topic">Functional vs non-functional requirements</span><span class="topic">QPS, storage, bandwidth and latency estimates</span><span class="topic">API contracts and core entities</span><span class="topic">Critical-path thinking</span><span class="topic">SCALE-45 framework</span></div></div></details>
        <details class="phase"><summary><span class="phase-no">02</span><span class="phase-title"><b>Scale G«ˆ from one server to millions</b><span>networking -+ load balancing -+ caching -+ databases -+ queues</span></span></summary><div class="phase-content"><p>Learn each building block as an answer to a specific bottleneck.</p><div class="topic-grid"><span class="topic">HTTP, TCP/UDP, DNS, WebSockets and gRPC</span><span class="topic">Load balancers, gateways and stateless services</span><span class="topic">Redis, cache-aside, TTL, invalidation and stampede</span><span class="topic">SQL/NoSQL, indexes, replication and sharding</span><span class="topic">Kafka, queues, pub/sub, workers and backpressure</span><span class="topic">CDNs, object storage and media delivery</span></div></div></details>
        <details class="phase"><summary><span class="phase-no">03</span><span class="phase-title"><b>Distributed reality</b><span>consistency -+ failure -+ concurrency -+ reliability -+ operations</span></span></summary><div class="phase-content"><p>Move beyond diagrams into the problems production systems actually have.</p><div class="topic-grid"><span class="topic">CAP, consistency models and replication lag</span><span class="topic">Quorums, leader election and consensus intuition</span><span class="topic">Retries, timeouts, circuit breakers and bulkheads</span><span class="topic">Idempotency, duplicate delivery and saga workflows</span><span class="topic">Hot keys, partitions, skew and rate limiting</span><span class="topic">Metrics, logs, traces, SLIs/SLOs and recovery</span></div></div></details>
        <details class="phase"><summary><span class="phase-no">04</span><span class="phase-title"><b>Classic architecture labs</b><span>feeds -+ chat -+ booking -+ payments -+ video -+ collaboration</span></span></summary><div class="phase-content"><p>Apply the same interview framework repeatedly until it becomes automatic.</p><div class="topic-grid"><span class="topic">URL shortener + rate limiter</span><span class="topic">Chat, presence and notifications</span><span class="topic">News feed and fan-out</span><span class="topic">Ticket booking and concurrency</span><span class="topic">Payments and idempotent workflows</span><span class="topic">File sync, video streaming and nearby search</span></div></div></details>
        <details class="phase"><summary><span class="phase-no">05</span><span class="phase-title"><b>AI becomes a backend dependency</b><span>models -+ gateways -+ context -+ routing -+ cost -+ fallbacks</span></span></summary><div class="phase-content"><p>Understand why an LLM behaves differently from a normal service dependency.</p><div class="topic-grid"><span class="topic">Tokens, context windows and streaming</span><span class="topic">Structured output and tool calling</span><span class="topic">AI gateways, provider routing and fallback</span><span class="topic">Token-based rate limits and cost budgets</span><span class="topic">Prompt/context versioning and state</span><span class="topic">Semantic caching and graceful degradation</span></div></div></details>
        <details class="phase"><summary><span class="phase-no">06</span><span class="phase-title"><b>Production RAG & semantic search</b><span>ingestion -+ embeddings -+ vector indexes -+ reranking -+ permissions</span></span></summary><div class="phase-content"><p>Go far beyond G«£vector DB + LLMG«•. Design the complete information pipeline.</p><div class="topic-grid"><span class="topic">Parsing, chunking and embedding pipelines</span><span class="topic">HNSW and ANN intuition</span><span class="topic">Vector vs lexical vs hybrid retrieval</span><span class="topic">Reranking and context assembly</span><span class="topic">Freshness, access control and multitenancy</span><span class="topic">RAG quality, latency and failure modes</span></div></div></details>
        <details class="phase"><summary><span class="phase-no">07</span><span class="phase-title"><b>Agents meet microservices</b><span>tools -+ MCP -+ memory -+ workflows -+ safety -+ durability</span></span></summary><div class="phase-content"><p>Keep deterministic services deterministic; let the agent operate through controlled interfaces.</p><div class="topic-grid"><span class="topic">Workflow vs agent: when not to use autonomy</span><span class="topic">Tool schemas, registries and discovery</span><span class="topic">Short-term memory, long-term memory and checkpoints</span><span class="topic">Retries, idempotent tool calls and durable execution</span><span class="topic">MCP architecture and service integration</span><span class="topic">Human approval, permissions and agent budgets</span></div></div></details>
        <details class="phase"><summary><span class="phase-no">08</span><span class="phase-title"><b>Production AI reliability</b><span>evals -+ tracing -+ security -+ cost -+ observability</span></span></summary><div class="phase-content"><p>The chapter that turns an AI demo into an operable production system.</p><div class="topic-grid"><span class="topic">Golden sets, offline/online evals and regression tests</span><span class="topic">Prompt, retrieval and tool-call tracing</span><span class="topic">Hallucination and quality failure handling</span><span class="topic">Prompt injection and poisoned retrieval</span><span class="topic">Cost-per-request, routing and caching</span><span class="topic">Provider outages, queues and degradation</span></div></div></details>
        <details class="phase"><summary><span class="phase-no">09</span><span class="phase-title"><b>Realtime voice architecture</b><span>WebRTC -+ streaming -+ interruption -+ latency -+ sessions</span></span></summary><div class="phase-content"><p>Use CodesBlock's own voice tutor as a system-design case study.</p><div class="topic-grid"><span class="topic">STT GÂ∆ LLM GÂ∆ TTS vs speech-to-speech</span><span class="topic">WebRTC and WebSockets</span><span class="topic">Voice activity detection and barge-in</span><span class="topic">Conversation/session state</span><span class="topic">Tool use during voice conversations</span><span class="topic">Latency budgets and concurrent-call scaling</span></div></div></details>
        <details class="phase"><summary><span class="phase-no">10</span><span class="phase-title"><b>Pressure test & capstone</b><span>8 mocks -+ constraint ladders -+ architecture report -+ review path</span></span></summary><div class="phase-content"><p>Finish by designing systems from a blank canvas and defending them under changing requirements.</p><div class="topic-grid"><span class="topic">Classic architecture mock</span><span class="topic">Senior reliability mock</span><span class="topic">RAG architecture mock</span><span class="topic">Agent architecture mock</span><span class="topic">Voice architecture mock</span><span class="topic">Capstone: design, break and defend</span></div></div></details>
      </div>
      <aside class="side" id="start"><div class="eyebrow">Start free</div><h3>See the course before you commit.</h3><p>Open the first lessons, use the learning workspace and decide whether the teaching style works for you.</p><div class="side-list"><span><i>G£Ù</i>90+ guided lessons</span><span><i>G£Ù</i>18 architecture labs</span><span><i>G£Ù</i>Voice AI tutor in-context</span><span><i>G£Ù</i>Highlights, notes and bookmarks</span><span><i>G£Ù</i>8 adaptive AI mocks</span></div><a class="btn primary" href="#">Sign in to start GÂ∆</a><small>No payment required to begin</small></aside>
    </div>
  </div>
</section>

<section class="surface" id="labs">
  <div class="wrap">
    <div class="section-head"><div class="kicker">Architecture labs</div><h2>Design systems where one changed assumption <span class="accent">changes the answer.</span></h2><p>Classic questions establish the fundamentals. Modern labs then add retrieval, models, agents, realtime media and AI-specific failure modes.</p></div>
    <div class="labs"><article class="lab"><div class="meta">Lab 04 -+ Scale</div><h3>Design a high-traffic feed</h3><p>Fan-out, hot users, ranking, pagination, caching and eventual consistency.</p><div class="labflow"><span>Post</span><i>GÂ∆</i><span>Fan-out</span><i>GÂ∆</i><span>Feed cache</span><i>GÂ∆</i><span>User</span></div></article><article class="lab"><div class="meta">Lab 07 -+ Reliability</div><h3>Design payment processing</h3><p>Idempotency, ledgers, webhooks, reconciliation and partial failure recovery.</p><div class="labflow"><span>Pay</span><i>GÂ∆</i><span>Ledger</span><i>GÂ∆</i><span>PSP</span><i>GÂ∆</i><span>Reconcile</span></div></article><article class="lab"><div class="meta">Lab 11 -+ Retrieval</div><h3>Design enterprise RAG</h3><p>Permissions, ingestion freshness, hybrid retrieval, reranking and citations.</p><div class="labflow"><span>Docs</span><i>GÂ∆</i><span>Index</span><i>GÂ∆</i><span>Retrieve</span><i>GÂ∆</i><span>Answer</span></div></article><article class="lab"><div class="meta">Lab 13 -+ Platform</div><h3>Design an AI gateway</h3><p>Provider abstraction, routing, token limits, fallback, cost and observability.</p><div class="labflow"><span>App</span><i>GÂ∆</i><span>Gateway</span><i>GÂ∆</i><span>Router</span><i>GÂ∆</i><span>Models</span></div></article><article class="lab"><div class="meta">Lab 16 -+ Agents</div><h3>Design a coding agent</h3><p>Repo indexing, tool execution, sandboxes, task state, human review and evals.</p><div class="labflow"><span>Repo</span><i>GÂ∆</i><span>Agent</span><i>GÂ∆</i><span>Tools</span><i>GÂ∆</i><span>Sandbox</span></div></article><article class="lab"><div class="meta">Lab 18 -+ Realtime</div><h3>Design a voice AI interviewer</h3><p>Streaming media, interruptions, session memory, model latency and scoring.</p><div class="labflow"><span>Voice</span><i>GÂ∆</i><span>Realtime</span><i>GÂ∆</i><span>Tools</span><i>GÂ∆</i><span>Report</span></div></article></div>
  </div>
</section>

<section class="tutor-section" id="tutor">
  <div class="wrap tutor-grid">
    <div><div class="kicker">Built into the lesson</div><div class="section-head" style="margin-bottom:0"><h2>Your AI tutor should help you thinkG«ˆnot replace the thinking.</h2><p>Read first. Form an answer. Then use AI at the exact point you get stuck, without leaving the course.</p></div><div class="feature-list"><div class="f"><i>01</i><div><b>Highlight GÂ∆ ask</b><p>Select a sentence or trade-off and explain it in context.</p></div></div><div class="f"><i>02</i><div><b>Talk instead of type</b><p>Use voice to ask follow-ups while you work through the architecture.</p></div></div><div class="f"><i>03</i><div><b>Save useful thinking</b><p>Turn highlights into bookmarks, notes and revision prompts.</p></div></div><div class="f"><i>04</i><div><b>Ask the tutor to attack your answer</b><p>Generate interviewer follow-ups instead of simply revealing the solution.</p></div></div></div></div>
    <div class="product-board"><div class="board-head"><span>course / ai-gateway / lesson 04</span><span>68% complete</span></div><div class="board"><div class="reading"><h4>Fallback without cascading failure</h4><p>If the primary model exceeds its latency budget, the gateway can route to a smaller fallback model. <span class="selection">But a fallback that receives the same traffic spike can fail for the same reason.</span></p><div class="pop"><span>Ask AI</span><span>Explain</span><span>Bookmark</span></div><div class="arch" style="margin-top:13px"><div class="flow"><span class="node">API</span><span class="arrow">GÂ∆</span><span class="node ai">Gateway</span><span class="arrow">GÂ∆</span><span class="node">Primary</span><span class="node">Fallback</span></div></div></div><div class="assistant"><small>AI tutor -+ selected text</small><p>A fallback is useful only if it has independent capacity or a different failure profile. Otherwise it can turn one overloaded dependency into two.</p><div class="voicebox">G˘≈ Ask with voice</div><div class="bookmark">Saved note: fallback isolation + separate quota</div></div></div></div>
  </div>
</section>

<section>
  <div class="wrap">
    <div class="section-head center"><div class="kicker">What you leave with</div><h2>Not a folder of diagrams. A <span class="accent">repeatable way to reason.</span></h2></div>
    <div class="outcome"><article class="outcome-card"><h3>Interview readiness</h3><ul><li>Turn vague prompts into a structured 45-minute conversation.</li><li>Estimate scale before selecting infrastructure.</li><li>Explain database, caching, messaging and consistency trade-offs.</li><li>Handle G«£what if?G«• follow-ups without freezing.</li><li>Communicate an architecture at junior, mid and senior depth.</li></ul></article><article class="outcome-card emphasis"><h3>Modern architecture literacy</h3><ul><li>Recognize when RAG is usefulG«ˆand when it is not.</li><li>Reason about vector retrieval, reranking and permissions.</li><li>Integrate models behind reliable backend services.</li><li>Design bounded agents that safely call microservices.</li><li>Discuss AI cost, evals, security, tracing and graceful degradation.</li></ul></article></div>
  </div>
</section>

<section class="final">
  <div class="wrap final-card"><div><div class="kicker" style="color:#8fc2ff">Crack the interview. Build the system.</div><h2>Learn the old rules. Then learn what changed.</h2><p>From databases, Redis, Kafka and microservices to RAG, model gateways, agents and realtime voiceG«ˆbuild the architectural judgment to explain, adapt and defend your design.</p></div><div class="final-actions"><a class="btn darkbtn btn-lg" href="#">Start learning free GÂ∆</a><a class="btn ghost-dark btn-lg" href="#curriculum">Review curriculum</a><small>No card required to start</small></div></div>
</section>
</div>

<?php
get_footer();
