<?php
/*
	tab output on settings page
*/

/**
 * Declare the Namespace.
 *
 * @since 1.0.0
 */
namespace azurecurve\AddOpenGraphTags;

/**
 * Prevent direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Register admin scripts.
 *
 * @since 2.0.0
 */
function register_admin_scripts() {
	wp_register_script( PLUGIN_HYPHEN . '-admin-js', esc_url_raw( plugins_url( '../assets/js/admin.js', __FILE__ ) ), array(), '1.0.0', true );
	wp_register_script( 'azrcrv-admin-standard-js', esc_url_raw( plugins_url( '../assets/js/admin-standard.js', __FILE__ ) ), array(), '26.6.8', true );
}

/**
 * Enqueue admin styles.
 *
 * @since 2.0.0
 */
function enqueue_admin_scripts() {
	global $pagenow;

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && ( $_GET['page'] == PLUGIN_HYPHEN || $_GET['page'] == 'azrcrv-plugin-menu' ) ) {
		wp_enqueue_script( PLUGIN_HYPHEN . '-admin-js' );
		wp_enqueue_script( 'azrcrv-admin-standard-js' );
	}
}

/**
 * Media Uploader.
 *
 * @since 1.0.0
 */
function media_uploader() {

	if ( isset( $_GET['page'] ) && $_GET['page'] == PLUGIN_HYPHEN ) {
		if ( function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
			wp_dequeue_script( 'jquery-ui-core' );
			wp_dequeue_script( 'jquery-ui-mouse' );
			wp_dequeue_script( 'jquery-ui-sortable' );
		} else {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );
		}
	}
}