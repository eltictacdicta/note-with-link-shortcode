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

class NoteShortcode
{
    public function __construct()
    {
        // Call the actions/hooks
        add_action('after_setup_theme', array($this, 'afterSetupTheme'));
        add_action('init', array($this, 'registerNoteShortcode'));
        add_action('init', array($this, 'registerCustomNoteShortcode'));
        add_action('wp_head', array($this, 'addCustomStyles'));
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
                    'texto' => 'También te puede interesar:',
                    'url' => '#',
                    'size' => '',
                    'style' => '',
                    'class' => 'button',
                );

                if (!empty($input['texto'])) {
                    $attr['text'] = $input['texto'];
                }

                if (!empty($input['url'])) {
                    $attr['href'] = $input['url'];
                }

                if (!empty($input['anchor'])) {
                    $attr['anchor'] = $input['anchor'];
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

    /**
     * Register custom note shortcode
     *
     * @return void
     */
    public function registerCustomNoteShortcode()
    {
        add_shortcode('nota_personalizada', array($this, 'mostrar_nota_personalizada'));
    }

    /**
     * Display custom note
     *
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function mostrar_nota_personalizada($atts)
    {
        $p = shortcode_atts(array(
            'url' => '',
            'texto' => 'También te puede interesar:',
            'anchor' => '',
            'type' => 'note'
        ), $atts);

        $icon = '';
        $classes = 'nota-cta';

        if ($p['type'] === 'download') {
            $icon = '⬇️ ';
            $classes .= ' download-button';
        } elseif ($p['type'] === 'home') {
            $icon = '🏠 ';
            $classes .= ' home-button';
        }

        // Generar el HTML en función del tipo
        if ($p['type'] === 'note') {
            $texto = '<div class="'.$classes.'"><div class="su-note-inner su-u-clearfix su-u-trim" style="background-color:#2a5c84;color:#ffffff; text-align:center; padding: 15px; margin-bottom: 15px; ">✅'.$p['texto'].'<strong> <a href="'.$p['url'].'" style="text-decoration:none; outline: none;" >'.$icon.$p['anchor'].'</a></strong></div></div>';
        } else {
            // Para tipos 'download' y 'home', generar un botón
            $texto = '<div class="'.$classes.'" style="text-align:center; margin-bottom: 15px;">
                        <a href="'.$p['url'].'" style="text-decoration:none; outline: none;">
                            <button style="background-color:#2a5c84; color:#ffffff; border:none; padding: 15px 30px; cursor:pointer; font-size:16px;">
                                '.$icon.$p['anchor'].'
                            </button>
                        </a>
                    </div>';
        }

        return $texto;
    }

    /**
     * Add custom styles to the head
     *
     * @return void
     */
    public function addCustomStyles()
    {
        echo '
        <style>    
            /* Estilos para el botón generado por el shortcode [button] */
            .button {
                display: inline-block;
                padding: 10px 20px;
                font-size: 16px;
                font-weight: bold;
                text-align: center;
                text-decoration: none;
                color: #ffffff;
                background-color: #0073aa;
                border-radius: 5px;
                border: 2px solid #0073aa;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            .button:hover {
                background-color: #005177;
                border-color: #005177;
                color: #ffffff;
            }

            /* Estilos para el botón de descarga */
            .download-button {
                background-color: #4CAF50;
                border-color: #4CAF50;
            }

            .download-button:hover {
                background-color: #45a049;
                border-color: #45a049;
            }

            /* Estilos para el botón de inicio */
            .home-button {
                background-color: #f44336;
                border-color: #f44336;
            }

            .home-button:hover {
                background-color: #d32f2f;
                border-color: #d32f2f;
            }

            /* Estilos para la nota personalizada */
            .nota-cta a {     
                color: white;        
                border-bottom: medium solid #16C60C;
                line-height: 150%;
            }

            .nota-cta a:hover {
                text-decoration: none;
            }

            .nota-cta a:link {
                text-decoration: none;
            }

            .nota-cta {
                border-style: none;
            }
        </style>';
    }
} // <-- Closing brace for the NoteShortcode class

if (!defined('ABSPATH')) {
    exit;  // Exit if accessed directly
}

$mdNoteShortcode = new NoteShortcode();