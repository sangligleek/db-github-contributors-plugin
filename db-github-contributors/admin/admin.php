<?php

function db_github_contributors_add_admin_page()
{
    add_menu_page(
        'DB GitHub Contributors settings',    // Page title
        'DB GitHub Contributors',             // Menu name
        'manage_options',                     // Required capability to see this page
        'db-github-contributors-settings',     // Page slug
        'db_github_contributors_settings_page' // Callback function to display the page
    );
}
add_action('admin_menu', 'db_github_contributors_add_admin_page');
