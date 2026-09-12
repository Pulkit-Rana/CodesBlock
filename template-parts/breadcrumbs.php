<?php
/**
 * Compact context trail for detail views.
 *
 * @package CodesBlock
 *
 * @var array{items?: array<int, array{label: string, url: string}>, current?: string} $args
 */

$items   = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
$current = isset( $args['current'] ) ? trim( (string) $args['current'] ) : '';

if ( '' === $current ) {
	return;
}
?>
<nav class="cb-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'codesblock' ); ?>">
	<ol>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'codesblock' ); ?></a></li>
		<?php foreach ( $items as $item ) : ?>
			<?php
			$label = isset( $item['label'] ) ? trim( (string) $item['label'] ) : '';
			$url   = isset( $item['url'] ) ? (string) $item['url'] : '';
			if ( '' === $label || '' === $url ) {
				continue;
			}
			?>
			<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
		<?php endforeach; ?>
		<li aria-current="page"><span><?php echo esc_html( $current ); ?></span></li>
	</ol>
</nav>
