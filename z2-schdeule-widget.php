<?php
/*
  Plugin Name: Zingit Scheduling Widget
  Plugin URI: https://zingitsolutions.com
  Description: Zingit Scheduling widget plugin helps you to get the booking requests from patients in a few seconds. Once installed please add your Zaid ID/Uniques ID sent by the Zingit team.
  Version: 1.2.0
  Author: Zingit Solutions
  Author URI: https://profiles.wordpress.org/zingitadmin/
  License:     GPLv2 or later
  License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// include custom jQuery
function zsw_plugin_redirect_custom_jquery() {
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), false, '3.4.1', false );
    wp_enqueue_script( 'jquery' );
}
add_action('wp_enqueue_scripts', 'zsw_plugin_redirect_custom_jquery');

register_activation_hook(__FILE__, 'zsw_plugin_activate');
add_action('admin_init', 'zsw_plugin_redirect');

function zsw_plugin_activate() {
    add_option('zsw_plugin_do_activation_redirect', true);
}



function zsw_plugin_redirect() {
    if (get_option('zsw_plugin_do_activation_redirect', false)) {
        delete_option('zsw_plugin_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect("admin.php?page=zsw-plugin-settings");
            exit;
        }
    }
}



//Plugin Text Domain
define("ZSW_TXTDM","zingit-scheduling-widget-wordpress");
 
 // All Query Page Code
add_action( 'admin_menu', 'zsw_add_menus' );
function zsw_add_menus() {
	add_menu_page( 'Zingit Scheduling Queries', __( 'Zingit Scheduling', ZSW_TXTDM ),  'administrator', 'zsw-plugin-settings', 'zsw_plugin_settings', 'dashicons-welcome-widgets-menus', 65);
}

// setting page body
function zsw_plugin_settings() {
	add_option('z2_api_key','');

       if(isset($_POST['zsw_update'])) {

           if (!isset($_POST['zsw_update_setting']) || !wp_verify_nonce($_POST['zsw_update_setting'], 'zsw-update-setting')) {
               die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

           } else {
              if(isset($_POST['z2_api_key'])) {
                  $z2_api_guid = sanitize_text_field($_POST['z2_api_key']);
                  update_option('z2_api_key', $z2_api_guid);
              }

           }
       }

    ?>
	
	<form method="post" enctype="multipart/form-data"  class="wrap" name="zsw_form">
		<h1>Plugin Settings</h1>
		<style>
		.ZingFormButton{
			   width:auto;
			   padding: 0 40px;
				background: #0073aa;
				color:#ffffff;
			}
		</style>
		<table class="form-table table">
			<tbody>
			    <tr>
					<th width="45%" scope="row">Enter your Unique ID :</th>
					<td>
                        <input type="text" class="regular-text" name="z2_api_key" value="<?php if(get_option('z2_api_key')){ echo get_option('z2_api_key'); } ?>" >
                        <p class="description">Please enter your Unique Id sent via email by Zingit team to activate this Plugin.</p>
                        <input name="zsw_update_setting" type="hidden" value="<?php echo wp_create_nonce('zsw-update-setting'); ?>" />
                    </td>
				</tr>
                <tr>
				    <td> &nbsp;</td><td><input class="button button-primary ZingFormButton" type="submit" name="zsw_update" value="<?php _e('Update') ?>"></td>
				</tr>

			</tbody>
		</table>
    </form>

    <div class="wrap">
        <h1>Shortcodes</h1>
        <table class="form-table table">
            <tbody>
            <tr>
                <th scope="row">Banner Shortcode :</th>
                <td>[zingit_widget_banner]
                    <p class="description">Please copy and paste this short code to use Banner Widget (Short Code includes "[]")</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Side Button Shortcode :</th>
                <td>[zingit_widget_popover]
                    <p class="description">Please copy and paste this short code to use Popover/Button Widget (Short Code includes "[]")</p>
                </td>
            </tr>
           </tbody>
        </table>
    </div>
	
	<?php
}



function zsw_banner_widget_shortcode() {

$zaidvalue = get_option('z2_api_key');

$string = '<script type="text/javascript" id="z2-schdeule-widget">
        (function () {
            var head = document.getElementsByTagName("head")[0];
            
            jQuery(document).ready(function() {
                onload();
            });
           
            function async_load(zingJQ) {
                window.zingJQ = zingJQ;
                let parentElement = zingJQ("#z2-schdeule-widget").parent();
                metaInfo = document.createElement("span");
                var zaid = document.createAttribute("zaid");
                zaid.value = "'.$zaidvalue.'";
                var id = document.createAttribute("id");
                id.value = "WTAPP_001_672762";
                var widgetType = document.createAttribute("widget-type");
                widgetType.value = "banner";
                metaInfo.setAttributeNode(id);
                metaInfo.setAttributeNode(zaid);
                metaInfo.setAttributeNode(widgetType);
                zingJQ(metaInfo)
                    .appendTo(parentElement); //main div
                var widgetScript = document.createElement("script");
                widgetScript.src = "https://widget-extension.s3-us-west-2.amazonaws.com/widget.js";
                widgetScript.type = "text/javascript";
                head.appendChild(widgetScript);
            }
            var onload = () => {
                var zingJQ = jQuery.noConflict();
                async_load(zingJQ);
            }
        })();
    </script>';
    return $string;

}
add_shortcode( 'zingit_widget_banner', 'zsw_banner_widget_shortcode' );


function zsw_popover_widget_shortcode() {

    $zaidvalue = get_option('z2_api_key');

    $string = '<script type="text/javascript" id="z2-schdeule-widget">
        (function () {
            var head = document.getElementsByTagName("head")[0];
            
            jQuery(document).ready(function() {
                onload();
            });
            
            function async_load(zingJQ) {
                window.zingJQ = zingJQ;
                let parentElement = zingJQ("#z2-schdeule-widget").parent();
                metaInfo = document.createElement("span");
                var zaid = document.createAttribute("zaid");
                zaid.value = "'.$zaidvalue.'";
                var id = document.createAttribute("id");
                id.value = "WTAPP_001_672762";
                var widgetType = document.createAttribute("widget-type");
                widgetType.value = "popover";
                metaInfo.setAttributeNode(id);
                metaInfo.setAttributeNode(zaid);
                metaInfo.setAttributeNode(widgetType);
                zingJQ(metaInfo)
                    .appendTo(parentElement); //main div
                var widgetScript = document.createElement("script");
                widgetScript.src = "https://widget-extension.s3-us-west-2.amazonaws.com/widget.js";
                widgetScript.type = "text/javascript";
                head.appendChild(widgetScript);
            }
            var onload = () => {
                var zingJQ = jQuery.noConflict();
                async_load(zingJQ);
            }
        })();
    </script>';
    return $string;

}
add_shortcode( 'zingit_widget_popover', 'zsw_popover_widget_shortcode' );

?>
