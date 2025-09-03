<?php

// Register Custom Post Type and Taxonomy

function wp_youflix_register_content_structures() {

    // Custom Post Type: Videos
    $labels_cpt = [
        'name'                  => _x( 'Videos', 'Post Type General Name', 'wp-youflix' ),
        'singular_name'         => _x( 'Video', 'Post Type Singular Name', 'wp-youflix' ),
        'menu_name'             => __( 'Videos', 'wp-youflix' ),
        'name_admin_bar'        => __( 'Video', 'wp-youflix' ),
        'archives'              => __( 'Video Archives', 'wp-youflix' ),
        'attributes'            => __( 'Video Attributes', 'wp-youflix' ),
        'parent_item_colon'     => __( 'Parent Video:', 'wp-youflix' ),
        'all_items'             => __( 'All Videos', 'wp-youflix' ),
        'add_new_item'          => __( 'Add New Video', 'wp-youflix' ),
        'add_new'               => __( 'Add New', 'wp-youflix' ),
        'new_item'              => __( 'New Video', 'wp-youflix' ),
        'edit_item'             => __( 'Edit Video', 'wp-youflix' ),
        'update_item'           => __( 'Update Video', 'wp-youflix' ),
        'view_item'             => __( 'View Video', 'wp-youflix' ),
        'view_items'            => __( 'View Videos', 'wp-youflix' ),
        'search_items'          => __( 'Search Video', 'wp-youflix' ),
    ];
    $args_cpt = [
        'label'                 => __( 'Video', 'wp-youflix' ),
        'description'           => __( 'A post type for individual YouTube videos.', 'wp-youflix' ),
        'labels'                => $labels_cpt,
        'supports'              => [ 'title', 'editor', 'thumbnail' ],
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-video-alt3',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    ];
    register_post_type( 'wp_youflix_video', $args_cpt );

    // Custom Taxonomy: Series
    $labels_tax = [
        'name'                       => _x( 'Series', 'Taxonomy General Name', 'wp-youflix' ),
        'singular_name'              => _x( 'Series', 'Taxonomy Singular Name', 'wp-youflix' ),
        'menu_name'                  => __( 'Series', 'wp-youflix' ),
        'all_items'                  => __( 'All Series', 'wp-youflix' ),
        'parent_item'                => __( 'Parent Series', 'wp-youflix' ),
        'parent_item_colon'          => __( 'Parent Series:', 'wp-youflix' ),
        'new_item_name'              => __( 'New Series Name', 'wp-youflix' ),
        'add_new_item'               => __( 'Add New Series', 'wp-youflix' ),
        'edit_item'                  => __( 'Edit Series', 'wp-youflix' ),
        'update_item'                => __( 'Update Series', 'wp-youflix' ),
        'view_item'                  => __( 'View Series', 'wp-youflix' ),
        'separate_items_with_commas' => __( 'Separate series with commas', 'wp-youflix' ),
        'add_or_remove_items'        => __( 'Add or remove series', 'wp-youflix' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'wp-youflix' ),
        'popular_items'              => __( 'Popular Series', 'wp-youflix' ),
        'search_items'               => __( 'Search Series', 'wp-youflix' ),
        'not_found'                  => __( 'Not Found', 'wp-youflix' ),
    ];
    $args_tax = [
        'labels'                     => $labels_tax,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    ];
    register_taxonomy( 'wp_youflix_series', [ 'wp_youflix_video' ], $args_tax );

}
add_action( 'init', 'wp_youflix_register_content_structures', 0 );
