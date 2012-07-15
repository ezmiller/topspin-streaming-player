<?php
/*
Plugin Name: Topspin Streaming Player
Plugin URI: 
Description: Plugin provides a widget to output a Topspin streaming player.
Version: 0.1
Author: eThan 
Author URI: 
License: GPL2

Copyright 2012  Ethan Miller  (email : ethanzanemiller@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2, 
    as published by the Free Software Foundation. 
    
    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    The license for this software can likely be found here: 
    http://www.gnu.org/licenses/gpl-2.0.html

*/
if (!class_exists('Topspin_Streaming_Player_Widget')) {

    // Define constants.
    define('TS_STREAMING_PLAYER_PLUGIN_PATH', WP_PLUGIN_DIR.'/topspin-streaming-player');
    define('TS_STREAMING_PLAYER_PLUGIN_URL', plugins_url().'/topspin-streaming-player');

    class Topspin_Streaming_Player_Widget extends WP_Widget {

        function Topspin_Streaming_Player_Widget () {
            $widget_ops = array('classname' => 'topspin-streaming-player', 'description' => __('Displays a Topspin streaming player.'));
            $this->WP_Widget('topspin_streaming_player', __('Topspin_Streaming_Player'), $widget_ops );
        }

        function widget($args, $instance) {
            extract($args);

            $version = empty($instance['ts-player-version']) ? 'v2' : $instance['ts-player-version'];
            $player_id = empty($instance['ts-player-id']) ? '' : $instance['ts-player-id'];
            $width = empty($instance['ts-player-width']) ? '' : $instance['ts-player-width'];
            $height = empty($instance['ts-player-height']) ? '' : $instance['ts-player-height'];

            // Version 1 variables
            $artist_id = empty($instance['ts-player-artist-id']) ? '' : $instance['ts-player-artist-id'];

            // Version 2 variables
            $use_custom_css = empty($instance['ts-player-custom-css']) ? 0 : 1;

            switch ( $version ) {
                case 'v1':
                    $content .= '<div class="topspin-widget topspin-widget-bundle-widget" style="position: relative;">' . "\n";
                    $content .= '<object type="application/x-shockwave-flash" width="'. $width .'" height="' . $height .'" id="TSWidget' . $player_id;
                    $content .= '" data="http://cdn.topspin.net/widgets/bundle/swf/TSBundleWidget.swf?timestamp=1296863070" bgColor="#2c4056">' . "\n";
                    $content .= '<param value="always" name="allowScriptAccess" />' . "\n";
                    $content .= '<param name="allowfullscreen" value="true" />' . "\n";
                    $content .= '<param name="quality" value="high" />' . "\n";
                    $content .= '<param name="movie" value="http://cdn.topspin.net/widgets/bundle/swf/TSBundleWidget.swf?timestamp=1296863070" />' . "\n";
                    $content .= '<param name="flashvars" value="widget_id=http://cdn.topspin.net/api/v1/artist/'. $artist_id . '/bundle_widget/' . $player_id;
                    $content .= '?timestamp=1296863070&amp;displayCTAButton=true&amp;theme=black&amp;highlightColor=0x001fbb&amp;linkColor=0xFFFFFF&amp;linkOverColor=0x006fb9&amp;baseColor=0xe8f5fb&amp;fontColor=0x000000&amp;secondaryFontColor=0x000000&amp;borderColor=0x006fb9&amp;playlistItemBgColor1=0x006fb9&amp;playlistItemBgColor2=0x4CAAE7&amp;playlistItemFontColor=0x000000&amp;playlistItemSelectColor=0x001fbb&amp;playlistItemFontSelectColor=0xFFFFFF&amp;playlistItemOverColor=0x89C2E7&amp;scrollbarBgColor=0xcacaca&amp;scrollbarButtonColor=0xf0f0f0&amp;iconColor=0xf0f0f0&amp;iconSelectedColor=0x000000" />' . "\n";
                    $content .= '<param name="wmode" value="transparent" />' . "\n";
                    $content .= '</object></div>' . "\n";
                    break;
                case 'v2':
                    $content = '<iframe id="tsFrame' . $player_id . '" '; 
                    $content .= 'src="http://cdn.topspin.net/api/' . $version . '/widget/player/' . $player_id;
                    if ( $use_custom_css ) {
                        $content .= '?css=' . TS_STREAMING_PLAYER_PLUGIN_URL . '/topspin-player-v2.css';
                    }
                    $content .= '" width="' . $width . '" height="' . $height . '" frameborder="0"></iframe>';
                    break;
                case 'v3':
                    $content = '<iframe id="tsFrame' . $player_id . '" '; 
                    $content .= 'src="http://cdn.topspin.net/api/' . $version . '/player/' . $player_id . '"';
                    $content .= ' width="' . $width . '" height="' . $height . '" frameborder="0"></iframe>';
                    break;
            }

            echo $content;
        }

        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            var_dump($new_instance);

            $instance['ts-player-version'] = strip_tags($new_instance['ts-player-version']);
            $instance['ts-player-id'] = strip_tags($new_instance['ts-player-id']);
            $instance['ts-player-width'] = strip_tags($new_instance['ts-player-width']);
            $instance['ts-player-height'] = strip_tags($new_instance['ts-player-height']);
            $instance['ts-player-custom-css'] = isset($new_instance['ts-player-custom-css']) ? 1 : 0;
            $instance['ts-player-artist-id'] = strip_tags($new_instance['ts-player-artist-id']);
            
            return $instance;
        }

        function form($instance) {

            // Set up some default widget settings.
            $defaults = array(  'ts-player-version' => 'v3', 
                                'ts-player-id' => '', 
                                'ts-player-width' => '220',  
                                'ts-player-height' => '210',
                                'ts-player-custom-css' => '',
                                'ts-player-artist-id' => '' 
                            );
            $instance = wp_parse_args( (array) $instance, $defaults ); 

            $versions = array( 'v1' => 'Version 1', 'v2' => 'Version 2', 'v3' => 'Version 3' );

            // Build form.
            $content = "<p>";
            $content .= '<label for="' . $this->get_field_id('ts-player-version') . '">Topspin Player Version: </label><br/>';
            $content .= '<select id="' . $this->get_field_id('ts-player-version') . '"'; 
            $content .= ' name="' . $this->get_field_name('ts-player-version') . '">';
            foreach ( $versions as $v => $l ) {
                $content .= '<option value ="' . $v . '"'; 
                if ( $v == $instance['ts-player-version'] ) {
                    $content .= ' selected';
                }
                $content .= '>' . $l . '</option>';
            }
            $content .= '</select>';
            $content .= "</p>";

            $content .= '<p>';
            $content .= '<label for="' . $this->get_field_id('ts-player-id') . '">Topspin Player ID: </label><br/>';
            $content .= '<input type="text" id="' . $this->get_field_id('ts-player-id') . '"';
            $content .= ' name="' . $this->get_field_name('ts-player-id') . '"'; 
            $content .= ' value="' . $instance['ts-player-id'] . '"/>';
            $content .= '</p>';

            $content .= '<p>';
            $content .= '<label for="' . $this->get_field_id('ts-player-width') . '">Player Width: </label><br/>';
            $content .= '<input type="number" id="' . $this->get_field_id('ts-player-width') . '"';
            $content .= ' name="' . $this->get_field_name('ts-player-width') . '"'; 
            $content .= ' value="' . $instance['ts-player-width'] . '"/>';
            $content .= '</p>';

            $content .= '<p>';
            $content .= '<label for="' . $this->get_field_id('ts-player-height') . '">Player Height: </label><br/>';
            $content .= '<input type="number" id="' . $this->get_field_id('ts-player-height') . '"';
            $content .= ' name="' . $this->get_field_name('ts-player-height') . '"'; 
            $content .= ' value="' . $instance['ts-player-height'] . '"/>';
            $content .= '</p>';

            $content .= '<p>';
            $content .= '<label class="v2-fields" for="' . $this->get_field_id('ts-player-custom-css') . '"';
            if ( $instance['ts-player-version'] == 'v2' ) {
                $content .= 'style="display:inline;"';
            } else { $content .= 'style="display:none;"'; }
            $content .= ' title="Set custom CSS in Settings->Topspin Player">Custom CSS: </label>';
            $content .= '<input type="checkbox" class="v2-fields" id="' . $this->get_field_id('ts-player-custom-css') . '"';
            $content .= ' name="' . $this->get_field_name('ts-player-custom-css') . '" ';
            if ( $instance['ts-player-custom-css'] ) {
                $content .= 'checked ';    
            }        
            if ( $instance['ts-player-version'] == 'v2' ) {
                $content .= 'style="display:inline;" />';
            } else { $content .= 'style="display:none;"/>'; }
            $content .= '</p>';

            $content .= '<p>';
            $content .= '<label class="v1-fields" for="' . $this->get_field_id('ts-player-artist-id') . '"';
            if ( $instance['ts-player-version'] == 'v1' ) {
                $content .= 'style="display:inline;"';
            } else { $content .= 'style="display:none;"'; }
            $content .= '>Topspin Artist ID: </label><br/>';
            $content .= '<input type="text" class="v1-fields" id="' . $this->get_field_id('ts-player-artist-id') . '"';
            $content .= ' name="' . $this->get_field_name('ts-player-artist-id') . '"';
            $content .= ' value="' . $instance['ts-player-artist-id'] . '"';
            if ( $instance['ts-player-version'] == 'v1' ) {
                $content .= 'style="display:inline;"/>';
            } else { $content .= 'style="display:none;"/>'; }
            $content .= '</p>';

            // jQuery script to manage fields necessary for different versions of player
            $content .= '<script>';
            $content .= 'jQuery(document).ready(function () { ';
                $content .= 'jQuery("#' . $this->get_field_id('ts-player-version') . '").change(function() { ';
                    $content .= 'if ( jQuery("#' . $this->get_field_id('ts-player-version') . '").val() == "v1" ) { '; 
                        $content .= 'jQuery(".v1-fields").css("display", "inline"); ';
                        $content .= 'jQuery(".v2-fields").css("display", "none"); ';                    
                    $content .= '} ';
                    $content .= 'else if ( jQuery("#' . $this->get_field_id('ts-player-version') . '").val() == "v2" ) { '; 
                        $content .= 'jQuery(".v1-fields").css("display", "none"); ';
                        $content .= 'jQuery(".v2-fields").css("display", "inline"); ';                    
                    $content .= '} ';
                    $content .= 'else if ( jQuery("#' . $this->get_field_id('ts-player-version') . '").val() == "v3" ) { '; 
                        $content .= 'jQuery(".v1-fields").css("display", "none"); ';
                        $content .= 'jQuery(".v2-fields").css("display", "none"); ';                    
                    $content .= '} ';

                $content .= '}); '; 
            $content .= '});';
            $content .= '</script>';

            // Output form.
            echo $content;
        }
    }
    add_action('widgets_init', create_function('', 'return register_widget("Topspin_Streaming_Player_Widget");'));

    // Create an admin page for the plugin under the Settings tab in WP
    //

    /**
     * Function: Registers the admin page
     *
     */
    function topspin_streaming_player_plugin_admin_menu() {
        add_options_page( 'Topspin Streaming Player Plugin Settings', 
                          'Topspin Player', 
                          'manage_options', 
                          'ts-player-plugin-settings', 
                          'topspin_streaming_player_plugin_admin_options_page' );
    }
    add_action('admin_menu', 'topspin_streaming_player_plugin_admin_menu');

    /**
     * Function: Outputs the admin page
     *
     * Note: specified in function topspin_streaming_player_plugin_admin_menu()
     *
     */
    function topspin_streaming_player_plugin_admin_options_page() {

        // Set the path to the Version 2 streaming player stylesheet
        $stylesheet = TS_STREAMING_PLAYER_PLUGIN_PATH . '/topspin-player-v2.css';

        // Save submitted options.
        if ( isset($_POST['ts-streaming-player-v2-styles']) ) {
            if ( is_writable($stylesheet) ) {
                $success = file_put_contents($stylesheet, $_POST['ts-streaming-player-v2-styles']);
            }
            if ( $success !== FALSE ) { 
                echo '<div class="updated"><p><strong>Setting updated.</strong></p></div>';
            } else { echo '<div class="error"><p><strong>Something went wrong while updating the settings.</strong></p></div>'; }   
        }

        // Build options form.
        $content = '<div class="wrap">';
        
        $content .= get_screen_icon();
        $content .= '<h2>' . __('Topspin Streaming Player Plugin Configuration') . '</h2>';
        $content .= '<div class="metabox-holder">';

        // Main box for settings
        $content .= '<div class="postbox-container" style="width: 75%">';
            $content .= '<form method="post">';
                // Stylesheet for v2 player
                $content .= '<div class="postbox">';
                    $content .= '<div class="handlediv" title="Click to toggle"><br></div>';
                    $content .= '<h3 class="hndle"><span>Topspin Streaming Player Version 2 Settings</span></h3>';
                    $content .= '<div class="inside">';
                        $content .= '<table class="form-table"><tbody>';
                            $content .= '<tr valign="top">';
                            $content .= '<td>';
                                $content .= '<b>Streaming Player V2 Styles:</b><br/>';
                                $content .= '<textarea name="ts-streaming-player-v2-styles" style="width:100%; height:250px;" ';
                                $content .= is_writable($stylesheet) ? '>' : 'disabled="true">';
                                $content .= file_get_contents($stylesheet);
                                $content .= '</textarea>';
                                $content .= is_writable($stylesheet) ? '' : '<em>This file is not writable.</em>';
                            $content .= '</td>';
                            $content .= '</tr>';
                        $content .= '</tbody></table>';                
                    $content .= '</div>';
                $content .= '</div>';
                // Submit button
                $content .= '<p class="submit">';
                $content .= '<input type="submit" name="submit" value="' . __('Update Options') . '&raquo;" />';
                $content .= '</p>';
            $content .= '</form>';          
        $content .= '</div><!-- .postbox-container -->';

        $content .= '<div class="postbox-container side" style="width:20%; margin-left:10px;">';

            // Found Bug? Box
            $content .= '<div id="found-bug" class="postbox">';
            $content .= '<div class="handlediv" title="Click to toggle"><br></div>';
                $content .= '<h3 class="hndle"><span><strong>Found a Bug?</strong></span></h3>';
                $content .= '<div class="inside">';
                    $content .= "<p>If you've found a bug or are experiencing a problem in this plugin, please send an email to ";
                    $content .= "<a href='mailto:emiller@screencuts.com?subject=Topspin Streaming Player Plugin Bug'>emiller@screen-cuts.com</a> ";
                    $content .= " with a clear description of the problem.</p>"; 
                $content .= '</div>';
            $content .= '</div>';
            

            // Donate Box    
            $content .= '<div id="donate" class="postbox">';
            $content .= '<div class="handlediv" title="Click to toggle"><br></div>';
            $content .= '<h3 class="hndle"><span><strong class="red">Donate</strong></span></h3>';
            $content .= '<div class="inside">';
                $content .= '<p><strong>';
                $content .= 'Want to help make this plugin even better? Donations help pay for development time, so donate $5, $10, or $20 now!';
                $content .= '</strong></p>';
                    $content .= '<form style="width:160px;margin:0 auto;" action="https://www.paypal.com/cgi-bin/webscr" method="post">';
                    $content .= '<input type="hidden" name="cmd" value="_s-xclick">';
                    $content .= '<input type="hidden" name="hosted_button_id" value="CDV95SG2YQXXY">';
                    $content .= '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
                    $content .= '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';
                    $content .= '</form>';
            $content .= '</div>';
        $content .= '</div><!-- .postbox-container .side -->';
        
        $content .= '</div><!-- .metabox-holder -->';
        $content .= '</div><!-- .wrap -->';

        // Output
        echo $content;
    }

    function topspin_streaming_player_options_page_scripts() {
        wp_enqueue_script('dashboard');
        wp_enqueue_script('postbox');
    }
    add_action( 'admin_print_scripts', 'topspin_streaming_player_options_page_scripts' );

}

?>