<?php
/*
Plugin Name: Bluesky Embed Widget
Description: A widget and shortcode to embed Bluesky posts with customization (height, mode, limit).
Version: 1.4
Author: Vestra Interactive
*/

if (!defined('ABSPATH')) exit;

// Register the widget
class Bluesky_Embed_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'bluesky_embed_widget',
            __('Bluesky Embed Widget', 'twowheelsin'),
            array('description' => __('Embed Bluesky posts with customization.', 'twowheelsin'))
        );
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
add_action('widgets_init', function () {
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

    $unique_id = 'bsky-' . uniqid();

    return "
    <div id='{$unique_id}' class='bsky-container' data-height='" . intval($atts['height']) . "'>
        <bsky-embed 
            username='" . esc_attr($atts['username']) . "' 
            mode='" . esc_attr($atts['mode']) . "' 
            limit='" . esc_attr($atts['limit']) . "'>
        </bsky-embed>
    </div>";
}
add_shortcode('bsky_embed', 'bluesky_embed_shortcode');

// Check for shortcode in posts and enqueue scripts only on frontend when needed
function bluesky_check_shortcode_usage($posts) {
    if (empty($posts) || is_admin()) return $posts;

    $found = false;
    foreach ($posts as $post) {
        if (has_shortcode($post->post_content, 'bsky_embed')) {
            $found = true;
            break;
        }
    }

    if ($found) {
        add_action('wp_enqueue_scripts', 'bluesky_enqueue_scripts', 20);
    }

    return $posts;
}
add_filter('the_posts', 'bluesky_check_shortcode_usage');

// Enqueue JS only if shortcode is detected
function bluesky_enqueue_scripts() {
    wp_enqueue_script(
        'bsky-embed',
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
