<?php
/*
Plugin Name: Bluesky Embed Widget
Description: A widget and shortcode to embed Bluesky posts with customization (height, mode, limit).
Version: 1.1
Author: Vestra Interactive
*/

if (!defined('ABSPATH')) exit;

// Register the widget
class Bluesky_Embed_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('bluesky_embed_widget', __('Bluesky Embed Widget', 'twowheelsin'), array('description' => __('Embed Bluesky posts with customization.', 'twowheelsin')));
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        $username = !empty($instance['username']) ? esc_attr($instance['username']) : 'twowheelsin.com';
        $limit = !empty($instance['limit']) ? intval($instance['limit']) : 3;
        $height = !empty($instance['height']) ? intval($instance['height']) : 120;
        $mode = !empty($instance['mode']) && $instance['mode'] === 'dark' ? 'dark' : 'light';

        echo do_shortcode("[bsky_embed username='$username' limit='$limit' height='$height' mode='$mode']");
        echo $args['after_widget'];
    }

    public function form($instance) {
        $username = !empty($instance['username']) ? esc_attr($instance['username']) : '';
        $limit = !empty($instance['limit']) ? intval($instance['limit']) : 3;
        $height = !empty($instance['height']) ? intval($instance['height']) : 120;
        $mode = !empty($instance['mode']) ? esc_attr($instance['mode']) : 'light';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('username'); ?>">Bluesky Username:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>">Number of Posts:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo $limit; ?>" min="1">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>">Truncate Height (px):</label>
            <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="number" value="<?php echo $height; ?>" min="50">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('mode'); ?>">Theme Mode:</label>
            <select class="widefat" id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>">
                <option value="light" <?php selected($mode, 'light'); ?>>Light</option>
                <option value="dark" <?php selected($mode, 'dark'); ?>>Dark</option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        return [
            'username' => sanitize_text_field($new_instance['username']),
            'limit' => intval($new_instance['limit']),
            'height' => intval($new_instance['height']),
            'mode' => in_array($new_instance['mode'], ['light', 'dark']) ? $new_instance['mode'] : 'light',
        ];
    }
}

// Register the widget
add_action('widgets_init', function() {
    register_widget('Bluesky_Embed_Widget');
});

// Shortcode handler
function bluesky_embed_shortcode($atts) {
    $atts = shortcode_atts([
        'username' => 'twowheelsin.com',
        'limit' => 3,
        'height' => 120,
        'mode' => 'light'
    ], $atts);

    ob_start(); ?>
    <bsky-embed username="<?php echo esc_attr($atts['username']); ?>" mode="<?php echo esc_attr($atts['mode']); ?>" limit="<?php echo esc_attr($atts['limit']); ?>"></bsky-embed>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch('<?php echo plugin_dir_url(__FILE__); ?>truncate.js')
                .then(r => r.text())
                .then(script => {
                    const height = <?php echo intval($atts['height']); ?>;
                    eval(script.replace(/120px/g, height + 'px'));
                });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('bsky_embed', 'bluesky_embed_shortcode');

// Enqueue core Bluesky script
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'bsky-embed',
        'https://cdn.jsdelivr.net/npm/bsky-embed/dist/bsky-embed.es.js',
        [],
        null,
        true
    );
});
