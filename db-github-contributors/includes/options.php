<?php

function db_github_contributors_settings_page()
{
?>
    <div class="wrap">
        <h1>DB GitHub Contributors</h1>
        <form method="post" action="options.php">
            <?php settings_fields('db_github_contributors_settings_group'); ?>
            <?php do_settings_sections('db-github-contributors-settings'); ?>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
<?php
}

function db_github_contributors_initialize_settings()
{
    add_settings_section(
        'db_github_contributors_section',
        'GitHub Project Settings',
        'db_github_contributors_section_callback',
        'db-github-contributors-settings'
    );

    add_settings_field(
        'github_project_urls',
        'GitHub Project URLs',
        'github_project_urls_callback',
        'db-github-contributors-settings',
        'db_github_contributors_section'
    );

    add_settings_field(
        'custom_css',
        'Custom CSS',
        'db_github_custom_css_callback',
        'db-github-contributors-settings',
        'db_github_contributors_section'
    );

    register_setting(
        'db_github_contributors_settings_group',
        'github_project_urls',
        array(
            'type' => 'array',
            'sanitize_callback' => 'sanitize_github_project_urls',
            'default' => array()
        )
    );

    register_setting(
        'db_github_contributors_settings_group',
        'custom_css'
    );
}

function db_github_contributors_section_callback()
{
    echo 'Enter the URL(s) of your GitHub project(s) below to fetch contributors.';
}

function github_project_urls_callback()
{
    $github_project_urls = get_option('github_project_urls');
    if (empty($github_project_urls)) {
        echo "<input type='text' name='github_project_urls[]' value='' style='margin-bottom: 10px; margin-right: 10px;' />";
    } else {
        foreach ($github_project_urls as $index => $url) {
            if (!empty($url)) {
                echo "<input type='text' name='github_project_urls[]' value='$url' style='margin-bottom: 10px; margin-right: 10px;'/>";
                echo "<button class='delete_github_project_url'>Delete URL</button>";
                echo "<br />";
            }
        }
    }
?>
    <button id="add_github_project_url">Add URL</button>
    <script>
        document.getElementById('add_github_project_url').addEventListener('click', function(e) {
            e.preventDefault();
            var input = document.createElement('input');
            input.setAttribute('type', 'text');
            input.setAttribute('name', 'github_project_urls[]');
            var br = document.createElement('br');
            var inputs = document.querySelectorAll('input[name="github_project_urls[]"]');
            var lastInput = inputs[inputs.length - 1];
            var parentTd = lastInput.parentNode;
            parentTd.appendChild(input);
            parentTd.appendChild(br);
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('delete_github_project_url')) {
                e.target.previousSibling.remove();
                e.target.remove();
                e.target.nextSibling.remove();
            }
        });
    </script>
<?php
}

function db_github_custom_css_callback()
{
    $custom_css = get_option('custom_css');

    $escaped_custom_css = esc_textarea($custom_css);

    echo "<textarea name='custom_css' rows='10' cols='50'>$escaped_custom_css</textarea>";
}

function db_github_contributors_register_custom_css()
{
    $css_file = plugin_dir_path(__FILE__) . '../public/assets/custom.css';

    wp_register_style('db_github_contributors_custom_css', $css_file);
}

function db_github_contributors_enqueue_custom_css()
{
    $css_file_path = plugin_dir_path(__FILE__) . '../public/assets/custom.css';

    $custom_css = get_option('custom_css');

    file_put_contents($css_file_path, $custom_css);

    wp_enqueue_style('db_github_contributors_custom_css', plugin_dir_url(__FILE__) . '../public/assets/custom.css');
}

add_action('wp_enqueue_scripts', 'db_github_contributors_register_custom_css');

add_action('wp_enqueue_scripts', 'db_github_contributors_enqueue_custom_css');

function sanitize_github_project_urls($input)
{
    foreach ($input as &$url) {
        $url = esc_url_raw($url);
    }
    return $input;
}

add_action('admin_init', 'db_github_contributors_initialize_settings');
?>