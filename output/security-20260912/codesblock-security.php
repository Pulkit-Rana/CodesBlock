<?php
/** Plugin Name: CodesBlock Production Security */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Retain incident evidence without allowing the confirmed intruder to sign in.
add_filter( 'authenticate', function ( $user ) {
    if ( $user instanceof WP_User && in_array( (int) $user->ID, array_map( 'intval', (array) get_option( 'cbsecurity_blocked_users', array() ) ), true ) ) {
        return new WP_Error( 'authentication_failed', 'Authentication failed.' );
    }
    return $user;
}, 100, 1 );
add_filter( 'determine_current_user', function ( $user_id ) {
    return in_array( (int) $user_id, array_map( 'intval', (array) get_option( 'cbsecurity_blocked_users', array() ) ), true ) ? 0 : $user_id;
}, 100 );

// The member-modal limits alone do not cover wp-login.php or XML-RPC.
add_filter( 'authenticate', function ( $user, $username, $password ) {
    if ( '' === (string) $username || '' === (string) $password || ! function_exists( 'cbcommerce_rate_limit_reached' ) ) {
        return $user;
    }
    $identity = strtolower( trim( (string) $username ) );
    $account = is_email( $identity ) ? get_user_by( 'email', $identity ) : get_user_by( 'login', $identity );
    if ( $account ) { $identity = 'user-' . $account->ID; }
    $limited_ip = cbcommerce_rate_limit_reached( 'native-login-ip', '', 30, 15 * MINUTE_IN_SECONDS );
    $limited_account = cbcommerce_rate_limit_reached( 'native-login-account', $identity, 15, 15 * MINUTE_IN_SECONDS );
    return $limited_ip || $limited_account ? new WP_Error( 'authentication_rate_limited', 'Too many login attempts. Please try again in 15 minutes.' ) : $user;
}, 5, 3 );

add_filter( 'xmlrpc_methods', function ( $methods ) {
    unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
    return $methods;
} );

add_action( 'send_headers', function () {
    if ( is_ssl() && ! headers_sent() ) { header( 'Strict-Transport-Security: max-age=31536000' ); }
}, 100 );
