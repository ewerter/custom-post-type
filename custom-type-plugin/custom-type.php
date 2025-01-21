<?php
/*
Plugin Name: Custom Type Plugin
Plugin URI: https://github.com
Description: Plugin to create a custom post type and taxonomy.
Version: 1.0
Author: Ewerton Barbosa
Author URI: https://github.com/ewerter
License: GPL2
*/

add_action('init', 'my_custom_post_type');

function my_custom_post_type() {
    register_post_type('park_post_type',
        array(
            'labels' => array(
                'name' => __('Parks'),
                'singular_name' => __('Park'),
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'parks'),
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'custom-fields'),
            'show_in_rest' => true,
        )
    );
}

// Registering custom fields
add_action('init', 'custom_post_type_fields');
function custom_post_type_fields() {
     
     register_post_meta('park_post_type', 'name', [
        'type' => 'string',
        'description' => 'The name of the park',
        'single' => true,
        'show_in_rest' => true,
    ]);

   
    register_post_meta('park_post_type', 'location', [
        'type' => 'string',
        'description' => 'Location of the Park',
        'single' => true,
        'show_in_rest' => true,
    ]);


     register_post_meta('park_post_type', 'hours', [
        'type' => 'string',
        'description' => 'Hours of operation',
        'single' => true,
        'show_in_rest' => true,
    ]);

    
    register_post_meta('park_post_type', 'short_description', [
        'type' => 'string',
        'description' => 'Description of the park',
        'single' => true,
        'show_in_rest' => true,
    ]);
}

//enqueing the script
function enqueue_custom_type_script() {
    wp_enqueue_script(
        'enqueue_custom_type_script',
        plugins_url('custom-type-plugin.js', __FILE__), 
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'], // Gutenberg dependencies
        '1.0.0',
        true
    );
}
add_action('enqueue_block_editor_assets', 'enqueue_custom_type_script');

function park_custom_meta_boxes() {
    add_meta_box(
        'park_meta_box',          // Unique ID for the meta box
        'Park Details',           // Meta box title
        'park_meta_box_callback', // Callback function to display fields
        'park_post_type',         // Post type where the meta box appears
        'normal',                 // Context (normal, side, or advanced)
        'high'                    // Priority
    );
}
add_action('add_meta_boxes', 'park_custom_meta_boxes');

function park_meta_box_callback($post) {
    // Add a nonce field for security
    wp_nonce_field('park_save_meta_box_data', 'park_meta_box_nonce');

    // Get existing values
    $name = get_post_meta($post->ID, 'name', true);
    $location = get_post_meta($post->ID, 'location', true);
    $hours = get_post_meta($post->ID, 'hours', true);
    $short_description = get_post_meta($post->ID, 'short_description', true);

    // Output the fields
    ?>
    <p>
        <label for="park_name">Name:</label>
        <input type="text" id="park_name" name="park_name" value="<?php echo esc_attr($name); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="park_location">Location:</label>
        <input type="text" id="park_location" name="park_location" value="<?php echo esc_attr($location); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="park_hours">Hours of Operation:</label>
        <input type="text" id="park_hours" name="park_hours" value="<?php echo esc_attr($hours); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="park_short_description">Short Description:</label>
        <textarea id="park_short_description" name="park_short_description" rows="4" style="width: 100%;"><?php echo esc_textarea($short_description); ?></textarea>
    </p>
    <?php
}

function park_save_meta_box_data($post_id) {
    // Check the nonce for security
    if (!isset($_POST['park_meta_box_nonce']) || !wp_verify_nonce($_POST['park_meta_box_nonce'], 'park_save_meta_box_data')) {
        return;
    }

    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the custom field values
    if (isset($_POST['park_name'])) {
        update_post_meta($post_id, 'name', sanitize_text_field($_POST['park_name']));
    }
    if (isset($_POST['park_location'])) {
        update_post_meta($post_id, 'location', sanitize_text_field($_POST['park_location']));
    }
    if (isset($_POST['park_hours'])) {
        update_post_meta($post_id, 'hours', sanitize_text_field($_POST['park_hours']));
    }
    if (isset($_POST['park_short_description'])) {
        update_post_meta($post_id, 'short_description', sanitize_textarea_field($_POST['park_short_description']));
    }
}
add_action('save_post', 'park_save_meta_box_data');