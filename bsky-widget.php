<?php
/*
Plugin Name: Bluesky Embed Widget
Description: A widget and shortcode to embed Bluesky posts with customization (height, mode, limit).
Version: 1.5
Author: Vestra Interactive
*/

if (!defined('ABSPATH')) exit;

// Shortcode
function bluesky_embed_shortcode($atts) {
    $atts = shortcode_atts([
        'username' => 'twowheelsin.com',
        'limit'    => 3,
        'mode'     => 'light',
    ], $atts);

    $username = esc_attr($atts['username']);
    $limit    = intval($atts['limit']);
    $mode     = esc_attr($atts['mode']);

    return "<bsky-embed username='$username' limit='$limit' mode='$mode'></bsky-embed>";
}
add_shortcode('bsky_embed', 'bluesky_embed_shortcode');

// Check if shortcode exists in post and enqueue on frontend only
add_filter('the_posts', function ($posts) {
    if (is_admin() || empty($posts)) return $posts;

    foreach ($posts as $post) {
        if (has_shortcode($post->post_content, 'bsky_embed')) {
            add_action('wp_enqueue_scripts', 'enqueue_bsky_embed_scripts');
            break;
        }
    }

    return $posts;
});

// Enqueue scripts only if shortcode used
function enqueue_bsky_embed_scripts() {
    wp_enqueue_script(
        'bsky-embed-lib',
        'https://cdn.jsdelivr.net/npm/bsky-embed/dist/bsky-embed.es.js',
        [],
        null,
        true
    );

    wp_enqueue_script(
        'bsky-truncate',
        plugin_dir_url(__FILE__) . 'truncate.js',
        [],
        null,
        true
    );
}
