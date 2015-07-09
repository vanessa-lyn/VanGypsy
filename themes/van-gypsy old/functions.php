<?php

    add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
    function theme_enqueue_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
        //THIS IS SUPPOSED TO BE FOR ADDING IN THE CHILD STYLES BUT IT DUPLICATING THEM. WILL HOLD OFF ADDING THIS IN NOW UNTIL I FIND WHERE THE DUPLICATION IS HAPPENING
        // wp_enqueue_style( 'child-style',
        //     get_stylesheet_directory_uri() . '/style.css',
        //     array('parent-style')
        // );
    }


    // add_action( 'wp_enqueue_scripts', 'retina_support_enqueue_scripts' );
    /**
     * Enqueueing retina.js
     *
     * This function is attached to the 'wp_enqueue_scripts' action hook.
     */
    function retina_support_enqueue_scripts() {
        wp_enqueue_script( 'retina_js', get_stylesheet_directory_uri() . '/js/retina.min.js', '', '', true );
    }


    // add_action( 'wp_enqueue_scripts', 'child_functions_enqueue_scripts' );
    // function child_functions_enqueue_scripts() {
    //     wp_enqueue_script( 'child_functions', get_stylesheet_directory_uri() . '/js/child-functions.js', '', '', true );
    // }


    // // adding in ability to upload svgs through wordpress
    // function cc_mime_types($mimes) {
    //   $mimes['svg'] = 'image/svg+xml';
    //   return $mimes;
    // }
    // add_filter('upload_mimes', 'cc_mime_types');
    // //////////////



    // // Custom Function to Include
    // function favicon_link() {
    //     echo '<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />' . "\n";
    // }
    // add_action( 'wp_head', 'favicon_link' );


    // function child_functions() {
    //     // wp_enqueue_script( 'twentyfourteen-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20140616', true );
    // }


    // // This theme uses wp_nav_menu() in two locations.
    // register_nav_menus( array(
    //     'primary'   => __( 'Top primary menu', 'twentyfourteen' ),
    //     'social'    => __( 'Social nav', 'twentyfourteen' ),
    //     'footer' => __( 'Footer menu', 'twentyfourteen' ),
    // ) );

    // //Menu Social Icons Plugin: for the right social icons
    // add_filter( 'storm_social_icons_type', create_function( '', 'return "icon-sign";' ) );