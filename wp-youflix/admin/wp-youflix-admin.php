<?php

// Add the admin menu page
add_action('admin_menu', 'wp_youflix_add_admin_menu');

function wp_youflix_add_admin_menu() {
    add_options_page(
        'WP-YouFlix Settings',
        'WP-YouFlix',
        'manage_options',
        'wp-youflix',
        'wp_youflix_options_page'
    );
}

function wp_youflix_options_page() {
    ?>
    <div class="wrap">
        <h2>WP-YouFlix</h2>
        <p><?php _e( 'There are currently no settings to configure. Please manage your videos under the "Videos" menu.', 'wp-youflix' ); ?></p>
    </div>
    <?php
}
?>
