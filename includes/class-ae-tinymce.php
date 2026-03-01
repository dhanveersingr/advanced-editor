<?php
/**
 * Advanced Editor - TinyMCE Core Configuration
 */

if (!defined('ABSPATH')) {
    exit;
}

class AE_TinyMCE
{

    public function __construct()
    {
        // Expand the visual buttons into FOUR maximally loaded rows
        add_filter('mce_buttons', array($this, 'configure_row_1'));
        add_filter('mce_buttons_2', array($this, 'configure_row_2'));
        add_filter('mce_buttons_3', array($this, 'configure_row_3'));
        add_filter('mce_buttons_4', array($this, 'configure_row_4'));

        // Deeply configure TinyMCE parameters (plugins, advanced HTML, force open toolbars)
        add_filter('tiny_mce_before_init', array($this, 'advanced_mce_config'));

        // Dynamically load ALL missing TinyMCE JS files
        add_filter('mce_external_plugins', array($this, 'load_external_plugins'));
    }

    /**
     * Row 1: Drodown & Core Formatting (High Density)
     */
    public function configure_row_1($buttons)
    {
        return array(
            'formatselect',
            'styleselect',
            'fontselect',
            'fontsizeselect',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'superscript',
            'subscript',
            '|',
            'forecolor',
            'backcolor',
            '|',
            'wp_adv'
        );
    }

    /**
     * Row 2: Structure & Links
     */
    public function configure_row_2($buttons)
    {
        return array(
            'alignleft',
            'aligncenter',
            'alignright',
            'alignjustify',
            '|',
            'bullist',
            'numlist',
            'outdent',
            'indent',
            '|',
            'ltr',
            'rtl',
            '|',
            'link',
            'unlink',
            'anchor',
            '|',
            'image',
            'media',
            '|',
            'pastetext',
            'removeformat',
            '|',
            'undo',
            'redo'
        );
    }

    /**
     * Row 3: Power Tools & Utility
     */
    public function configure_row_3($buttons)
    {
        return array(
            'table',
            '|',
            'hr',
            'charmap',
            'emoticons',
            'nonbreaking',
            '|',
            'insertdatetime',
            'pagebreak',
            'blockquote',
            '|',
            'searchreplace',
            'visualchars',
            'visualblocks',
            '|',
            'print',
            'preview',
            'fullscreen',
            '|',
            'code',
            '|',
            'wp_help'
        );
    }

    /**
     * Row 4: Consistently Empty
     */
    public function configure_row_4($buttons)
    {
        return array();
    }

    /**
     * Aggressively configure TinyMCE's internal settings
     */
    public function advanced_mce_config($initArray)
    {
        // 1. Force the Kitchen Sink (Rows 2+) to be OPEN by default, saving a click
        $initArray['wordpress_adv_hidden'] = false;

        // 2. Add extra font sizes
        $initArray['fontsize_formats'] = '8pt 10pt 12pt 14pt 18pt 24pt 36pt 48pt';

        // 3. Prevent WP from stripping advanced HTML tags (DIVs, Iframes, Spans with inline styles)
        $ext = 'iframe[align|longdesc|name|width|height|frameborder|scrolling|marginheight|marginwidth|src|allowfullscreen],';
        $ext .= 'span[id|name|class|style|lang|dir],';
        $ext .= 'div[id|name|class|style|lang|dir],';
        $ext .= 'style[type],';
        $ext .= 'script[async|defer|src|type|language]';

        if (isset($initArray['extended_valid_elements'])) {
            $initArray['extended_valid_elements'] .= ',' . $ext;
        } else {
            $initArray['extended_valid_elements'] = $ext;
        }

        // 4. Stop auto-stripping of empty tags
        $initArray['verify_html'] = false;
        $initArray['paste_as_text'] = true;

        // 5. Config DateTime plugin output format
        $initArray['insertdatetime_formats'] = '%H:%M:%S,%Y-%m-%d,%I:%M:%S %p,%D';

        // 6. Ensure the plugins array allows these native features to even run
        if (isset($initArray['plugins'])) {
            $initArray['plugins'] .=
                ',searchreplace,table,visualchars,visualblocks,print,nonbreaking,anchor,emoticons,textpattern,insertdatetime,pagebreak,preview,directionality,fullscreen,media,lists,advlist,textcolor,colorpicker,hr';
        } else {
            $initArray['plugins'] =
                'searchreplace,table,visualchars,visualblocks,print,nonbreaking,anchor,emoticons,textpattern,insertdatetime,pagebreak,preview,directionality,fullscreen,media,lists,advlist,textcolor,colorpicker,hr';
        }

        // 7. SEMANTIC ARCHETYPES (Instant Content Formatting)
        $style_formats = array(
            array(
                'title' => 'Archetype: News Article',
                'items' => array(
                    array('title' => 'Master Headline', 'block' => 'h1', 'classes' => 'nt2-news-h1'),
                    array('title' => 'Sub-Headline', 'block' => 'h2', 'classes' => 'nt2-news-h2'),
                    array('title' => 'Justified Content', 'block' => 'p', 'classes' => 'nt2-news-p'),
                    array('title' => 'Pull Quote', 'block' => 'blockquote', 'classes' => 'nt2-news-quote'),
                ),
            ),
            array(
                'title' => 'Archetype: Encyclopedia',
                'items' => array(
                    array('title' => 'Topic Title', 'block' => 'h1', 'classes' => 'nt2-wiki-h1'),
                    array('title' => 'Data Body', 'block' => 'p', 'classes' => 'nt2-wiki-p'),
                    array('title' => 'Info Sidebar', 'block' => 'div', 'classes' => 'nt2-wiki-sidebar', 'wrapper' => true),
                ),
            ),
            array(
                'title' => 'Archetype: Blog / Article',
                'items' => array(
                    array('title' => 'Creative Headline', 'block' => 'h1', 'classes' => 'nt2-blog-h1'),
                    array('title' => 'Author Bio', 'block' => 'div', 'classes' => 'nt2-blog-bio', 'wrapper' => true),
                    array('title' => 'Opening (Drop Cap)', 'block' => 'p', 'classes' => 'nt2-blog-dropcap'),
                    array('title' => 'Engaging Para', 'block' => 'p', 'classes' => 'nt2-blog-p'),
                ),
            ),
            array(
                'title' => 'Archetype: Product Review',
                'items' => array(
                    array('title' => 'Review Heading', 'block' => 'h1', 'classes' => 'nt2-rev-h1'),
                    array('title' => 'Spec Summary', 'block' => 'table', 'classes' => 'nt2-rev-table'),
                    array('title' => 'Pros List', 'block' => 'ul', 'classes' => 'nt2-rev-pros'),
                    array('title' => 'Cons List', 'block' => 'ul', 'classes' => 'nt2-rev-cons'),
                ),
            ),
            array(
                'title' => 'Archetype: Web Page',
                'items' => array(
                    array('title' => 'Hero Heading', 'block' => 'h1', 'classes' => 'nt2-page-h1'),
                    array('title' => 'Modern Section', 'block' => 'div', 'classes' => 'nt2-page-section', 'wrapper' => true),
                    array('title' => 'Action Button', 'block' => 'div', 'classes' => 'nt2-page-cta', 'wrapper' => true),
                ),
            ),
        );
        $initArray['style_formats'] = json_encode($style_formats);

        return $initArray;
    }

    /**
     * Load ALL External Non-Bundled TinyMCE Plugins Natively (Self-Sufficient)
     * We now point to the local assets folder for maximum privacy and offline support.
     */
    public function load_external_plugins($plugin_array)
    {
        $base_url = AE_URL . 'assets/tinymce-plugins/';
        $v = '?v=' . AE_VERSION; // Cache busting

        $plugin_array['searchreplace'] = $base_url . 'searchreplace/plugin.min.js' . $v;
        $plugin_array['table'] = $base_url . 'table/plugin.min.js' . $v;
        $plugin_array['visualblocks'] = $base_url . 'visualblocks/plugin.min.js' . $v;
        $plugin_array['visualchars'] = $base_url . 'visualchars/plugin.min.js' . $v;
        $plugin_array['print'] = $base_url . 'print/plugin.min.js' . $v;
        $plugin_array['anchor'] = $base_url . 'anchor/plugin.min.js' . $v;
        $plugin_array['insertdatetime'] = $base_url . 'insertdatetime/plugin.min.js' . $v;
        $plugin_array['pagebreak'] = $base_url . 'pagebreak/plugin.min.js' . $v;
        $plugin_array['preview'] = $base_url . 'preview/plugin.min.js' . $v;
        $plugin_array['directionality'] = $base_url . 'directionality/plugin.min.js' . $v;
        $plugin_array['emoticons'] = $base_url . 'emoticons/plugin.min.js' . $v;
        $plugin_array['nonbreaking'] = $base_url . 'nonbreaking/plugin.min.js' . $v;
        $plugin_array['textpattern'] = $base_url . 'textpattern/plugin.min.js' . $v;
        $plugin_array['advlist'] = $base_url . 'advlist/plugin.min.js' . $v;
        $plugin_array['textcolor'] = $base_url . 'textcolor/plugin.min.js' . $v;
        $plugin_array['colorpicker'] = $base_url . 'colorpicker/plugin.min.js' . $v;
        $plugin_array['lists'] = $base_url . 'lists/plugin.min.js' . $v;
        $plugin_array['hr'] = $base_url . 'hr/plugin.min.js' . $v;
        $plugin_array['fullscreen'] = $base_url . 'fullscreen/plugin.min.js' . $v;
        $plugin_array['media'] = $base_url . 'media/plugin.min.js' . $v;

        return $plugin_array;
    }
}