<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Islamic_Quotes_Widget extends \Elementor\Widget_Base {

    // Widget Name
    public function get_name() {
        return 'islamic_quotes_widget';
    }

    // Widget Title
    public function get_title() {
        return __( 'Islamic Quotes', 'islamic-quotes-plugin' );
    }

    // Widget Icon
    public function get_icon() {
        return 'fa fa-heart'; // Change 'fa-book' to any other Font Awesome icon class
    }


    // Widget Category
    public function get_categories() {
        return [ 'general' ];
    }

    // Widget Content Controls
    protected function register_controls() {
        // No controls needed for this simple widget, but you can add some here if needed
    }

    // Render the Widget Output
    protected function render() {
        echo do_shortcode('[islamic_quote]');
    }
}
