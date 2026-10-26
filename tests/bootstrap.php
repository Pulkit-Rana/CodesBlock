<?php
/**
 * Test Bootstrap & WordPress Mock Environment for CodesBlock Test Suite.
 *
 * Enables fast, isolated execution of unit tests without a database or full WP installation.
 *
 * @package CodesBlock\Tests
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/app/public/' );
}

if ( ! defined( 'WPINC' ) ) {
	define( 'WPINC', 'wp-includes' );
}

// Global registry for mocked hooks and state
$GLOBALS['_cb_mock_actions'] = array();
$GLOBALS['_cb_mock_filters'] = array();
$GLOBALS['_cb_mock_options'] = array();
$GLOBALS['_cb_mock_context'] = array(
	'is_front_page'         => false,
	'is_home'               => false,
	'is_singular'           => false,
	'is_page'               => false,
	'is_page_template'      => false,
	'is_post_type_archive'  => false,
	'post_type'             => 'post',
	'post_id'               => 1,
	'post_name'             => '',
	'post_meta'             => array(),
	'page_by_path'          => array(),
	'home_url'              => 'https://codesblock.test',
);

// WordPress Core Mock Functions
if ( ! function_exists( 'get_option' ) ) {
	function get_option( $option, $default = false ) {
		return array_key_exists( $option, $GLOBALS['_cb_mock_options'] ) ? $GLOBALS['_cb_mock_options'][ $option ] : $default;
	}
}

if ( ! function_exists( 'add_option' ) ) {
	function add_option( $option, $value = '', $deprecated = '', $autoload = 'yes' ) {
		if ( array_key_exists( $option, $GLOBALS['_cb_mock_options'] ) ) {
			return false;
		}
		$GLOBALS['_cb_mock_options'][ $option ] = $value;
		return true;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( $option, $value, $autoload = null ) {
		$GLOBALS['_cb_mock_options'][ $option ] = $value;
		return true;
	}
}

if ( ! function_exists( 'delete_option' ) ) {
	function delete_option( $option ) {
		if ( ! array_key_exists( $option, $GLOBALS['_cb_mock_options'] ) ) {
			return false;
		}
		unset( $GLOBALS['_cb_mock_options'][ $option ] );
		return true;
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['_cb_mock_actions'][ $hook_name ][] = array(
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		$GLOBALS['_cb_mock_filters'][ $hook_name ][] = array(
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
		return true;
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook_name, $value, ...$args ) {
		if ( empty( $GLOBALS['_cb_mock_filters'][ $hook_name ] ) ) {
			return $value;
		}
		foreach ( $GLOBALS['_cb_mock_filters'][ $hook_name ] as $filter ) {
			$callback = $filter['callback'];
			if ( is_callable( $callback ) ) {
				$value = call_user_func_array( $callback, array_merge( array( $value ), $args ) );
			}
		}
		return $value;
	}
}

if ( ! function_exists( 'do_action' ) ) {
	function do_action( $hook_name, ...$args ) {
		if ( empty( $GLOBALS['_cb_mock_actions'][ $hook_name ] ) ) {
			return;
		}
		foreach ( $GLOBALS['_cb_mock_actions'][ $hook_name ] as $action ) {
			$callback = $action['callback'];
			if ( is_callable( $callback ) ) {
				call_user_func_array( $callback, $args );
			}
		}
	}
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $maybeint ) {
		return abs( (int) $maybeint );
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $url ) {
		if ( '' === trim( (string) $url ) ) {
			return '';
		}
		$url = str_replace( ' ', '%20', (string) $url );
		$url = preg_replace( '|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url );
		return $url;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		return wp_strip_all_tags( (string) $str, true );
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $string, $remove_breaks = false ) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', (string) $string );
		$string = strip_tags( $string );
		if ( $remove_breaks ) {
			$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
		}
		return trim( $string );
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '', $scheme = null ) {
		$base = $GLOBALS['_cb_mock_context']['home_url'];
		return $base . ( '' !== $path ? '/' . ltrim( $path, '/' ) : '' );
	}
}

if ( ! function_exists( 'admin_url' ) ) {
	function admin_url( $path = '', $scheme = 'admin' ) {
		$base = 'relative' === $scheme ? '/wp-admin/' : 'https://codesblock.test/wp-admin/';
		return $base . ltrim( $path, '/' );
	}
}

if ( ! function_exists( 'get_template_directory' ) ) {
	function get_template_directory() {
		return dirname( __DIR__ );
	}
}

if ( ! function_exists( 'get_template_directory_uri' ) ) {
	function get_template_directory_uri() {
		return 'https://codesblock.test/wp-content/themes/codesblock';
	}
}

if ( ! function_exists( 'wp_create_nonce' ) ) {
	function wp_create_nonce( $action = -1 ) {
		return substr( hash_hmac( 'sha256', (string) $action, 'mock_secret_nonce_key' ), 0, 10 );
	}
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
	function wp_verify_nonce( $nonce, $action = -1 ) {
		$expected = substr( hash_hmac( 'sha256', (string) $action, 'mock_secret_nonce_key' ), 0, 10 );
		return hash_equals( $expected, (string) $nonce ) ? 1 : false;
	}
}

if ( ! function_exists( 'add_theme_support' ) ) {
	function add_theme_support( $feature, ...$args ) {
		return true;
	}
}

if ( ! function_exists( 'register_nav_menus' ) ) {
	function register_nav_menus( $locations = array() ) {
		return true;
	}
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		return true;
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		return true;
	}
}

if ( ! function_exists( 'wp_localize_script' ) ) {
	function wp_localize_script( $handle, $object_name, $l10n ) {
		return true;
	}
}

if ( ! function_exists( 'add_editor_style' ) ) {
	function add_editor_style( $stylesheet = 'editor-style.css' ) {
		return true;
	}
}

if ( ! function_exists( 'is_front_page' ) ) {
	function is_front_page() {
		return (bool) $GLOBALS['_cb_mock_context']['is_front_page'];
	}
}

if ( ! function_exists( 'is_home' ) ) {
	function is_home() {
		return (bool) $GLOBALS['_cb_mock_context']['is_home'];
	}
}

if ( ! function_exists( 'is_singular' ) ) {
	function is_singular( $post_types = '' ) {
		if ( ! $GLOBALS['_cb_mock_context']['is_singular'] ) {
			return false;
		}
		if ( empty( $post_types ) ) {
			return true;
		}
		$types = (array) $post_types;
		return in_array( $GLOBALS['_cb_mock_context']['post_type'], $types, true );
	}
}

if ( ! function_exists( 'is_page' ) ) {
	function is_page( $page = '' ) {
		return (bool) $GLOBALS['_cb_mock_context']['is_page'];
	}
}

if ( ! function_exists( 'is_page_template' ) ) {
	function is_page_template( $template = '' ) {
		return (bool) $GLOBALS['_cb_mock_context']['is_page_template'];
	}
}

if ( ! function_exists( 'is_post_type_archive' ) ) {
	function is_post_type_archive( $post_types = '' ) {
		if ( ! $GLOBALS['_cb_mock_context']['is_post_type_archive'] ) {
			return false;
		}
		if ( empty( $post_types ) ) {
			return true;
		}
		$types = (array) $post_types;
		return in_array( $GLOBALS['_cb_mock_context']['post_type'], $types, true );
	}
}

if ( ! function_exists( 'get_the_ID' ) ) {
	function get_the_ID() {
		return $GLOBALS['_cb_mock_context']['post_id'];
	}
}

if ( ! function_exists( 'get_post_field' ) ) {
	function get_post_field( $field, $post = null ) {
		if ( 'post_name' === $field ) {
			return $GLOBALS['_cb_mock_context']['post_name'];
		}
		return '';
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $post_id, $key = '', $single = false ) {
		$meta = $GLOBALS['_cb_mock_context']['post_meta'];
		if ( '' !== $key ) {
			return isset( $meta[ $key ] ) ? $meta[ $key ] : ( $single ? '' : array() );
		}
		return $meta;
	}
}

if ( ! function_exists( 'get_post_type_archive_link' ) ) {
	function get_post_type_archive_link( $post_type ) {
		return home_url( '/' . $post_type . 's/' );
	}
}

if ( ! function_exists( 'get_page_by_path' ) ) {
	function get_page_by_path( $page_path ) {
		return isset( $GLOBALS['_cb_mock_context']['page_by_path'][ $page_path ] )
			? (object) $GLOBALS['_cb_mock_context']['page_by_path'][ $page_path ]
			: null;
	}
}

if ( ! function_exists( 'get_permalink' ) ) {
	function get_permalink( $post = 0 ) {
		if ( is_object( $post ) && isset( $post->ID ) ) {
			return home_url( '/page-' . $post->ID . '/' );
		}
		return home_url( '/page/' );
	}
}

if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( (string) $url, $component );
	}
}

if ( ! function_exists( 'add_query_arg' ) ) {
	function add_query_arg( ...$args ) {
		if ( count( $args ) === 2 && is_string( $args[0] ) ) {
			return $args[1] . ( false === strpos( $args[1], '?' ) ? '?' : '&' ) . $args[0];
		}
		if ( count( $args ) === 3 ) {
			return $args[2] . ( false === strpos( $args[2], '?' ) ? '?' : '&' ) . rawurlencode( (string) $args[0] ) . '=' . rawurlencode( (string) $args[1] );
		}
		return is_string( end( $args ) ) ? end( $args ) : '';
	}
}

if ( ! function_exists( 'wp_count_posts' ) ) {
	function wp_count_posts( $type = 'post' ) {
		return (object) array( 'publish' => 42 );
	}
}

if ( ! function_exists( 'count_users' ) ) {
	function count_users() {
		return array( 'total_users' => 1500 );
	}
}

if ( ! function_exists( 'register_rest_route' ) ) {
	function register_rest_route( $namespace, $route, $args = array() ) {
		$GLOBALS['_cb_mock_rest_routes'][ $namespace . $route ] = $args;
		return true;
	}
}

/**
 * Lightweight Test Assertion Framework.
 */
class CBTestAssertionException extends Exception {}

class CBTestCase {
	public int $assertions = 0;
	public string $currentTest = '';

	public function assertTrue( $condition, string $message = 'Failed asserting that condition is true' ): void {
		$this->assertions++;
		if ( ! $condition ) {
			throw new CBTestAssertionException( $message );
		}
	}

	public function assertFalse( $condition, string $message = 'Failed asserting that condition is false' ): void {
		$this->assertions++;
		if ( $condition ) {
			throw new CBTestAssertionException( $message );
		}
	}

	public function assertEquals( $expected, $actual, string $message = '' ): void {
		$this->assertions++;
		if ( $expected !== $actual ) {
			$msg = $message ?: sprintf( "Failed asserting that %s equals expected %s", var_export( $actual, true ), var_export( $expected, true ) );
			throw new CBTestAssertionException( $msg );
		}
	}

	public function assertContains( string $needle, string $haystack, string $message = '' ): void {
		$this->assertions++;
		if ( false === strpos( $haystack, $needle ) ) {
			$msg = $message ?: sprintf( "Failed asserting that string contains '%s'", $needle );
			throw new CBTestAssertionException( $msg );
		}
	}

	public function assertFileExists( string $filename, string $message = '' ): void {
		$this->assertions++;
		if ( ! file_exists( $filename ) ) {
			$msg = $message ?: sprintf( "Failed asserting that file '%s' exists", $filename );
			throw new CBTestAssertionException( $msg );
		}
	}
}
