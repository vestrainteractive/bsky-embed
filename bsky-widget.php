<?php
/*
Plugin Name: Bluesky Embed Widget
Description: A widget to embed Bluesky posts with customizable settings.
Version: 1.0
Author: Vestra Interactive
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

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
        ?>
        <bsky-embed username="<?php echo $username; ?>" mode="light" limit="<?php echo $limit; ?>"></bsky-embed>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                fetch('<?php echo plugin_dir_url(__FILE__); ?>truncate.js')
                .then(response => response.text())
                .then(script => eval(script));
            });
        </script>
        <?php
        echo $args['after_widget'];
    }

    public function form($instance) {
        $username = !empty($instance['username']) ? esc_attr($instance['username']) : '';
        $limit = !empty($instance['limit']) ? intval($instance['limit']) : 3;
        $height = !empty($instance['height']) ? intval($instance['height']) : 120;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('username')); ?>"><?php _e('Bluesky Username:', 'twowheelsin'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('username')); ?>" name="<?php echo esc_attr($this->get_field_name('username')); ?>" type="text" value="<?php echo esc_attr($username); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php _e('Number of Posts:', 'twowheelsin'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="1">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('height')); ?>"><?php _e('Default Height:', 'twowheelsin'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('height')); ?>" name="<?php echo esc_attr($this->get_field_name('height')); ?>" type="number" value="<?php echo esc_attr($height); ?>" min="50">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['username'] = (!empty($new_instance['username'])) ? sanitize_text_field($new_instance['username']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? intval($new_instance['limit']) : 3;
        $instance['height'] = (!empty($new_instance['height'])) ? intval($new_instance['height']) : 120;
        return $instance;
    }
}

function register_bluesky_embed_widget() {
    register_widget('Bluesky_Embed_Widget');
}
add_action('widgets_init', 'register_bluesky_embed_widget');

// Enqueue scripts
function bluesky_enqueue_scripts() {
    wp_enqueue_script('bluesky-truncate', plugin_dir_url(__FILE__) . 'truncate.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'bluesky_enqueue_scripts');

function twi_enqueue_bsky_embed_script() {
    wp_enqueue_script(
        'bsky-embed',
        'https://cdn.jsdelivr.net/npm/bsky-embed/dist/bsky-embed.es.js',
        array(),
        null,
        true // Load in footer
    );
}
add_action('wp_enqueue_scripts', 'twi_enqueue_bsky_embed_script');

?>
