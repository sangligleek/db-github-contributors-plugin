<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('github_project_url');
