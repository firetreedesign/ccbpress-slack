<?php
function ccbpress_slack_admin_scripts() {
    wp_enqueue_script( 'ccbpress-slack-admin', CCBPRESS_SLACK_PLUGIN_URL . 'assets/js/admin.js' );
}
add_action( 'admin_enqueue_scripts', 'ccbpress_slack_admin_scripts' );
