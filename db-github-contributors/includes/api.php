<?php

function db_github_contributors_fetch_contributors($github_project_url)
{
    if (!empty($github_project_url)) {
        $github_url_parts = parse_url($github_project_url);
        $github_url_path = explode('/', $github_url_parts['path']);
        $github_username = $github_url_path[1];
        $github_repo = $github_url_path[2];

        $cache_key = 'github_contributors_' . md5($github_project_url);

        $cached_contributors = get_transient($cache_key);
        if ($cached_contributors) {
            return $cached_contributors;
        }

        $api_url = "https://api.github.com/repos/$github_username/$github_repo/contributors";

        $response = wp_remote_get($api_url);

        if (!is_wp_error($response) && $response['response']['code'] === 200) {
            $contributors = json_decode($response['body']);

            set_transient($cache_key, $contributors, HOUR_IN_SECONDS);

            return $contributors;
        } else {
            if (is_wp_error($response)) {
                echo 'Error: ' . $response->get_error_message();
            } else {
                echo 'Error fetching contributors.';
            }
            return '';
        }
    } else {
        return '';
    }
}
