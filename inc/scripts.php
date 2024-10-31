<?php
/**
 * Enqueue a script in the WordPress admin on settings.
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function sendmsgs_enqueue_admin_script( $hook ) {
    if ( 'settings_page_sendmsg-api' != $hook ) {
        return;
    }
    /* Admin Style */
    wp_enqueue_style( 'sendmsg-admin-style', plugin_dir_url( __FILE__ ) . '../assets/css/admin-style.css', array(), SENDMSGS_VERSION );

    /* Admin Scripts */
    wp_enqueue_script( 'sendmsg-admin-script', plugin_dir_url( __FILE__ ) . '../assets/js/admin-script.js', array('jquery'), SENDMSGS_VERSION );

	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'sendmsg-admin-script',
		'sendmsg_ajax_obj',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('sendmsg_ajax_nonce')
		)
	);

}
add_action( 'admin_enqueue_scripts', 'sendmsgs_enqueue_admin_script' );