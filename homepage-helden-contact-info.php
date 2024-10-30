<?php
/**
 * @link              https://www.homepage-helden.de
 * @since             1.0.0
 * @package           Homepage_Helden_Contact_Info
 *
 * Plugin Name:       Homepage Helden Contact Info
 * Plugin URI:        http://wordpress.org/plugins/homepage-helden-contact-info/
 * Description:       Contact information for Homepage Helden GmbH clients - displayed directly on your Wordpress dashboard for quick support
 * Version:           1.3
 * Author:            Thore Janke
 * Author URI:        https://www.homepage-helden.de
 * Text Domain:       homepage-helden-contact-info
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/

	if(isset($_REQUEST['disable']))
	{
		if($_REQUEST['disable'] == 'homepage-helden-contact-info')
		{
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$Homepage_Helden_Contact_Info_plugin_path = plugin_dir_path( __FILE__ );
			$Homepage_Helden_Contact_Info_plugin = $Homepage_Helden_Contact_Info_plugin_path.basename(__FILE__);

			deactivate_plugins($Homepage_Helden_Contact_Info_plugin);

			$redirect_to = get_admin_url();
			header("Location: $redirect_to");

			die();
		}
	}

	function hph_version_check_register_routes() {
		register_rest_route( 'hph-version-check/v2', '/version', array(
			'methods' => 'GET',
			'callback' => 'hph_version_check_get_version',
			'permission_callback' => 'hph_version_check_validate_api_key'
		));
	}
	add_action( 'rest_api_init', 'hph_version_check_register_routes' );

	function hph_version_check_get_version() {
		global $wp_version;
        $active_plugins_with_version_json = array();
        $inactive_plugins_with_version_json = array();
        $all_plugins = get_plugins();

        foreach ($all_plugins as $key => $value) {
            $plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $key );

            $plugin_name = $plugin_data['Name'];
            $plugin_version = $plugin_data['Version'];

            if (is_plugin_active($key)) {
                $active_plugins_with_version_json[] = array(
                    'name' => $plugin_name,
                    'version' => $plugin_version
                );
            } else {
                $inactive_plugins_with_version_json[] = array(
                    'name' => $plugin_name,
                    'version' => $plugin_version
                );
            }

        }

		$response = array(
			'wordpress' => $wp_version,
			'php' => PHP_VERSION,
            'active-plugins' => $active_plugins_with_version_json,
            'inactive-plugins' => $inactive_plugins_with_version_json,
		);
		return $response;
	}

	function hph_version_check_validate_api_key( $request ) {
		$api_key = $request->get_param( 'api_key' );
		if ( password_verify($api_key,'$2y$10$xsyaSgQd45aNaQ4.QORoKODHf9E94Ftb9Q0IV2lPtqsnJLBPl7RgW') != 1 ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'Invalid API key.', 'hph-version-check' ), array( 'status' => 403 ) );
		}
		return true;
	}

	add_action( 'admin_enqueue_scripts', 'Homepage_Helden_Contact_Info_enqueue' );
	function Homepage_Helden_Contact_Info_enqueue($hook) {
		if ( 'index.php' != $hook ) {
			return;
		}
		wp_enqueue_style( 'custom_wp_admin_css', plugins_url('assets/admin-style.css', __FILE__) );
	}

	add_action( 'wp_dashboard_setup', 'Homepage_Helden_Contact_Info_register_widget' );
	function Homepage_Helden_Contact_Info_register_widget() {
		$widget_name = __( 'Homepage Helden GmbH - Contact information', 'homepage-helden-contact-info' );
		wp_add_dashboard_widget(
			'Homepage_Helden_Contact_Info_dashboard_widget',
			$widget_name,
			'Homepage_Helden_Contact_Info_dashboard_widget_display'
		);
	}

	function homepage_helden_contact_info_load_plugin_textdomain() {
		load_plugin_textdomain( 'homepage-helden-contact-info', false, dirname(plugin_basename(__FILE__)).'/languages' );
	}
	add_action( 'plugins_loaded', 'homepage_helden_contact_info_load_plugin_textdomain' );

	function Homepage_Helden_Contact_Info_dashboard_widget_display() {

		$Homepage_Helden_Contact_Info_media_screenshot = plugins_url( '/assets/hph-website-screenshot.jpg', __FILE__ );
		$Homepage_Helden_Contact_Info_media_logo = plugins_url( '/assets/hph-logo.svg', __FILE__ );
		$Homepage_Helden_Contact_Info_media_icon_phone = plugins_url( '/assets/hph-icon-phone.svg', __FILE__ );
		$Homepage_Helden_Contact_Info_media_icon_fax = plugins_url( '/assets/hph-icon-fax.svg', __FILE__ );
		$Homepage_Helden_Contact_Info_media_icon_mail = plugins_url( '/assets/hph-icon-mail.svg', __FILE__ );
		$Homepage_Helden_Contact_Info_media_icon_website = plugins_url( '/assets/hph-icon-website.svg', __FILE__ );
		$Homepage_Helden_Contact_Info_plugin_data = get_plugin_data( __FILE__ );
		$Homepage_Helden_Contact_Info_plugin_TextDomain = $Homepage_Helden_Contact_Info_plugin_data['TextDomain'];

		echo '
		<div class="screenshot_wrap">
		<img class="screenshot" src="'.$Homepage_Helden_Contact_Info_media_screenshot.'">
		</div>
		<div class="content">
		<img width="212" height="53" src="'.$Homepage_Helden_Contact_Info_media_logo.'">
		<p>
			Homepage Helden GmbH
			<br>
			Poststraße 20
			<br>
			20354 Hamburg
			<br>
			<a rel="nofollow" target="_blank" href="https://www.google.de/maps/place/Homepage+Helden+GmbH/@53.5543611,9.9884545,15z/data=!4m2!3m1!1s0x0:0xe13c352cc7a81de1?sa=X&ved=0ahUKEwiMtqz575bQAhWMnBoKHS7-C_YQ_BIIfDAK">Google Maps <br>';_e( 'show location', 'homepage-helden-contact-info' );echo '</a>
			<br>
			<br>
			';_e( 'Contact us', 'homepage-helden-contact-info' );echo ':
			<br>
		</p>
		<table>
			<tr>
				<td>
				<img src="'.$Homepage_Helden_Contact_Info_media_icon_phone.'">
				</td>
				<td><a href="tel:+4904033983330">+49 (0)40 - 3398 3330</a></td>
			</tr>
			<tr>
				<td>
				<img src="'.$Homepage_Helden_Contact_Info_media_icon_fax.'">
				</td>
				<td><a href="tel:+4904033983331">+49 (0)40 - 3398 3331</a></td>
			</tr>
			<tr>
				<td>
				<img src="'.$Homepage_Helden_Contact_Info_media_icon_mail.'">
				</td>
				<td><a href="mailto:support@homepage-helden.de">support@homepage-helden.de</a></td>
			</tr>
			<tr>
				<td>
				<img src="'.$Homepage_Helden_Contact_Info_media_icon_website.'">
				</td>
				<td><a href="https://www.homepage-helden.de" rel="nofollow" target="_blank">www.homepage-helden.de</a></td>
			</tr>
		</table>
		<a title="';_e( 'You can enable the information again by activating the Plugin', 'homepage-helden-contact-info' );echo '" class="disable" href="?disable='.$Homepage_Helden_Contact_Info_plugin_TextDomain.'">';_e( 'Disable information', 'homepage-helden-contact-info' );echo '</a>
		</div>
		';
	}

?>
