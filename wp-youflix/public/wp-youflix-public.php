<?php

// Public-facing functionality for WP-YouFlix

/**
 * Enqueue scripts and styles.
 */
function wp_youflix_enqueue_scripts() {
    if ( is_singular() && has_shortcode( get_the_content(), 'wp_youflix' ) ) {
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
}
add_action('wp_enqueue_scripts', 'wp_youflix_enqueue_scripts');

/**
 * Render the frontend display.
 */
function wp_youflix_render_frontend() {
    ob_start();

    $series_terms = get_terms( [
        'taxonomy'   => 'wp_youflix_series',
        'hide_empty' => true,
    ] );

    if ( is_wp_error( $series_terms ) || empty( $series_terms ) ) {
        echo '<p>' . __( 'No video series found.', 'wp-youflix' ) . '</p>';
        return ob_get_clean();
    }

    $first_video_post = null;

    // Find the first video for the hero section
    if ( ! empty( $series_terms ) ) {
        $first_series_args = [
            'post_type'      => 'wp_youflix_video',
            'posts_per_page' => 1,
            'tax_query'      => [
                [
                    'taxonomy' => 'wp_youflix_series',
                    'field'    => 'term_id',
                    'terms'    => $series_terms[0]->term_id,
                ],
            ],
        ];
        $first_video_query = new WP_Query( $first_series_args );
        if ( $first_video_query->have_posts() ) {
            $first_video_post = $first_video_query->posts[0];
        }
        wp_reset_postdata();
    }

    // Render Hero Section
    if ( $first_video_post ) {
        $hero_video_id = get_post_meta( $first_video_post->ID, '_wp_youflix_video_id', true );
        $hero_title = get_the_title( $first_video_post->ID );
        ?>
        <div class="wp-youflix-hero">
            <div class="wp-youflix-hero-video">
                <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?php echo esc_attr($hero_video_id); ?>?autoplay=1&mute=1&controls=0&showinfo=0&rel=0&loop=1&playlist=<?php echo esc_attr($hero_video_id); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="wp-youflix-hero-content">
                <h2><?php echo esc_html($hero_title); ?></h2>
            </div>
        </div>
        <?php
    }

    echo '<div class="wp-youflix-container">';

    foreach ( $series_terms as $term ) {
        $args = [
            'post_type'      => 'wp_youflix_video',
            'posts_per_page' => 50,
            'tax_query'      => [
                [
                    'taxonomy' => 'wp_youflix_series',
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ],
            ],
        ];
        $videos_query = new WP_Query( $args );

        if ( $videos_query->have_posts() ) :
            ?>
            <div class="wp-youflix-playlist-row">
                <h3><?php echo esc_html( $term->name ); ?></h3>
                <div class="wp-youflix-carousel-container">
                    <button class="wp-youflix-carousel-prev">&lt;</button>
                    <div class="wp-youflix-video-carousel">
                        <?php while ( $videos_query->have_posts() ) : $videos_query->the_post();
                            $video_id = get_post_meta( get_the_ID(), '_wp_youflix_video_id', true );
                            $thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                            $title = get_the_title();
                            if ( ! $thumbnail_url ) {
                                // Use a fallback image if no featured image is set
                                $thumbnail_url = 'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg';
                            }
                            ?>
                            <a href="#" class="wp-youflix-video-thumb" data-video-id="<?php echo esc_attr( $video_id ); ?>">
                                <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $title ); ?>">
                            </a>
                        <?php endwhile; ?>
                    </div>
                    <button class="wp-youflix-carousel-next">&gt;</button>
                </div>
            </div>
            <?php
        endif;
        wp_reset_postdata();
    }

    echo '</div>'; // .wp-youflix-container

    // Modal HTML remains the same
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


/**
 * Register the shortcode.
 */
function wp_youflix_shortcode_wrapper($atts) {
    return wp_youflix_render_frontend();
}
add_shortcode('wp_youflix', 'wp_youflix_shortcode_wrapper');
?>
