<?php

/*
Plugin Name: WP Fusion - Auto Retry
Description: Sets a cron task to retry outgoing API calls after 5 minutes in cases of an API timeout
Plugin URI: https://wpfusion.com/
Version: 1.2
Author: Very Good Plugins
Author URI: https://verygoodplugins.com/
*/


/**
 * Detect a failed API call and schedule a retry
 *
 * @access public
 * @return void
 */

function wpf_schedule_retry( $method, $args, $cid, $result ) {

	$methods = array(
		'apply_tags',
		'remove_tags',
		'update_contact',
	);

	if ( ! in_array( $method, $methods ) ) {
		return; // Only allow on these methods.
	}

	$messages = array(
		'cURL error 28',
		'Internal Server Error',
		'503',
	);

	foreach ( $messages as $message ) {

		if ( false !== strpos( $result->get_error_message(), $message ) ) {

			$user_id = wp_fusion()->user->get_user_id( $cid );

			wpf_log( 'notice', $user_id, 'Scheduling retry for ' . date( get_option( 'date_format' ) . ' H:i:s', current_time( 'timestamp' ) + 300 ), array( 'source' => 'auto-retry' ) );

			wp_schedule_single_event( time() + 300, 'wpf_auto_retry', array( $method, $args, $cid ) );

			return;
		}
	}

}

add_action( 'wpf_api_error', 'wpf_schedule_retry', 10, 4 );


/**
 * Handle the retry
 *
 * @access public
 * @return void
 */

function wpf_do_retry( $method, $args, $cid ) {

	$user_id = wp_fusion()->user->get_user_id( $cid );

	wpf_log( 'notice', $user_id, 'Doing scheduled retry for method <code>' . $method . '</code>', array( 'source' => 'auto-retry' ) );

	// We'll only retry once
	remove_action( 'wpf_api_error', 'wpf_schedule_retry', 10, 4 );

	$result = call_user_func_array( array( wp_fusion()->crm, $method ), $args );

	if ( is_wp_error( $result ) ) {

		wpf_log( 'error', $user_id, 'Scheduled retry also failed with method <code>' . $method . '</code>: ' . $result->get_error_message(), array( 'source' => 'auto-retry', 'args' => $args ) );

	} else {

		wpf_log( 'notice', $user_id, 'Scheduled retry for <code>' . $method . '</code> succeeded!', array( 'source' => 'auto-retry' ) );

	}

}

add_action( 'wpf_auto_retry', 'wpf_do_retry', 10, 3 );
