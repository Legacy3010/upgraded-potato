<?php

// Add the admin menu page
add_action('admin_menu', 'wp_youflix_add_admin_menu');
// Register the settings
add_action('admin_init', 'wp_youflix_settings_init');
// Handle cache clearing
add_action('admin_init', 'wp_youflix_clear_cache_handler');

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
        'wp_youflix_api_section',
        __('API Settings', 'wp-youflix'),
        null,
        'wp_youflix_options'
    );

    add_settings_field(
        'wp_youflix_api_key',
        __('YouTube API Key', 'wp-youflix'),
        'wp_youflix_api_key_render',
        'wp_youflix_options',
        'wp_youflix_api_section'
    );

    add_settings_field(
        'wp_youflix_channel_id',
        __('YouTube Channel ID', 'wp-youflix'),
        'wp_youflix_channel_id_render',
        'wp_youflix_options',
        'wp_youflix_api_section'
    );

    add_settings_section(
        'wp_youflix_cache_section',
        __('Cache Settings', 'wp-youflix'),
        null,
        'wp_youflix_options'
    );

    add_settings_field(
        'wp_youflix_cache_time',
        __('Cache Expiration (hours)', 'wp-youflix'),
        'wp_youflix_cache_time_render',
        'wp_youflix_options',
        'wp_youflix_cache_section'
    );
}

function wp_youflix_api_key_render() {
    $options = get_option('wp_youflix_settings');
    $api_key = $options['wp_youflix_api_key'] ?? '';
    ?>
    <input type='text' name='wp_youflix_settings[wp_youflix_api_key]' value='<?php echo esc_attr($api_key); ?>' class='regular-text'>
    <?php
}

function wp_youflix_channel_id_render() {
    $options = get_option('wp_youflix_settings');
    $channel_id = $options['wp_youflix_channel_id'] ?? '';
    ?>
    <input type='text' name='wp_youflix_settings[wp_youflix_channel_id]' value='<?php echo esc_attr($channel_id); ?>' class='regular-text'>
    <?php
}

function wp_youflix_cache_time_render() {
    $options = get_option('wp_youflix_settings');
    $cache_time = $options['wp_youflix_cache_time'] ?? 3;
    ?>
    <input type='number' name='wp_youflix_settings[wp_youflix_cache_time]' value='<?php echo esc_attr($cache_time); ?>' min='1' class='small-text'>
    <p class="description"><?php _e('How long to cache the YouTube API results.', 'wp-youflix'); ?></p>
    <?php
}

function wp_youflix_clear_cache_handler() {
    if (isset($_GET['action']) && $_GET['action'] === 'wp_youflix_clear_cache') {
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'wp_youflix_clear_cache_nonce')) {
            wp_die('Invalid nonce.');
        }

        $options = get_option('wp_youflix_settings');
        $channel_id = $options['wp_youflix_channel_id'] ?? '';

        if (!empty($channel_id)) {
            // First, delete the playlists transient
            delete_transient('wp_youflix_playlists_' . $channel_id);

            // To delete the playlist items transients, we need the playlist IDs.
            // We force a new fetch of the playlists since we just cleared the cache.
            $playlists = wp_youflix_get_playlists();

            if (!is_wp_error($playlists) && !empty($playlists)) {
                foreach ($playlists as $playlist) {
                    delete_transient('wp_youflix_playlist_items_' . $playlist['id']);
                }
            }
        }

        wp_redirect(admin_url('options-general.php?page=wp-youflix&cache_cleared=true'));
        exit;
    }
}

function wp_youflix_options_page() {
    ?>
    <div class="wrap">
        <h2>WP-YouFlix</h2>
        <?php if (isset($_GET['cache_cleared']) && $_GET['cache_cleared'] === 'true') : ?>
            <div id="message" class="updated notice is-dismissible">
                <p><?php _e('Plugin cache has been cleared.', 'wp-youflix'); ?></p>
            </div>
        <?php endif; ?>
        <form action='options.php' method='post'>
            <?php
            settings_fields('wp_youflix_options');
            do_settings_sections('wp_youflix_options');
            submit_button();
            ?>
        </form>
        <form method="get">
            <input type="hidden" name="page" value="wp-youflix">
            <input type="hidden" name="action" value="wp_youflix_clear_cache">
            <?php wp_nonce_field('wp_youflix_clear_cache_nonce'); ?>
            <?php submit_button(__('Clear Plugin Cache', 'wp-youflix'), 'delete', 'clear-cache-submit', false); ?>
        </form>
    </div>
    <?php
}
?>
