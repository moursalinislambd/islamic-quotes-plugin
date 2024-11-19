<?php
/**
 * Plugin Name:       Islamic Quotes Plugin
 * Plugin URI:        https://bdislamicqa.xyz/wordpress-plugin/
 * Contact with me:   mailto:dev.onexusdev@gmail.com or mailto:morsalinislam.net@gmail.com
 * Description:       A beautiful islamic quote plugin that displays daily Islamic quotes with easy sharing options for Facebook, Twitter, and WhatsApp. Perfect for adding inspirational quotes to your site.
 * Version:           1.0
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            DewsofDev , OnexusDev, Moursalin Islam
 * Author URI:        https://www.facebook.com/morsalinislam.bd
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       islamic-quotes-plugin
 * Domain Path:       /languages
 * Tags:              quotes, Islamic, social media, sharing, inspiration
 * Tested up to:      6.2
 * Stable tag:        1.0.0
 *
 * ------------------------------------------------------------------------
 * This plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this plugin. If not, see <https://www.gnu.org/licenses/>.
 * ------------------------------------------------------------------------
 */




// Ensure no direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Enqueue Styles and Scripts
 */
if (!function_exists('iq_enqueue_assets')) {
    function iq_enqueue_assets() {
        // Enqueue CSS
        wp_enqueue_style('iq-style', plugins_url('assets/style.css', __FILE__));

        
    }
    add_action('wp_enqueue_scripts', 'iq_enqueue_assets');
}

/**
 * Function to Load Quotes from JSON
 */
if (!function_exists('iq_get_random_quote')) {
    function iq_get_random_quote() {
        $file_path = plugin_dir_path(__FILE__) . 'assets/quotes.json';
        if (file_exists($file_path)) {
            $quotes = json_decode(file_get_contents($file_path), true);
            if (!empty($quotes)) {
                return $quotes[array_rand($quotes)];
            }
        }
        return "Inspiring Islamic Quote"; // Default quote if none found
    }
}

/**
 * Shortcode Function to Render the Quote and Buttons
 */
if (!function_exists('iq_render_quote_shortcode')) {
    function iq_render_quote_shortcode() {
        $quote = iq_get_random_quote();
        $plugin_name = "Islamic Quotes Plugin";
        $encoded_quote = urlencode($quote . " - Shared via $plugin_name");

        // Social Media Share URLs
        $facebook_url = "https://www.facebook.com/sharer/sharer.php?u=&quote=" . $encoded_quote;
        $twitter_url = "https://twitter.com/intent/tweet?text=" . $encoded_quote;
        $whatsapp_url = "https://wa.me/?text=" . $encoded_quote;

        // Display Quote and Share Buttons
        $output = '<div class="iq-quote-container">';
        $output .= '<blockquote class="iq-quote-text">' . esc_html($quote) . '</blockquote>';
        $output .= '<div class="iq-share-buttons">';
        $output .= '<a href="' . esc_url($facebook_url) . '" target="_blank" class="iq-share-btn iq-facebook">Share on Facebook</a>';
        $output .= '<a href="' . esc_url($twitter_url) . '" target="_blank" class="iq-share-btn iq-twitter">Share on Twitter</a>';
        $output .= '<a href="' . esc_url($whatsapp_url) . '" target="_blank" class="iq-share-btn iq-whatsapp">Share on WhatsApp</a>';
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
    add_shortcode('islamic_quote', 'iq_render_quote_shortcode');
}

/**
 * Register the Elementor Islamic Quotes Widget
 */
if (!function_exists('iq_register_elementor_widgets')) {
    function iq_register_elementor_widgets() {
        // Check if Elementor is active
        if ( did_action( 'elementor/loaded' ) ) {
            // Include the Elementor widget file
            require_once( plugin_dir_path( __FILE__ ) . 'includes/class-islamic-quotes-widget.php' );

            // Register the widget
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Islamic_Quotes_Widget() );
        }
    }
    add_action( 'elementor/widgets/widgets_registered', 'iq_register_elementor_widgets' );
}

/**
 * Plugin Activation Hook to Check Elementor
 */
if (!function_exists('iq_check_elementor_dependency')) {
    function iq_check_elementor_dependency() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate the plugin
            wp_die( 'This plugin requires Elementor to be installed and activated.', 'Plugin dependency check', array( 'back_link' => true ) );
        }
    }
    register_activation_hook( __FILE__, 'iq_check_elementor_dependency' );
}

/**
 * Enqueue Gutenberg Block Assets
 */
if (!function_exists('iq_register_gutenberg_block')) {
    function iq_register_gutenberg_block() {
        // Check if Gutenberg is active
        if ( function_exists( 'register_block_type' ) ) {
            wp_register_script(
                'iq-block-script',
                plugins_url('blocks/block.js', __FILE__),
                array( 'wp-blocks', 'wp-element', 'wp-editor' ), // Dependencies for Gutenberg
                null,
                true
            );

            // Register the block type
            register_block_type('islamic-quotes-plugin/quote-block', array(
                'editor_script' => 'iq-block-script',
                'render_callback' => 'iq_render_quote_shortcode', // Use the shortcode function to render the content
            ));
        }
    }
    add_action('init', 'iq_register_gutenberg_block');
}







// plugin update system 


// Define constants for plugin information
define('DQV_PLUGIN_VERSION', '1.0'); // Replace with your current version
define('DQV_UPDATE_URL', 'https://bdislamicqa.xyz/diq-update-info.json.json'); // URL of the JSON update info

// Function to check for updates from the JSON file
function dqv_check_for_updates() {
    // Fetch the JSON file content
    $response = wp_remote_get(DQV_UPDATE_URL);

    if (is_wp_error($response)) {
        return; // Error fetching the JSON file, stop further processing
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    
    if (empty($data) || !isset($data['new_version'])) {
        return; // No valid data found in the JSON file
    }

    // Check if there's a new version available
    $new_version = $data['new_version'];
    $update_url = $data['update_url'];
    $description = isset($data['description']) ? $data['description'] : '';

    if (version_compare(DQV_PLUGIN_VERSION, $new_version, '<')) {
        // Display an admin notice for the update
        add_action('admin_notices', function() use ($new_version, $update_url, $description) {
            echo '<div class="notice notice-warning is-dismissible" style="background-color: yellow; padding: 10px;">';
            echo '<p><strong>There is a new version (' . esc_html($new_version) . ') of Daily Quran Verse. ';
            echo esc_html($description) . '</strong></p>';
            echo '<p><a href="' . esc_url($update_url) . '" class="button-primary" style="font-weight: bold;">Click here to update</a></p>';
            echo '</div>';
        });
    }
}

add_action('admin_init', 'dqv_check_for_updates');





// Function to handle auto-update from Google Drive
function dqv_auto_update_plugin() {
    if (isset($_GET['dqv_auto_update']) && $_GET['dqv_auto_update'] === 'true') {
        // Fetch update information
        $response = wp_remote_get(DQV_UPDATE_URL);
        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($data) || !isset($data['update_url'])) {
            wp_die('Update URL not found.');
        }

        $update_url = $data['update_url'];

        // Download the ZIP file from Google Drive
        $tmp_file = download_url($update_url);

        if (is_wp_error($tmp_file)) {
            wp_die('Failed to download the update.');
        }

        // Unzip the downloaded file
        $result = unzip_file($tmp_file, WP_PLUGIN_DIR . '/daily-quran-verse');

        if (is_wp_error($result)) {
            wp_die('Failed to unzip the update.');
        }

        // Delete the temporary file
        unlink($tmp_file);

        // Notify the admin that the update is complete
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>The Daily Quran Verse plugin has been updated successfully.</p>';
            echo '</div>';
        });
    }
}

add_action('admin_init', 'dqv_auto_update_plugin');



// Add a button or link for manual update
function dqv_add_manual_update_button() {
    $update_url = admin_url('?dqv_auto_update=true');
    echo '<div class="wrap">';
    echo '<h1>Daily Quran Verse Plugin</h1>';
    echo '<p>Click the button below to manually update the plugin:</p>';
    echo '<a href="' . esc_url($update_url) . '" class="button-primary">Update Now</a>';
    echo '</div>';
}

add_action('admin_menu', function() {
    add_options_page('Daily Quran Verse Update', 'Update Daily Quran Verse', 'manage_options', 'dqv-update', 'dqv_add_manual_update_button');
});
