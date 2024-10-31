<?php
/*
 * Plugin Name: Offline Updater
 * Version: 1.3
 * Description: For those sites can't update online, like some read-only cloud or recover-after-restart server, now you can just check out those updating files and easily deal with them, See Dashboard -> Offline Updater.
 * Plugin URI: https://www.xiaomac.com/offline-updater.html
 * Author: Link
 * Author URI: https://www.xiaomac.com
 */

class Offline_Updater {
	function __construct() {
		if ( is_multisite() )
			add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
		else
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	function admin_menu() {
		if(!is_super_admin()) return;
		add_submenu_page(
			'index.php',
			'Offline Updater',
			'Offline Updater',
			'manage_options',
			'offline-updater',
			array( $this, 'content_wrapper' )
		);
	}

	function content_wrapper() {
		require_once(ABSPATH . 'wp-admin/includes/update.php');
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');

		echo '<div class="wrap">';
		echo '<h2>Offline Updater</h2>';

		$updates = get_core_updates();
		echo '<h2>' . __( 'Update' ) . '</h2>';
		if ( !isset($updates[0]->response) || 'latest' == $updates[0]->response ) {
			echo '<p>' . __('You have the latest version of WordPress.') . '</p>';
		}else{
			foreach($updates as $update){
				echo '<p>';
				if(!empty($update->locale)) echo $update->locale . ' | ';
				echo '<a href="'.$update->download.'" target="_blank">'.$update->download.'</a>';
				if(!empty($update->packages->no_content)) echo ' | <a href="'.$update->packages->no_content.'" target="_blank">no_content.zip</a>';
				if(!empty($update->packages->new_bundled)) echo ' | <a href="'.$update->packages->new_bundled.'" target="_blank">new_bundled.zip</a>';
				echo '</p>';
			}
		}

		$plugins = get_plugin_updates();
		echo '<h2>' . __( 'Plugins' ) . '</h2>';
		if ( empty( $plugins ) ) {
			echo '<p>' . __( 'Your plugins are all up to date.' ) . '</p>';
		}else{
			foreach($plugins as $plugin){
				printf('<p><a href="%2$s" target="_blank">%1$s</a> | <a href="%3$s" target="_blank">%3$s</a></p>', $plugin->Name, $plugin->update->url, $plugin->update->package);
			}
		}

		echo '<h2>' . __( 'Themes' ) . '</h2>';
		$themes = get_theme_updates();
		if ( empty( $themes ) ) {
			echo '<p>' . __( 'Your themes are all up to date.' ) . '</p>';
		}else{
			foreach($themes as $theme){
				$update = $theme->update;
				printf('<p>%1$s | <a href="%2$s" target="_blank">%2$s</a> </p>', $update['theme'], $update['package']);
			}
		}

		echo '<h2>' . __( 'Translations' ) . '</h2>';
		$language_updates = wp_get_translation_updates();
		if ( ! $language_updates ) {
			echo '<p>' . __( 'Your translations are all up to date.' ) . '</p>';
		}else{
			foreach($language_updates as $language_update){
				printf('<p>[%1$s] %2$s <a href="%3$s" target="_blank">%3$s</a> </p>', $language_update->type, $language_update->slug, $language_update->package);
			}
		}
		echo '</div>';
	}
}

new Offline_Updater();
