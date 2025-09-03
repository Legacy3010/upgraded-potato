<?php

// Add the admin menu page
add_action('admin_menu', 'wp_youflix_add_admin_menu');
// Register the settings
add_action('admin_init', 'wp_youflix_settings_init');

function wp_youflix_add_admin_menu() {
    add_options_page(
        'WP-YouFlix Settings',
        'WP-YouFlix',
        'manage_options',
        'wp-youflix',
        'wp_youflix_options_page'
    );
}

function wp_youflix_settings_init() {
    register_setting('wp_youflix_options', 'wp_youflix_settings');

    add_settings_section(
        'wp_youflix_section',
        __('API Settings', 'wp-youflix'),
        'wp_youflix_section_callback',
        'wp_youflix_options'
    );

    add_settings_field(
        'wp_youflix_api_key',
        __('YouTube API Key', 'wp-youflix'),
        'wp_youflix_api_key_render',
        'wp_youflix_options',
        'wp_youflix_section'
    );

    add_settings_field(
        'wp_youflix_channel_id',
        __('YouTube Channel ID', 'wp-youflix'),
        'wp_youflix_channel_id_render',
        'wp_youflix_options',
        'wp_youflix_section'
    );
}

function wp_youflix_api_key_render() {
    $options = get_option('wp_youflix_settings');
    ?>
    <input type='text' name='wp_youflix_settings[wp_youflix_api_key]' value='<?php echo esc_attr($options['wp_youflix_api_key']); ?>' class='regular-text'>
    <?php
}

function wp_youflix_channel_id_render() {
    $options = get_option('wp_youflix_settings');
    ?>
    <input type='text' name='wp_youflix_settings[wp_youflix_channel_id]' value='<?php echo esc_attr($options['wp_youflix_channel_id']); ?>' class='regular-text'>
    <?php
}

function wp_youflix_section_callback() {
    echo __('Enter your YouTube API Key and Channel ID below.', 'wp-youflix');
}

function wp_youflix_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>WP-YouFlix</h2>
        <?php
        settings_fields('wp_youflix_options');
        do_settings_sections('wp_youflix_options');
        submit_button();
        ?>
    </form>
    <?php
}
?>
