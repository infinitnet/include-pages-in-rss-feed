<?php
/**
 * Plugin Name: Include Pages in RSS Feed
 * Description: Adds page post types to the standard WordPress RSS feed.
 * Author: Infinitnet
 * Author URI: https://infinitnet.io/
 * Plugin URI: https://github.com/infinitnet/include-pages-in-rss-feed
 * Update URI: https://github.com/infinitnet/include-pages-in-rss-feed
 * Version: 1.0
 * License: GPLv3
 * Text Domain: include-pages-in-rss-feed
 */

function add_pages_to_rss_feed($query) {
    if ($query->is_feed()) {
        $query->set('post_type', array('post', 'page'));

        $exclude_ids = get_option('exclude_page_ids_in_rss', '');
        if (!empty($exclude_ids)) {
            $query->set('post__not_in', array_map('intval', explode(',', $exclude_ids)));
        }
    }
    return $query;
}
add_filter('pre_get_posts', 'add_pages_to_rss_feed');

function add_rss_feed_settings_page() {
    add_options_page(
        'Include Pages in RSS Feed',
        'Include Pages in RSS Feed',
        'manage_options',
        'include-pages-in-rss-feed',
        'render_rss_feed_settings_page'
    );
}
add_action('admin_menu', 'add_rss_feed_settings_page');

function render_rss_feed_settings_page() {
    ?>
    <div class="wrap">
        <h1>Include Pages in RSS Feed</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('include-pages-in-rss-feed');
            do_settings_sections('include-pages-in-rss-feed');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function register_rss_feed_settings() {
    register_setting(
        'include-pages-in-rss-feed',
        'exclude_page_ids_in_rss',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );

    add_settings_section('rss_feed_settings_section', '', null, 'include-pages-in-rss-feed');

    add_settings_field(
        'exclude_page_ids',
        'Exclude Page IDs',
        'render_exclude_page_ids_field',
        'include-pages-in-rss-feed',
        'rss_feed_settings_section'
    );
}
add_action('admin_init', 'register_rss_feed_settings');

function render_exclude_page_ids_field() {
    $value = get_option('exclude_page_ids_in_rss', '');
    echo '<input type="text" name="exclude_page_ids_in_rss" value="' . esc_attr($value) . '" />';
}
