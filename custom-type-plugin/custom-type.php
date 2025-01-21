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
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'show_in_rest' => true,
        )
    );
}

// Registering custom fields
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
add_action('init', 'custom_post_type_fields');

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