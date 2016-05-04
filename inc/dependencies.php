<?php
/**
 * Detection de CMB2. Identique dans tous les plugins.
 */
if ( !function_exists( 'cmb2_detection' ) ) {
    function cmb2_detection() {
        if ( !defined( 'CMB2_LOADED' ) ) {
            add_action( 'admin_notices', 'cmb2_notice' );
        }
    }

    function cmb2_notice() {
        if ( current_user_can( 'activate_plugins' ) ) {
            echo '<div class="error message"><p>' . __( '<a href="' . esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=cmb2&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox">CMB2 plugin</a> or <a href="https://github.com/yozzi/startup-reloaded" target="_blank">StartUp Reloaded theme</a> must be active to use custom metaboxes.', 'startup-cpt-timeline' ) . '</p></div>';
        }
    }

    add_action( 'init', 'cmb2_detection' );
}

/**
 * Detection de Shortcake. Identique dans tous les plugins.
 */
if ( !function_exists( 'shortcode_ui_detection' ) ) {
    function shortcode_ui_detection() {
        if ( !function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
            add_action( 'admin_notices', 'shortcode_ui_notice' );
        }
    }

    function shortcode_ui_notice() {
        if ( current_user_can( 'activate_plugins' ) ) {
            echo '<div class="error message"><p>' . __( '<a href="' . esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=shortcode-ui&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox">Shortcake plugin</a> must be active to use fast shortcodes.', 'startup-cpt-timeline' ) . '</p></div>';
        }
    }

    add_action( 'init', 'shortcode_ui_detection' );
}