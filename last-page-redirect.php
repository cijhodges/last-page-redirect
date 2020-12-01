<?php
/*
    * Plugin Name: Last Page Redirect
    * Plugin URI: https://github.com/cijhodges/last-page-redirect
    * Description: Redirect users to the last page of their session based on a referal URL.
    * Version: 1.0.6
    * Author: Compassion Web & Interactive
    * Author URI: https://www.compassion.com/
*/

define( 'LAST_PAGE_REDIRECT_PATH', plugin_dir_path( __FILE__ ) );
define( 'LAST_PAGE_REDIRECT_URL', plugin_dir_url( __FILE__ ) );
require_once LAST_PAGE_REDIRECT_PATH . 'includes/load.php';
require_once LAST_PAGE_REDIRECT_PATH . 'includes/install.php';

function init_last_page_redirect() {
    new LastPageRedirect\Checker();
}

if ( is_admin() ) {
    $updater = new LastPageRedirectUpdater( __FILE__ ); // instantiate our class
    $updater->set_username( 'cijhodges' ); // set username
    $updater->set_repository( 'last-page-redirect' ); // set repo
    $updater->initialize();
} elseif ( $GLOBALS['pagenow'] !== 'wp-login.php' ) {
    add_action( 'template_redirect', 'init_last_page_redirect' );
}
