<?php
/**
 * Compact, code-native architecture preview for the System Design course card.
 *
 * @package CodesBlock
 */
?>
<figure class="sd-hero-visual">
	<svg viewBox="0 0 720 405" role="img" aria-labelledby="sd-hero-title sd-hero-desc">
		<title id="sd-hero-title"><?php esc_html_e( 'Ticket booking system architecture', 'codesblock' ); ?></title>
		<desc id="sd-hero-desc"><?php esc_html_e( 'Clients connect through an API gateway to search and booking services, a seat lock, primary database, cache, and event stream.', 'codesblock' ); ?></desc>
		<defs>
			<marker id="sd-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse"><path d="M0 0 10 5 0 10z" fill="#58708f"/></marker>
			<linearGradient id="sd-board" x1="0" x2="1"><stop stop-color="#f9fbff"/><stop offset="1" stop-color="#eef7f4"/></linearGradient>
		</defs>
		<rect width="720" height="405" rx="22" fill="url(#sd-board)"/>
		<path d="M0 47H720" stroke="#d8e2ed"/>
		<circle cx="28" cy="24" r="6" fill="#ff826f"/><circle cx="49" cy="24" r="6" fill="#ffc34f"/><circle cx="70" cy="24" r="6" fill="#32c98d"/>
		<text x="99" y="29" fill="#60708a" font-size="13" font-family="Source Sans 3, sans-serif" font-weight="700">DESIGN LAB 08  /  TICKET BOOKING</text>

		<g fill="none" stroke="#58708f" stroke-width="3" marker-end="url(#sd-arrow)">
			<path d="M128 158H217"/><path d="M321 158H394"/><path d="M498 158H584"/>
			<path d="M446 194V266H548"/><path d="M446 194V324H548"/>
		</g>
		<g font-family="Source Sans 3, sans-serif">
			<g transform="translate(34 118)"><rect width="94" height="80" rx="13" fill="#fff" stroke="#b9c9da" stroke-width="2"/><rect x="24" y="15" width="46" height="34" rx="4" fill="#eaf1ff" stroke="#2864dc" stroke-width="2"/><path d="M17 58H77" stroke="#2864dc" stroke-width="3"/><text x="47" y="73" text-anchor="middle" fill="#24354d" font-size="12" font-weight="700">CLIENTS</text></g>
			<g transform="translate(218 118)"><rect width="104" height="80" rx="13" fill="#173a8a"/><path d="M30 29H74M63 18l11 11-11 11M41 18 30 29l11 11" stroke="#fff" stroke-width="3" fill="none"/><text x="52" y="64" text-anchor="middle" fill="#fff" font-size="12" font-weight="700">API GATEWAY</text></g>
			<g transform="translate(394 118)"><rect width="104" height="80" rx="13" fill="#fff" stroke="#9eb4ca" stroke-width="2"/><circle cx="52" cy="30" r="14" fill="#dff7ee" stroke="#13a978" stroke-width="2"/><path d="M46 30l4 4 8-9" stroke="#087a58" stroke-width="3" fill="none"/><text x="52" y="64" text-anchor="middle" fill="#24354d" font-size="12" font-weight="700">BOOKING</text></g>
			<g transform="translate(584 118)"><rect width="102" height="80" rx="13" fill="#fff5cf" stroke="#d8a623" stroke-width="2"/><path d="M34 31h34v23H34zM40 25v12M62 25v12" stroke="#9b6c00" stroke-width="3" fill="none"/><text x="51" y="68" text-anchor="middle" fill="#5b440a" font-size="12" font-weight="700">SEAT LOCK</text></g>
			<g transform="translate(548 235)"><rect width="138" height="58" rx="12" fill="#eaf1ff" stroke="#5c83d3" stroke-width="2"/><ellipse cx="27" cy="20" rx="12" ry="6" fill="none" stroke="#2864dc" stroke-width="2"/><path d="M15 20v17c0 8 24 8 24 0V20" fill="none" stroke="#2864dc" stroke-width="2"/><text x="52" y="35" fill="#24354d" font-size="12" font-weight="700">ORDERS DB</text></g>
			<g transform="translate(548 307)"><rect width="138" height="58" rx="12" fill="#e4f8f1" stroke="#42af8b" stroke-width="2"/><path d="M17 30h8l6-11 10 22 7-11h9" fill="none" stroke="#087a58" stroke-width="2.5"/><text x="67" y="35" fill="#164c40" font-size="12" font-weight="700">EVENT STREAM</text></g>
			<text x="34" y="368" fill="#60708a" font-size="12" font-weight="600">constraint: never sell the same seat twice</text>
		</g>
	</svg>
	<figcaption><span><?php esc_html_e( 'Architecture preview', 'codesblock' ); ?></span><strong><?php esc_html_e( 'Model the race condition before choosing the stack.', 'codesblock' ); ?></strong></figcaption>
</figure>
