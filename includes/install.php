<?php
add_action( 'admin_menu', 'my_admin_menu' );

function my_admin_menu() {
	add_menu_page( 
        'Last Page Redirect', 
        'Last Page Redirect', 
        'manage_options', 
        'last-page-redirect',
        'last_page_redirect_page', 
        'dashicons-undo', 
        null
    );
}

function last_page_redirect_page() {
    require_once( 'AdminPage.php' );

    $admin_page = new LastPageRedirect\AdminPage();
    $admin_page->render();
}

function last_page_redirect_install() {
    global $wpdb;

    if ( $wpdb->get_results( "SELECT 1 FROM `{$wpdb->prefix}last_page_redirect` LIMIT 1;" ) ) {
        return false;
    }

    $wpdb->query(
        "CREATE TABLE `{$wpdb->prefix}last_page_redirect` ( 
            `id` INT NOT NULL AUTO_INCREMENT , 
            `referal_url` VARCHAR(500) NOT NULL , 
            `operator` VARCHAR(45) NOT NULL , 
            `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`));"
    );
}

last_page_redirect_install();

if ( is_admin() ) {
    wp_enqueue_script('undescore', LAST_PAGE_REDIRECT_URL . 'admin/js/underscore.min.js' );
}
