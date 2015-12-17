<?php
/*
Plugin Name: StartUp CPT Timeline
Description: Le plugin pour activer le Custom Post Timeline
Author: Yann Caplain
Version: 1.2.0
Text Domain: startup-cpt-timeline
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//GitHub Plugin Updater
function startup_cpt_timeline_updater() {
	include_once 'lib/updater.php';
	//define( 'WP_GITHUB_FORCE_UPDATE', true );
	if ( is_admin() ) {
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'startup-cpt-timeline',
			'api_url' => 'https://api.github.com/repos/yozzi/startup-cpt-timeline',
			'raw_url' => 'https://raw.github.com/yozzi/startup-cpt-timeline/master',
			'github_url' => 'https://github.com/yozzi/startup-cpt-timeline',
			'zip_url' => 'https://github.com/yozzi/startup-cpt-timeline/archive/master.zip',
			'sslverify' => true,
			'requires' => '3.0',
			'tested' => '3.3',
			'readme' => 'README.md',
			'access_token' => '',
		);
		new WP_GitHub_Updater( $config );
	}
}

//add_action( 'init', 'startup_cpt_timeline_updater' );

//CPT
function startup_cpt_timeline() {
	$labels = array(
		'name'                => _x( 'Timeline', 'Post Type General Name', 'startup-cpt-timeline' ),
		'singular_name'       => _x( 'Timeline', 'Post Type Singular Name', 'startup-cpt-timeline' ),
		'menu_name'           => __( 'Timeline', 'startup-cpt-timeline' ),
		'name_admin_bar'      => __( 'Timeline', 'startup-cpt-timeline' ),
		'parent_item_colon'   => __( 'Parent Item:', 'startup-cpt-timeline' ),
		'all_items'           => __( 'All Items', 'startup-cpt-timeline' ),
		'add_new_item'        => __( 'Add New Item', 'startup-cpt-timeline' ),
		'add_new'             => __( 'Add New', 'startup-cpt-timeline' ),
		'new_item'            => __( 'New Item', 'startup-cpt-timeline' ),
		'edit_item'           => __( 'Edit Item', 'startup-cpt-timeline' ),
		'update_item'         => __( 'Update Item', 'startup-cpt-timeline' ),
		'view_item'           => __( 'View Item', 'startup-cpt-timeline' ),
		'search_items'        => __( 'Search Item', 'startup-cpt-timeline' ),
		'not_found'           => __( 'Not found', 'startup-cpt-timeline' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'startup-cpt-timeline' )
	);
	$args = array(
		'label'               => __( 'timeline', 'startup-cpt-timeline' ),
		'description'         => __( '', 'startup-cpt-timeline' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'revisions' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-clock',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
        'capability_type'     => array('timeline','timelines'),
        'map_meta_cap'        => true
	);
	register_post_type( 'timeline', $args );

}

add_action( 'init', 'startup_cpt_timeline', 0 );

//Flusher les permalink à l'activation du plugin pour qu'ils fonctionnent sans mise à jour manuelle
function startup_cpt_timeline_rewrite_flush() {
    startup_cpt_timeline();
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'startup_cpt_timeline_rewrite_flush' );

// Capabilities
function startup_cpt_timeline_caps() {
	$role_admin = get_role( 'administrator' );
	$role_admin->add_cap( 'edit_timeline' );
	$role_admin->add_cap( 'read_timeline' );
	$role_admin->add_cap( 'delete_timeline' );
	$role_admin->add_cap( 'edit_others_timelines' );
	$role_admin->add_cap( 'publish_timelines' );
	$role_admin->add_cap( 'edit_timelines' );
	$role_admin->add_cap( 'read_private_timelines' );
	$role_admin->add_cap( 'delete_timelines' );
	$role_admin->add_cap( 'delete_private_timelines' );
	$role_admin->add_cap( 'delete_published_timelines' );
	$role_admin->add_cap( 'delete_others_timelines' );
	$role_admin->add_cap( 'edit_private_timelines' );
	$role_admin->add_cap( 'edit_published_timelines' );
}

register_activation_hook( __FILE__, 'startup_cpt_timeline_caps' );

// Metaboxes
function startup_cpt_timeline_meta() {
    require get_template_directory() . '/inc/font-awesome.php';
    
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_startup_cpt_timeline_';

	$cmb_box = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'Timeline details', 'startup-cpt-timeline' ),
		'object_types'  => array( 'timeline' )
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Date', 'startup-cpt-timeline' ),
		'id'         => $prefix . 'date',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Icon', 'startup-cpt-timeline' ),
        'id'               => $prefix . 'icon',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $font_awesome
    ) );
    
    $cmb_box->add_field( array(
        'name'    => __( 'Dot color', 'startup-cpt-timeline' ),
        'id'      => $prefix . 'color',
        'type'    => 'colorpicker',
        'default' => '#fff'
    ) );
    
    // Pull all the pages into an array
    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'hierarchical' => 0
    ); 
    
	$pages = array();
	$pages_obj = get_pages( $args );
	foreach ($pages_obj as $page) {
		$pages[$page->ID] = $page->post_title;
	}
    
    $cmb_box->add_field( array(
        'name'             => __( 'Date page', 'startup-cpt-timeline' ),
        'id'               => $prefix . 'page',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $pages
    ) );
}

add_action( 'cmb2_admin_init', 'startup_cpt_timeline_meta' );

// Shortcode
function startup_cpt_timeline_shortcode( $atts ) {

	// Attributes
    $atts = shortcode_atts(array(
            'bg' => ''
        ), $atts);
    
	// Code
        ob_start();
        require get_template_directory() . '/template-parts/content-timeline.php';
        return ob_get_clean();    
}
add_shortcode( 'timeline', 'startup_cpt_timeline_shortcode' );

// Shortcode UI
/**
 * Detecion de Shortcake. Identique dans tous les plugins.
 */
if ( !function_exists( 'shortcode_ui_detection' ) ) {
    function shortcode_ui_detection() {
        if ( !function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
            add_action( 'admin_notices', 'shortcode_ui_notice' );
        }
    }

    function shortcode_ui_notice() {
        if ( current_user_can( 'activate_plugins' ) ) {
            echo '<div class="error message"><p>' . __( 'Shortcake plugin must be active to use fast shortcodes.', 'startup-cpt-timeline' ) . '</p></div>';
        }
    }

add_action( 'init', 'shortcode_ui_detection' );
}

function startup_cpt_timeline_shortcode_ui() {

    shortcode_ui_register_for_shortcode(
        'timeline',
        array(
            'label' => esc_html__( 'Timeline', 'startup-cpt-timeline' ),
            'listItemImage' => 'dashicons-clock',
            'attrs' => array(
                array(
                    'label' => esc_html__( 'Background', 'startup-cpt-timeline' ),
                    'attr'  => 'bg',
                    'type'  => 'color',
                ),
            ),
        )
    );
};
if ( function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
    add_action( 'init', 'startup_cpt_timeline_shortcode_ui');
}

// Enqueue scripts and styles.
function startup_cpt_timeline_scripts() {
    wp_enqueue_style( 'startup-cpt-timeline-style', plugins_url( '/css/startup-cpt-timeline.css', __FILE__ ), array( ), false, 'all' );
    wp_enqueue_script( 'modernizr', plugins_url( '/js/modernizr.js', __FILE__ ), array( ), '', 'false' );
}

add_action( 'wp_enqueue_scripts', 'startup_cpt_timeline_scripts' );

// Add code to footer
function startup_cpt_timeline_footer() { ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
        var timelineBlocks = jQuery('.cd-timeline-block'),
            offset = 0.8;

        //hide timeline blocks which are outside the viewport
        hideBlocks(timelineBlocks, offset);

        //on scolling, show/animate timeline blocks when enter the viewport
        jQuery(window).on('scroll', function(){
            (!window.requestAnimationFrame) 
                ? setTimeout(function(){ showBlocks(timelineBlocks, offset); }, 100)
                : window.requestAnimationFrame(function(){ showBlocks(timelineBlocks, offset); });
        });

        function hideBlocks(blocks, offset) {
            blocks.each(function(){
                ( jQuery(this).offset().top > jQuery(window).scrollTop()+jQuery(window).height()*offset ) && jQuery(this).find('.cd-timeline-img, .cd-timeline-content').addClass('is-hidden');
            });
        }

        function showBlocks(blocks, offset) {
            blocks.each(function(){
                ( jQuery(this).offset().top <= jQuery(window).scrollTop()+jQuery(window).height()*offset && jQuery(this).find('.cd-timeline-img').hasClass('is-hidden') ) && jQuery(this).find('.cd-timeline-img, .cd-timeline-content').removeClass('is-hidden').addClass('bounce-in');
            });
        }
    });
    </script>
<?php }

add_action( 'wp_footer', 'startup_cpt_timeline_footer' );
?>