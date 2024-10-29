<?php
/*
* Plugin Name: Aptivada for wp
* Plugin URI: http://www.aptivada.com
* Description: This Plugin provides a shortcode for embedding a published Audience contest into a WordPress site
* Version: 2.0.0
* Author: audience.io
* Author URI: http://www.audience.io
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


/*
embed the audience sdk in every page, to allow for audience popups.
*/
function audience_custom_javascript() {
    ?>
    <script async defer type="text/javascript" src="https://xp.audience.io/sdk.js"></script>
    <?php
}
add_action('wp_head', 'audience_custom_javascript');

/*
make_aptivada_iframe calls the javascript page from aptivada.com which creates an iframe.
The paramaters are inside args, which must include app-id and app-type.
*/
function make_audience_iframe($args){

    $type = !empty($args['widget-type']) && !empty($args['widget-id']) 
        ?   'widget' 
        :   (!empty($args['sdk-type'])
                ? 'sdk'
                :   (!empty($args['app-type']) && !empty($args['app-id'])
                    ? 'app'
                    : 'unknown'
            ));

    if($type == 'unknown'){
        return '';
    }

    if($type == 'widget'){
        $handle = 'https://www.aptivada.com/js/widget.js';
        $list = 'enqueued';
        if (!wp_script_is( $handle, $list )) {       
            wp_register_script( 'AptivadaWidget', $handle);
            wp_enqueue_script( 'AptivadaWidget' );
        }
        return '<div class="aptivada-widget" data-widget-id="'. $args['widget-id'] .'" data-widget-type="'. $args['widget-type'].'"></div>';
    } else if($type == 'app'){
        $handle = 'https://www.aptivada.com/js/all.js';
        $list = 'enqueued';
        if (!wp_script_is( $handle, $list )) {       
            wp_register_script( 'AptivadaJS', $handle);
            wp_enqueue_script( 'AptivadaJS' );
        }
        return '<div id="aptivada_app" data-app-type=' . $args['app-type'] . ' data-app-id=' . $args['app-id'] . '></div>';
    } else if($type == 'sdk'){
        // $handle = 'https://xp.audience.io/sdk.js';
        // $list = 'enqueued';
        // if (!wp_script_is( $handle, $list )) {       
        //     wp_register_script( 'AptivadaSDK', $handle);
        //     wp_enqueue_script( 'AptivadaSDK' );
        // }
        $app_id = '';
        if (isset($args['app-id'])) {
            $app_id = $args['app-id'];
        }
        $app_type = '';
        if (isset($args['app-type'])) {
            $app_type = $args['app-type'];
        }
        $app_subtype = '';
        if (isset($args['app-subtype'])) {
            $app_subtype = $args['app-subtype'];
        }
        if ($app_id && $app_type){
            return '<div class="audience-container" data-load-enabled="true" data-load-behavior="embed" data-load-revisit-enabled="true" data-load-revisit-behavior="embed" data-id="'. $app_id .'" data-type="'.$app_type.'" '. ($app_subtype ?  'data-subtype="' . $app_subtype . '"' : "").'></div>';
        } else {
            return '';
        }
    }
    
};

add_shortcode("aptivada", "make_audience_iframe");
add_shortcode("audience", "make_audience_iframe");