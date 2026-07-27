<?php
/**
 * Plugin Name: SBA Site Support
 * Description: Allows SVG uploads, loads Poppins font + global design CSS for the Soulful Beginnings Academy page.
 */

if (!defined('ABSPATH')) exit;

/* ---- Allow SVG uploads ---- */
add_filter('upload_mimes', function ($mimes) {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
});
add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    if (substr(strtolower($filename), -4) === '.svg') {
        $data['ext']  = 'svg';
        $data['type'] = 'image/svg+xml';
    }
    return $data;
}, 10, 4);
// Let the media library render SVG thumbnails
add_filter('wp_prepare_attachment_for_js', function ($response, $attachment) {
    if ($response['mime'] === 'image/svg+xml') {
        $response['sizes'] = array(
            'full' => array(
                'url'         => $response['url'],
                'width'       => 150,
                'height'      => 150,
                'orientation' => 'portrait',
            ),
        );
    }
    return $response;
}, 10, 2);

/* ---- Front-end assets: Poppins + global design CSS ---- */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'sba-poppins',
        'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;1,700;1,800&display=swap',
        array(),
        null
    );
    wp_enqueue_style(
        'sba-design',
        content_url('mu-plugins/sba-design.css'),
        array(),
        '2.7'
    );
    // Testimonials carousel + mobile hamburger behaviour
    wp_enqueue_script(
        'sba-carousel',
        content_url('mu-plugins/sba-carousel.js'),
        array(),
        '1.1',
        true
    );
}, 20);

/* Tag the front-end body so the design CSS scopes cleanly */
add_filter('body_class', function ($classes) {
    $classes[] = 'sb-page';
    return $classes;
});

/* Load the same CSS inside the Elementor editor canvas so it looks right while editing */
add_action('elementor/editor/after_enqueue_styles', function () {
    wp_enqueue_style('sba-design-editor', content_url('mu-plugins/sba-design.css'), array(), '1.0');
});
