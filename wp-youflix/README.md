# WP-YouFlix

A manual video gallery manager for WordPress with a modern, Netflix-style interface.

## Description

WP-YouFlix is a WordPress plugin that allows you to create a beautiful, engaging video gallery with a layout inspired by Netflix. This plugin provides a 'Videos' custom post type and 'Series' taxonomy, giving you full, manual control over the content you display. It's perfect for showcasing a curated video portfolio.

## Features

*   Netflix-inspired design
*   Manually manage videos and series within WordPress
*   Hero section for a featured video
*   Carousels for your video series
*   Simple shortcode for display: `[wp_youflix]`

## How It Works

This plugin creates a "Videos" menu in your WordPress admin dashboard. You can add new videos just like you would add a new blog post. Each video uses its **Post Title** as the video title, its **Featured Image** as the thumbnail, and has a special field for the YouTube Video ID. You can categorize your videos into "Series", which are then used to create the Netflix-style carousels on the frontend.

## Installation

1.  Upload the `wp-youflix` directory to your `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

1.  **Add a Video Series:** Go to **Videos > Series** in your admin dashboard to create your categories (e.g., "Web Design Tutorials", "Case Studies").
2.  **Add a Video:**
    *   Go to **Videos > Add New**.
    *   Enter a **Title** for your video. This will be displayed on the gallery.
    *   Set a **Featured Image**. This will be used as the video's thumbnail in the carousel.
    *   In the "YouTube Video ID" box below the editor, paste the ID of your YouTube video (e.g., for `https://www.youtube.com/watch?v=dQw4w9WgXcQ`, the ID is `dQw4w9WgXcQ`).
    *   On the right-hand side, assign the video to one or more series.
    *   Publish the video.
3.  **Display the Gallery:** Add the following shortcode to any page or post:

    `[wp_youflix]`

For the best experience, use a full-width page template in your theme. The hero section will automatically feature the latest video from your first series.
