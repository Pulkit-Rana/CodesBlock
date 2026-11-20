<?php
/**
 * Course curriculum explorer: phase-grouped module list beside one detail panel.
 *
 * Without JavaScript every module panel is shown in order; course-landing.js
 * turns the list into tabs.
 *
 * @package CodesBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$codesblock_cur_modules = isset( $args['modules'] ) ? (array) $args['modules'] : array();
$codesblock_cur_totals  = isset( $args['totals'] ) ? (array) $args['totals'] : array();
$codesblock_cur_labels  = isset( $args['kind_labels'] ) ? (array) $args['kind_labels'] : array();
$codesblock_cur_phases  = function_exists( 'codesblock_course_phases' ) ? codesblock_course_phases( $codesblock_cur_modules ) : array();

if ( ! $codesblock_cur_modules ) {
	return;
}

$codesblock_cur_phase_of = array();
foreach ( $codesblock_cur_phases as $codesblock_cur_phase ) {
	foreach ( $codesblock_cur_phase['modules'] as $codesblock_cur_index ) {
		$codesblock_cur_phase_of[ $codesblock_cur_index ] = $codesblock_cur_phase;
	}
}

$codesblock_cur_first = (int) array_key_first( $codesblock_cur_modules );
?>
<section class="cbl-section cbl-curriculum" aria-labelledby="syllabus-heading">
	<div class="cbl-section-head">
		<div>
			<p class="cbl-eyebrow"><?php esc_html_e( 'Curriculum', 'codesblock' ); ?></p>
			<h2 id="syllabus-heading"><?php esc_html_e( 'Your route through the course', 'codesblock' ); ?></h2>
			<p class="cbl-muted">
				<?php
				echo esc_html(
					sprintf(
						/* translators: 1: modules, 2: items */
						__( '%1$s modules · %2$s lessons, labs and drills. Pick a module to see what is inside.', 'codesblock' ),
						number_format_i18n( isset( $codesblock_cur_totals['modules'] ) ? $codesblock_cur_totals['modules'] : 0 ),
						number_format_i18n( isset( $codesblock_cur_totals['items'] ) ? $codesblock_cur_totals['items'] : 0 )
					)
				);
				?>
			</p>
		</div>
	</div>

	<?php if ( count( $codesblock_cur_phases ) > 1 ) : ?>
		<ol class="cbl-route" role="list" aria-label="<?php esc_attr_e( 'Course phases', 'codesblock' ); ?>">
			<?php foreach ( $codesblock_cur_phases as $codesblock_cur_phase ) : ?>
				<li class="is-<?php echo esc_attr( $codesblock_cur_phase['key'] ); ?>" style="--cbl-span: <?php echo esc_attr( max( 1, count( $codesblock_cur_phase['modules'] ) ) ); ?>">
					<strong><?php echo esc_html( $codesblock_cur_phase['label'] ); ?></strong>
					<?php if ( $codesblock_cur_phase['summary'] ) : ?>
						<span><?php echo esc_html( $codesblock_cur_phase['summary'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>

	<div class="cbl-explorer" data-cbl-explorer>
		<div class="cbl-explorer-nav" role="tablist" aria-label="<?php esc_attr_e( 'Modules', 'codesblock' ); ?>" aria-orientation="vertical" hidden>
			<?php foreach ( $codesblock_cur_phases as $codesblock_cur_phase ) : ?>
				<p class="cbl-explorer-phase is-<?php echo esc_attr( $codesblock_cur_phase['key'] ); ?>" aria-hidden="true"><?php echo esc_html( $codesblock_cur_phase['label'] ); ?></p>
				<?php foreach ( $codesblock_cur_phase['modules'] as $codesblock_cur_index ) : ?>
					<?php $codesblock_cur_module = $codesblock_cur_modules[ $codesblock_cur_index ]; ?>
					<button
						class="cbl-explorer-tab is-<?php echo esc_attr( $codesblock_cur_phase['key'] ); ?>"
						type="button"
						role="tab"
						id="cbl-mod-tab-<?php echo esc_attr( $codesblock_cur_index ); ?>"
						aria-controls="cbl-mod-<?php echo esc_attr( $codesblock_cur_index ); ?>"
						aria-selected="<?php echo $codesblock_cur_index === $codesblock_cur_first ? 'true' : 'false'; ?>"
						tabindex="<?php echo $codesblock_cur_index === $codesblock_cur_first ? '0' : '-1'; ?>"
					>
						<span class="cbl-explorer-num" aria-hidden="true"><?php echo esc_html( $codesblock_cur_module['is_bonus'] ? '+' : preg_replace( '/\D+/', '', (string) $codesblock_cur_module['label'] ) ); ?></span>
						<span class="cbl-explorer-name"><?php echo esc_html( $codesblock_cur_module['title'] ? $codesblock_cur_module['title'] : $codesblock_cur_module['label'] ); ?></span>
					</button>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</div>

		<div class="cbl-explorer-panels">
			<?php foreach ( $codesblock_cur_modules as $codesblock_cur_index => $codesblock_cur_module ) : ?>
				<?php
				$codesblock_cur_phase   = isset( $codesblock_cur_phase_of[ $codesblock_cur_index ] ) ? $codesblock_cur_phase_of[ $codesblock_cur_index ] : null;
				$codesblock_cur_lessons = array();
				$codesblock_cur_handson = array();
				foreach ( $codesblock_cur_module['items'] as $codesblock_cur_item ) {
					if ( 'lesson' === $codesblock_cur_item['kind'] ) {
						$codesblock_cur_lessons[] = $codesblock_cur_item;
					} else {
						$codesblock_cur_handson[] = $codesblock_cur_item;
					}
				}
				?>
				<article
					class="cbl-mod is-<?php echo esc_attr( $codesblock_cur_phase ? $codesblock_cur_phase['key'] : 'core' ); ?>"
					id="cbl-mod-<?php echo esc_attr( $codesblock_cur_index ); ?>"
					role="tabpanel"
					aria-labelledby="cbl-mod-title-<?php echo esc_attr( $codesblock_cur_index ); ?>"
					tabindex="0"
				>
					<header class="cbl-mod-head">
						<p class="cbl-mod-kicker">
							<?php echo esc_html( $codesblock_cur_module['label'] ); ?>
							<?php if ( $codesblock_cur_module['schedule'] ) : ?>
								<span><?php echo esc_html( $codesblock_cur_module['schedule'] ); ?></span>
							<?php endif; ?>
							<?php if ( $codesblock_cur_phase && count( $codesblock_cur_phases ) > 1 ) : ?>
								<em><?php echo esc_html( $codesblock_cur_phase['label'] ); ?></em>
							<?php endif; ?>
						</p>
						<h3 id="cbl-mod-title-<?php echo esc_attr( $codesblock_cur_index ); ?>"><?php echo esc_html( $codesblock_cur_module['title'] ? $codesblock_cur_module['title'] : __( 'Lessons', 'codesblock' ) ); ?></h3>
						<ul class="cbl-mod-counts" role="list">
							<?php if ( $codesblock_cur_lessons ) : ?>
								<li><b><?php echo esc_html( number_format_i18n( count( $codesblock_cur_lessons ) ) ); ?></b> <?php echo esc_html( _n( 'lesson', 'lessons', count( $codesblock_cur_lessons ), 'codesblock' ) ); ?></li>
							<?php endif; ?>
							<?php foreach ( $codesblock_cur_labels as $codesblock_cur_kind => $codesblock_cur_label ) : ?>
								<?php if ( ! empty( $codesblock_cur_module['counts'][ $codesblock_cur_kind ] ) ) : ?>
									<li class="cbl-tag cbl-tag-<?php echo esc_attr( $codesblock_cur_kind ); ?>"><b><?php echo esc_html( number_format_i18n( $codesblock_cur_module['counts'][ $codesblock_cur_kind ] ) ); ?></b> <?php echo esc_html( $codesblock_cur_label ); ?></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</header>

					<?php if ( $codesblock_cur_handson ) : ?>
						<p class="cbl-mod-label"><?php esc_html_e( 'You build and defend', 'codesblock' ); ?></p>
						<ul class="cbl-handson" role="list">
							<?php foreach ( $codesblock_cur_handson as $codesblock_cur_item ) : ?>
								<li class="is-<?php echo esc_attr( $codesblock_cur_item['kind'] ); ?>">
									<?php if ( isset( $codesblock_cur_labels[ $codesblock_cur_item['kind'] ] ) ) : ?>
										<em><?php echo esc_html( $codesblock_cur_labels[ $codesblock_cur_item['kind'] ] ); ?></em>
									<?php endif; ?>
									<span><?php echo esc_html( preg_replace( '/^(lab|case|capstone|deliverable|mock\s*\d*(\s*\([^)]*\))?|debug lab|break it at 10x)\s*[:·—-]\s*/i', '', $codesblock_cur_item['text'] ) ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $codesblock_cur_lessons ) : ?>
						<p class="cbl-mod-label"><?php esc_html_e( 'Lessons', 'codesblock' ); ?></p>
						<ol class="cbl-lessons" role="list">
							<?php foreach ( $codesblock_cur_lessons as $codesblock_cur_item ) : ?>
								<li><?php echo esc_html( $codesblock_cur_item['text'] ); ?></li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
