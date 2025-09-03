<?php

// Public-facing functionality for WP-YouFlix

/**
 * Get playlists from the YouTube channel.
 */
function wp_youflix_get_playlists() {
    $options = get_option('wp_youflix_settings');
    $api_key = $options['wp_youflix_api_key'] ?? '';
    $channel_id = $options['wp_youflix_channel_id'] ?? '';
    $cache_time = $options['wp_youflix_cache_time'] ?? 3; // Default to 3 hours
    $expiration = $cache_time * HOUR_IN_SECONDS;

    if (empty($api_key) || empty($channel_id)) {
        return new WP_Error('missing_credentials', __('API Key or Channel ID is missing.', 'wp-youflix'));
    }

    $transient_key = 'wp_youflix_playlists_' . $channel_id;
    $cached_playlists = get_transient($transient_key);

    if (false !== $cached_playlists) {
        return $cached_playlists;
    }

    $url = sprintf(
        'https://www.googleapis.com/youtube/v3/playlists?part=snippet,contentDetails&channelId=%s&maxResults=50&key=%s',
        $channel_id,
        $api_key
    );

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['error'])) {
        return new WP_Error('youtube_api_error', $data['error']['message']);
    }

    set_transient($transient_key, $data['items'], $expiration);

    return $data['items'];
}

/**
 * Get videos from a specific playlist.
 *
 * @param string $playlist_id The ID of the playlist.
 */
function wp_youflix_get_playlist_items($playlist_id) {
    $options = get_option('wp_youflix_settings');
    $api_key = $options['wp_youflix_api_key'] ?? '';
    $cache_time = $options['wp_youflix_cache_time'] ?? 3; // Default to 3 hours
    $expiration = $cache_time * HOUR_IN_SECONDS;

    if (empty($api_key)) {
        return new WP_Error('missing_api_key', __('API Key is missing.', 'wp-youflix'));
    }

    $transient_key = 'wp_youflix_playlist_items_' . $playlist_id;
    $cached_items = get_transient($transient_key);

    if (false !== $cached_items) {
        return $cached_items;
    }

    $url = sprintf(
        'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&playlistId=%s&maxResults=50&key=%s',
        $playlist_id,
        $api_key
    );

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['error'])) {
        return new WP_Error('youtube_api_error', $data['error']['message']);
    }

    set_transient($transient_key, $data['items'], $expiration);

    return $data['items'];
}

/**
 * Enqueue scripts and styles.
 */
function wp_youflix_enqueue_scripts() {
    wp_enqueue_style(
        'wp-youflix-style',
        plugin_dir_url(dirname(__DIR__)) . 'assets/css/wp-youflix.css',
        [],
        WP_YOUFLIX_VERSION
    );

    wp_enqueue_script(
        'wp-youflix-script',
        plugin_dir_url(dirname(__DIR__)) . 'assets/js/wp-youflix.js',
        ['jquery'],
        WP_YOUFLIX_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'wp_youflix_enqueue_scripts');

/**
 * Register the shortcode.
 */
function wp_youflix_shortcode($atts) {
    $atts = shortcode_atts(
        [
            'playlists' => '',
        ],
        $atts,
        'wp_youflix'
    );

    ob_start();

    $all_playlists = wp_youflix_get_playlists();

    if (is_wp_error($all_playlists)) {
        echo esc_html($all_playlists->get_error_message());
        return ob_get_clean();
    }

    $playlists_to_show = [];
    if (!empty($atts['playlists'])) {
        $playlist_ids = array_map('trim', explode(',', $atts['playlists']));
        $playlist_map = [];
        foreach ($all_playlists as $playlist) {
            $playlist_map[$playlist['id']] = $playlist;
        }
        foreach ($playlist_ids as $id) {
            if (isset($playlist_map[$id])) {
                $playlists_to_show[] = $playlist_map[$id];
            }
        }
    } else {
        $playlists_to_show = $all_playlists;
    }

    $playlists = $playlists_to_show;

    if (is_wp_error($playlists)) {
        echo esc_html($playlists->get_error_message());
        return ob_get_clean();
    }

    if (empty($playlists)) {
        echo '<p>' . __('No playlists found.', 'wp-youflix') . '</p>';
        return ob_get_clean();
    }

    // For the hero section, let's take the first video of the first playlist.
    if (!empty($playlists)) {
        $first_playlist_id = $playlists[0]['id'];
        $first_playlist_videos = wp_youflix_get_playlist_items($first_playlist_id);

        if (!is_wp_error($first_playlist_videos) && !empty($first_playlist_videos)) {
            $hero_video = $first_playlist_videos[0];
            $hero_video_id = $hero_video['snippet']['resourceId']['videoId'];
            $hero_title = $hero_video['snippet']['title'];
            $hero_description = $hero_video['snippet']['description'];
            $truncated_description = strlen($hero_description) > 200 ? substr($hero_description, 0, 200) . '...' : $hero_description;
            ?>
            <div class="wp-youflix-hero">
                <div class="wp-youflix-hero-video">
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?php echo esc_attr($hero_video_id); ?>?autoplay=1&mute=1&controls=0&showinfo=0&rel=0&loop=1&playlist=<?php echo esc_attr($hero_video_id); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <div class="wp-youflix-hero-content">
                    <h2><?php echo esc_html($hero_title); ?></h2>
                    <p><?php echo esc_html($truncated_description); ?></p>
                </div>
            </div>
            <?php
        }
    }

    echo '<div class="wp-youflix-container">';

    foreach ($playlists as $playlist) {
        $playlist_id = $playlist['id'];
        $playlist_title = $playlist['snippet']['title'];
        $videos = wp_youflix_get_playlist_items($playlist_id);

        if (is_wp_error($videos) || empty($videos)) {
            continue;
        }
        ?>
        <div class="wp-youflix-playlist-row">
            <h3><?php echo esc_html($playlist_title); ?></h3>
            <div class="wp-youflix-carousel-container">
                <button class="wp-youflix-carousel-prev">&lt;</button>
                <div class="wp-youflix-video-carousel">
                    <?php foreach ($videos as $video) :
                        $video_id = $video['snippet']['resourceId']['videoId'];
                    $thumbnail_url = $video['snippet']['thumbnails']['high']['url'];
                    ?>
                    <a href="#" class="wp-youflix-video-thumb" data-video-id="<?php echo esc_attr($video_id); ?>">
                        <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($video['snippet']['title']); ?>">
                    </a>
                <?php endforeach; ?>
                </div>
                <button class="wp-youflix-carousel-next">&gt;</button>
            </div>
        </div>
        <?php
    }

    echo '</div>'; // .wp-youflix-container

    ?>
    <div id="wp-youflix-modal" class="wp-youflix-modal">
        <div class="wp-youflix-modal-content">
            <span class="wp-youflix-modal-close">&times;</span>
            <div class="wp-youflix-modal-video-wrap">
                <iframe width="560" height="315" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode('wp_youflix', 'wp_youflix_shortcode');
?>
