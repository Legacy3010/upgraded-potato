<?php

// Custom Meta Box for Video ID

/**
 * Register the meta box.
 */
function wp_youflix_add_video_id_meta_box() {
    add_meta_box(
        'wp_youflix_video_id',
        __( 'YouTube Video ID', 'wp-youflix' ),
        'wp_youflix_render_video_id_meta_box',
        'wp_youflix_video',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wp_youflix_add_video_id_meta_box' );

/**
 * Render the meta box HTML.
 *
 * @param WP_Post $post The post object.
 */
function wp_youflix_render_video_id_meta_box( $post ) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'wp_youflix_save_video_id', 'wp_youflix_video_id_nonce' );

    $video_id = get_post_meta( $post->ID, '_wp_youflix_video_id', true );

    echo '<label for="wp_youflix_video_id_field">';
    _e( 'Enter the YouTube Video ID (e.g., dQw4w9WgXcQ)', 'wp-youflix' );
    echo '</label> ';
    echo '<input type="text" id="wp_youflix_video_id_field" name="wp_youflix_video_id_field" value="' . esc_attr( $video_id ) . '" size="25" />';
}

/**
 * Save the meta box data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function wp_youflix_save_video_id_meta_data( $post_id ) {

    // Check if our nonce is set.
    if ( ! isset( $_POST['wp_youflix_video_id_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['wp_youflix_video_id_nonce'], 'wp_youflix_save_video_id' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( isset( $_POST['post_type'] ) && 'wp_youflix_video' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    // Make sure that the field is set.
    if ( ! isset( $_POST['wp_youflix_video_id_field'] ) ) {
        return;
    }

    // Sanitize user input.
    $my_data = sanitize_text_field( $_POST['wp_youflix_video_id_field'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_wp_youflix_video_id', $my_data );
}
add_action( 'save_post', 'wp_youflix_save_video_id_meta_data' );
?>
