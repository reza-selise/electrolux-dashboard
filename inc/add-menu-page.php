<?php 
/**
 * This will use to add admin menu items
 */

function el_add_settings_menu_items(){

    add_menu_page(
        "Event Dashboard",  // page_title
        "Event Dashboard",            //  menu_title
        'administrator',    // Capability
        'el-event-dashboard', // menu slug
        'el_dashboard_event_page_render', // callback
    );


    // consultation page 
    add_submenu_page(
        'el-event-dashboard',   // parent page slug
        'Consultation',         // page title
        "Consultation",         // menu title
        'administrator',        // Capability
        'consultation',
        'el_dashboard_consultation_render', // callback function  
        1
    );


    // home consultation page 
    add_submenu_page(
        'el-event-dashboard',   // parent page slug
        'Home Consultation',         // page title
        "Home Consultation",         // menu title
        'administrator',        // Capability
        'home-consultation',
        'el_dashboard_home_consultation_render', // callback function  
        2
    );

    // management page

    add_submenu_page(
        'el-event-dashboard',   // parent page slug
        'Management',         // page title
        "Management",         // menu title
        'administrator',        // Capability
        'management',
        'el_dashboard_management_render', // callback function  
        3
    );
    
}
add_action('admin_menu', 'el_add_settings_menu_items', 1);

function el_dashboard_event_page_render(){
    ?>
    <h1>Event main page</h1>
    <?php
}

function el_dashboard_consultation_render(){
    ?>
    <h1>Consultation </h1>
    <?php
}


function el_dashboard_home_consultation_render(){
    ?>
    <h1>Home Consultation</h1>
    <?php
}




function el_dashboard_management_render(){
    ?>
    <h1>Management </h1>
    <?php
}
