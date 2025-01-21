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
            'taxonomies' => array( 'park_taxonomy' ),
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

//enqueueing the style
add_action('wp_enqueue_scripts', 'enqueue_custom_type_style');

function enqueue_custom_type_style() {
    wp_enqueue_style(
        'custom_type_style',
        plugins_url('style.css', __FILE__)
    );
}

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

// Registering custom taxonomy
function park_taxonomy() {
    register_taxonomy(
        'facilities',        // Taxonomy name
        'park_post_type',             // post type name
        array(
            'hierarchical' => true,
            'label' => 'Facilities', // display name
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'facilities',    // This controls the base slug that will display before each term
                'with_front' => false  // Don't display the category base before
            ),
            'show_in_rest' => true, // Enables Gutenberg compatibility
            )
    );
}
add_action( 'init', 'park_taxonomy');

// Registering shortcut
function display_park_list($atts) {
    // Parse attributes (if any are passed)
    $atts = shortcode_atts(
        array(
            'posts_per_page' => -1, // Default to show all posts
            'orderby' => 'date',    // Order by date
            'order' => 'DESC',      // Descending order
        ),
        $atts,
        'park_list'
    );

    // Query for the 'park_post_type' posts
    $query = new WP_Query(array(
        'post_type' => 'park_post_type',
        'posts_per_page' => $atts['posts_per_page'],
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    ));

    // Check if posts exist
    if ($query->have_posts()) {
        $output = '<div class="park-list">';

        // Loop through posts and build output
        while ($query->have_posts()) {
            $query->the_post();

            // Fetch custom fields
            $park_name = get_post_meta(get_the_ID(), 'name', true);
            $park_location = get_post_meta(get_the_ID(), 'location', true);
            $park_hours = get_post_meta(get_the_ID(), 'hours', true);

            // Customize the output for each post
            $output .= '<div class="park-item">';
            if (has_post_thumbnail()) {
                $output .= '<div class="park-thumbnail">' . get_the_post_thumbnail(get_the_ID(), 'medium') . '</div>';
            }
            $output .= '<h3>' . get_the_title() . '</h3>';
            if ($park_name) {
                $output .= '<p>Park: ' . esc_html($park_name) . '</p>';
            }
            if ($park_location) {
                $output .= '<p>Location: ' . esc_html($park_location) . '</p>';
            }
            if ($park_hours) {
                $output .= '<p>Hours: ' . esc_html($park_hours) . '</p>';
            }
            $output .= '<div class="park-content"><p>' . wp_trim_words(get_the_excerpt(), 20, '...') . '</p></div>';
            $output .= '</div>';
        }

        $output .= '</div>';

        // Restore original post data
        wp_reset_postdata();
    } else {
        // No posts found
        $output = '<p>No parks found.</p>';
    }

    return $output; // Return the output for the shortcode
}

// Add the shortcode
add_shortcode('park_list', 'display_park_list');


