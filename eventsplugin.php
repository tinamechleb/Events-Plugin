<?php

/*
Plugin Name: Events
Description: A custom WordPress plugin for managing events.
Version: 1.0
Author: Tina Mechleb
*/

function custom_events_register_post_type() {
    $labels = array(
        'name'               => _x('Events', 'post type general name'),
        'singular_name'      => _x('Event', 'post type singular name'),
        'menu_name'          => _x('Events', 'admin menu'),
        'add_new'            => _x('Add New', 'event'),
        'add_new_item'       => __('Add New Event'),
        'edit_item'          => __('Edit Event'),
        'new_item'           => __('New Event'),
        'view_item'          => __('View Event'),
        'search_items'       => __('Search Events'),
        'not_found'          => __('No events found'),
        'not_found_in_trash' => __('No events found in Trash'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-calendar-alt',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'),
        'taxonomies'         => array('event_category'),
    );

    register_post_type('event', $args);
}

add_action('init', 'custom_events_register_post_type');


function custom_events_register_taxonomy() {
    $labels = array(
        'name'              => _x('Event Categories', 'taxonomy general name'),
        'singular_name'     => _x('Event Category', 'taxonomy singular name'),
        'search_items'      => __('Search Event Categories'),
        'all_items'         => __('All Event Categories'),
        'parent_item'       => __('Parent Event Category'),
        'parent_item_colon' => __('Parent Event Category:'),
        'edit_item'         => __('Edit Event Category'),
        'update_item'       => __('Update Event Category'),
        'add_new_item'      => __('Add New Event Category'),
        'new_item_name'     => __('New Event Category Name'),
        'menu_name'         => __('Event Categories'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'event-category'),
    );

    register_taxonomy('event_category', array('event'), $args);
}

add_action('init', 'custom_events_register_taxonomy');


function custom_events_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
    ), $atts);

    $args = array(
        'post_type'      => 'event',
        'posts_per_page' => -1,
    );

    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'event_category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        );
    }

    $events_query = new WP_Query($args);

    ob_start();
    if ($events_query->have_posts()) {
        echo '<ul>';
        while ($events_query->have_posts()) {
            $events_query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo '</ul>';
    } else {
        echo 'No events found.';
    }
    wp_reset_postdata();

    return ob_get_clean();
}

add_shortcode('custom_events', 'custom_events_shortcode');

?>