<?php

/**
 * Plugin Name: DB GitHub Contributors
 * Description: Plugin to highlight GitHub profiles that have contributed to one or more specific projects.
 * Version: 1.0.0
 * Implementation: Shortcode, CSS customization
 * Author: Sangligleek
 * Author URI: https://github.com/sangligleek
 */

function db_github_contributors_activate()
{
    add_option('github_project_urls', array());
    add_option('custom_css', '');
}
register_activation_hook(__FILE__, 'db_github_contributors_activate');

include_once(plugin_dir_path(__FILE__) . 'includes/options.php');
include_once(plugin_dir_path(__FILE__) . 'includes/api.php');
include_once(plugin_dir_path(__FILE__) . 'admin/admin.php');

function db_github_contributors_shortcode($atts)
{
    $github_project_urls = get_option('github_project_urls');

    $all_contributors = array();

    foreach ($github_project_urls as $github_project_url) {
        if (!empty($github_project_url)) {
            $contributors = db_github_contributors_fetch_contributors($github_project_url);

            $all_contributors = array_merge($all_contributors, $contributors);
        }
    }

    $unique_contributors = [];
    foreach ($all_contributors as $contributor) {
        if (!isset($unique_contributors[$contributor->login])) {
            $unique_contributors[$contributor->login] = $contributor;
        }
    }

    $custom_css = get_option('custom_css');

    $output = '<style>' . $custom_css . '</style>';

    // Display contributors
    if (!empty($unique_contributors)) {
        $output .= '<h2 class="github-contributors-h2">GitHub contributors</h2>';
        $output .= '<ul id="github-contributors-list">';
        foreach ($unique_contributors as $contributor) {
            $output .= '<li>';
            $output .= '<a href="' . esc_url($contributor->html_url) . '" target="_blank">';
            $output .= '<img src="' . esc_url($contributor->avatar_url) . '" alt="' . esc_attr($contributor->login) . '" />';
            $output .= esc_html($contributor->login);
            $output .= '</a>';
            $output .= '</li>';
        }
        $output .= '</ul>';
    } else {
        $output .= '<p>No contributors found.</p>';
    }

    return $output;
}
add_shortcode('github_contributors', 'db_github_contributors_shortcode');
