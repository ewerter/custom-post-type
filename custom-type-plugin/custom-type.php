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
            'supports' => array('title', 'editor', 'thumbnail'),
            'show_in_rest' => true,
        )
    );
}