<?php
// phpcs:disable PSR1.Files.SideEffects
/**
 * Plugin Name:     Note With Link Shortcode by Misterdigital
 * Plugin URI:      https://github.com/pvtl/wp-note-shortcode.git
 * Description:     Adds a Note Shortcode & popup (with options for the button) to the WYSIWYG
 * Author:          Misterdigital 
 * Author URI:      http://misterdigital.es
 * Text Domain:     note-shortcode
 * Domain Path:     /languages
 * Version:         0.1
 *
 * @package         NoteShortcode
 */

//namespace App\Plugins\Pvtl;

class NoteShortcode
{
    public function __construct()
    {
        // Call the actions/hooks
        add_action('after_setup_theme', array($this, 'afterSetupTheme'));
        add_action('init', array($this, 'registerNoteShortcode'));
    }

    /**
     * Add a Note to Tinymce, only after theme is setup
     *
     * @return void
     */
    public function afterSetupTheme()
    {
        add_action('init', function () {
            // Only execute script when user has access rights
            if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
                return;
            }

            // Only execute script when rich editing is enabled
            if (get_user_option('rich_editing') !== 'true') {
                return;
            }

            // Add the JS to the admin screen
            add_filter('mce_external_plugins', function ($plugin_array) {
                $file = plugin_dir_url(__FILE__) . '/resources/assets/js/shortcode-note-with-link.js';
                $plugin_array['button-shortcode'] = $file;
                return $plugin_array;
            });

            // Register the Note to the editor
            add_filter('mce_buttons', function ($buttons) {
                array_push($buttons, 'button-shortcode');
                return $buttons;
            });
        });
    }

    /**
     * Handle the shortcode
     *
     * @return void
     */
    public function registerNoteShortcode()
    {
        add_shortcode('button', function ($input) {
            if (!is_admin()) {
                $attr = array(
                    'text' => 'Learn More',
                    'href' => '#',
                    'size' => '',
                    'style' => '',
                    'target' => '',
                    'class' => 'button',
                );

                if (!empty($input['text'])) {
                    $attr['text'] = $input['text'];
                }

                if (!empty($input['href'])) {
                    $attr['href'] = $input['href'];
                }

                if (!empty($input['size'])) {
                    $attr['size'] = $input['size'];
                }

                if (!empty($input['style'])) {
                    $attr['style'] = $input['style'];
                }

                if (!empty($input['target'])) {
                    $attr['target'] = $input['target'];
                }

                $attr = apply_filters('wpbs_attributes', $attr);

                $html = '<a href="' . $attr['href'] . '" ';
                $html .= 'class="'.$attr['class'].' ' . $attr['size'] . ' '  . $attr['style'] . '" ';
                $html .= (!empty($attr['target'])) ? 'target="'.$attr['target'].'"' : '';
                $html .= '>'. $attr['text'] . '</a>';

                return $html;
            }
        });
    }
}

if (!defined('ABSPATH')) {
    exit;  // Exit if accessed directly
}

$mdNoteShortcode = new NoteShortcode();
