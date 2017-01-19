<?php
/**
 * Plugin Name: Cynétique Cryostem
 * Plugin URI: http://www.atcg-partners.com
 * Description: Cynétique de prélévements Cryostem
 * Version: 1.0.0
 * Author: Regis TEDONE
 * Author URI: mailto:rt@atcg-partners.com
 * License: GPL2
 */

require_once( plugin_dir_path( __FILE__) . 'oauth2/oauth2.php' );
require_once( plugin_dir_path( __FILE__) . 'cynetique_settings.php' );
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

/**
 * Set token in client cookie
 *
 * @return void
 */
function setCryostemTokenCookie() {

    $current_time = time();
    if(isset($_SESSION['cryostemToken']) && !empty($_SESSION['cryostemToken'])) {

        $token = $_SESSION['cryostemToken']->access_token;
        $expires = $current_time + $_SESSION['cryostemToken']->expires_in -100;
        setcookie('cryostemToken', $token, $expires, COOKIEPATH, COOKIE_DOMAIN);

    } else {
        unset($_COOKIE['cryostemToken']);
        setcookie("cryostemToken", "", $current_time-3600);
    }
}
add_action( 'init', 'setCryostemTokenCookie' );

/**
 * Create plugin settings links (Réglages)
 *
 * @return array $links
 */
function cynetic_settings_action_links( $links ) {
    array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=wpcyn_settings' ) . '">' . __( 'Configuration' ) . '</a>' );
    return $links;
}
add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), 'cynetic_settings_action_links', 10, 2 );


/**
 * Register CSS and JS for this plugin
 *
 * @return void
 */
function cynetic_init() {

    $pageIds = [1785, 1936];
    if(in_array(get_the_ID(), $pageIds)) {

        $popovercss = date("ymd-Gis", filemtime( plugin_dir_path(__FILE__) . 'css/jquery.webui-popover.min.css'));
        wp_register_style('popovercss', plugins_url('css/jquery.webui-popover.min.css', __FILE__), false, $popovercss);
        wp_enqueue_style('popovercss');

        $cyneticcss = date("ymd-Gis", filemtime( plugin_dir_path(__FILE__) . 'css/cynetic.css'));
        wp_register_style('cyneticcss', plugins_url('css/cynetic.css', __FILE__), false, $cyneticcss);
        wp_enqueue_style('cyneticcss');

        $popoverjs = date("ymd-Gis", filemtime( plugin_dir_path(__FILE__) . 'js/jquery.webui-popover.min.js'));
        wp_enqueue_script('popoverjs', plugins_url('js/jquery.webui-popover.min.js', __FILE__), array(), $popoverjs);

        $cyneticjs = date("ymd-Gis", filemtime( plugin_dir_path(__FILE__) . 'js/cynetic.js'));
        wp_enqueue_script('cyneticjs', plugins_url('js/cynetic.js', __FILE__), array(), $cyneticjs, true);

        //Register Ajax url
        wp_localize_script('cyneticjs', 'ajaxurl', admin_url( 'admin-ajax.php' ) );

    }
}

add_action('wp_enqueue_scripts', 'cynetic_init');


/**
 * Main plugin function called by a shortcode.
 *
 * @return string $content
 */

function display_cynetic($attributes=[]) {

    $attributes = array_change_key_case((array)$attributes, CASE_LOWER);
    $lang = $attributes['lang'];

    $content = '<hr /><h3>' . __( 'Type de greffe', 'cynetique-labels' ) . '</h3>';
    $content .= __( 'Veuillez sélectionner un type de greffe', 'cynetique-labels' ) . '<br />';
    $content .= '<form id="type_selector_form" autocomplete="off">';
    $content .= '<label id="graft1_label"><input name="type_greffe" id="graft1" data-id="1" checked type="radio"> '. __( 'Donneur non apparenté (DV)', 'cynetique-labels' ) .'</label>';
    $content .= '<label id="graft2_label"><input name="type_greffe" id="graft2" data-id="2" type="radio"> '. __( 'Géno-identique', 'cynetique-labels' ) .'</label>';
    $content .= '<label id="graft3_label"><input name="type_greffe" id="graft3" data-id="3" type="radio"> '. __( 'Haplo-identique', 'cynetique-labels' ) .'</label>';
    $content .= '<label id="graft4_label"><input name="type_greffe" id="graft4" data-id="4" type="radio"> '. __( 'Unité de sang placentaire (USP)', 'cynetique-labels' ) .'</label>';
    $content .= '</form>';
    $content .= '<br />';

    $svg_path = plugin_dir_path( __FILE__) . 'svg/cynetique_'.$lang.'.svg';
    $svg_legend_path = plugin_dir_path( __FILE__) . 'svg/cynetique_legend_'.$lang.'.svg';
    $svg_content = file_get_contents($svg_path);
    $svg_content .= file_get_contents($svg_legend_path);
    $content .= $svg_content;
    $content .= '<script>var restData={};</script>';
    $content .= '<div id="dataContainer"></div>';

    return $content;
}
add_shortcode( 'cynetique', 'display_cynetic' );

/**
 * Ajax callback function
 *
 * @return string $restData
 */
function getRestData_callback() {

    $graftTypeLabels = [ '',
        __( 'DV', 'cynetique-labels' ),
        __( 'Géno-identique', 'cynetique-labels' ),
        __( 'Haplo-identique', 'cynetique-labels' ),
        __( 'USP', 'cynetique-labels' )
    ];

    $graftTypeId = intval( $_POST['graftTypeId'] );

    $token = oauth2::determineToken();

    //help
    //graft_types
        //Donneur non apparenté (DV)
        //Géno-identique
        //Haplo-identique
        //Unité de sang placentaire (USP)
    //sample_number
        //D0
        //R0
        //a1
        //a2
        //c1
        //c2
        //s1
        //s2
        //s3
    //tube_number
    //specimen_collection_number

    $sample_number_D0 = oauth2::getData($token, 'sample_number', array('graftTypeId'=>$graftTypeId, 'visitTypeName'=>'D0'));
    $sample_number_R0 = oauth2::getData($token, 'sample_number', array('graftTypeId'=>$graftTypeId, 'visitTypeName'=>'R0'));
    $sample_number_a1 = oauth2::getData($token, 'sample_number', array('graftTypeId'=>$graftTypeId, 'visitTypeName'=>'a1'));
    $sample_number_a2 = oauth2::getData($token, 'sample_number', array('graftTypeId'=>$graftTypeId, 'visitTypeName'=>'a2'));
    $sample_number_c1 = oauth2::getData($token, 'sample_number', array('graftTypeId'=>$graftTypeId, 'visitTypeName'=>'c1'));
    $sample_number_c2 = oauth2::getData($token, 'sample_number', array('graftTypeId'=>$graftTypeId, 'visitTypeName'=>'c2'));
    $sample_number_s1 = oauth2::getData($token, 'sample_number', array('graftTypeId'=>$graftTypeId, 'visitTypeName'=>'s1'));
    $sample_number_s2 = oauth2::getData($token, 'sample_number', array('graftTypeId'=>$graftTypeId, 'visitTypeName'=>'s2'));
    $sample_number_s3 = oauth2::getData($token, 'sample_number', array('graftTypeId'=>$graftTypeId, 'visitTypeName'=>'s3'));

    $restData = '<script>';

    if(empty($sample_number_D0->data[0])) {
        $restData .= "restData.D0='<strong>D0 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . __( 'N/D', 'cynetique-labels' ) . "';";
    } else {
        $restData .= "restData.D0='<strong>D0 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . $sample_number_D0->data[0]->SA_TY_NA_1210592326 . " : " . $sample_number_D0->data[0]->SAMPLE_NUMBER. "<br />" . $sample_number_D0->data[1]->SA_TY_NA_1210592326 . " : " . $sample_number_D0->data[1]->SAMPLE_NUMBER. "<br />" . $sample_number_D0->data[2]->SA_TY_NA_1210592326 . " : " . $sample_number_D0->data[2]->SAMPLE_NUMBER. "';";
    }

    if(empty($sample_number_R0->data[0])) {
        $restData .= "restData.R0='<strong>R0 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . __( 'N/D', 'cynetique-labels' ) . "';";
    } else {
        $restData .= "restData.R0='<strong>R0 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . $sample_number_R0->data[0]->SA_TY_NA_1210592326 . " : " . $sample_number_R0->data[0]->SAMPLE_NUMBER. "<br />" . $sample_number_R0->data[1]->SA_TY_NA_1210592326 . " : " . $sample_number_R0->data[1]->SAMPLE_NUMBER. "<br />" . $sample_number_R0->data[2]->SA_TY_NA_1210592326 . " : " . $sample_number_R0->data[2]->SAMPLE_NUMBER. "';";
    }

    if(empty($sample_number_a1->data[0])) {
        $restData .= "restData.a1='<strong>a1 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . __( 'N/D', 'cynetique-labels' ) . "';";
    } else {
        $restData .= "restData.a1='<strong>a1 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . $sample_number_a1->data[0]->SA_TY_NA_1210592326 . " : " . $sample_number_a1->data[0]->SAMPLE_NUMBER. "<br />" . $sample_number_a1->data[1]->SA_TY_NA_1210592326 . " : " . $sample_number_a1->data[1]->SAMPLE_NUMBER. "<br />" . $sample_number_a1->data[2]->SA_TY_NA_1210592326 . " : " . $sample_number_a1->data[2]->SAMPLE_NUMBER. "';";
    }

    if(empty($sample_number_a2->data[0])) {
        $restData .= "restData.a2='<strong>a2 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . __( 'N/D', 'cynetique-labels' ) . "';";
    } else {
        $restData .= "restData.a2='<strong>a2 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . $sample_number_a2->data[0]->SA_TY_NA_1210592326 . " : " . $sample_number_a2->data[0]->SAMPLE_NUMBER. "<br />" . $sample_number_a2->data[1]->SA_TY_NA_1210592326 . " : " . $sample_number_a2->data[1]->SAMPLE_NUMBER. "<br />" . $sample_number_a2->data[2]->SA_TY_NA_1210592326 . " : " . $sample_number_a2->data[2]->SAMPLE_NUMBER. "';";
    }

    if(empty($sample_number_c1->data[0])) {
        $restData .= "restData.c1='<strong>c1 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . __( 'N/D', 'cynetique-labels' ) . "';";
    } else {
        $restData .= "restData.c1='<strong>c1 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . $sample_number_c1->data[0]->SA_TY_NA_1210592326 . " : " . $sample_number_c1->data[0]->SAMPLE_NUMBER. "<br />" . $sample_number_c1->data[1]->SA_TY_NA_1210592326 . " : " . $sample_number_c1->data[1]->SAMPLE_NUMBER. "<br />" . $sample_number_c1->data[2]->SA_TY_NA_1210592326 . " : " . $sample_number_c1->data[2]->SAMPLE_NUMBER. "';";
    }

    if(empty($sample_number_c2->data[0])) {
        $restData .= "restData.c2='<strong>c2 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . __( 'N/D', 'cynetique-labels' ) . "';";
    } else {
        $restData .= "restData.c2='<strong>c2 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . $sample_number_c2->data[0]->SA_TY_NA_1210592326 . " : " . $sample_number_c2->data[0]->SAMPLE_NUMBER. "<br />" . $sample_number_c2->data[1]->SA_TY_NA_1210592326 . " : " . $sample_number_c2->data[1]->SAMPLE_NUMBER. "<br />" . $sample_number_c2->data[2]->SA_TY_NA_1210592326 . " : " . $sample_number_c2->data[2]->SAMPLE_NUMBER. "';";
    }

    if(empty($sample_number_s1->data[0])) {
        $restData .= "restData.s1='<strong>s1 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . __( 'N/D', 'cynetique-labels' ) . "';";
    } else {
        $restData .= "restData.s1='<strong>s1 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . $sample_number_s1->data[0]->SA_TY_NA_1210592326 . " : " . $sample_number_s1->data[0]->SAMPLE_NUMBER. "<br />" . $sample_number_s1->data[1]->SA_TY_NA_1210592326 . " : " . $sample_number_s1->data[1]->SAMPLE_NUMBER. "<br />" . $sample_number_s1->data[2]->SA_TY_NA_1210592326 . " : " . $sample_number_s1->data[2]->SAMPLE_NUMBER. "';";
    }

    if(empty($sample_number_s2->data[0])) {
        $restData .= "restData.s2='<strong>s2 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . __( 'N/D', 'cynetique-labels' ) . "';";
    } else {
        $restData .= "restData.s2='<strong>s2 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . $sample_number_s2->data[0]->SA_TY_NA_1210592326 . " : " . $sample_number_s2->data[0]->SAMPLE_NUMBER. "<br />" . $sample_number_s2->data[1]->SA_TY_NA_1210592326 . " : " . $sample_number_s2->data[1]->SAMPLE_NUMBER. "<br />" . $sample_number_s2->data[2]->SA_TY_NA_1210592326 . " : " . $sample_number_s2->data[2]->SAMPLE_NUMBER. "';";
    }

    if(empty($sample_number_s3->data[0])) {
        $restData .= "restData.s3='<strong>s3 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . __( 'N/D', 'cynetique-labels' ) . "';";
    } else {
        $restData .= "restData.s3='<strong>s3 - " . $graftTypeLabels[$graftTypeId] . "</strong><hr />" . $sample_number_s3->data[0]->SA_TY_NA_1210592326 . " : " . $sample_number_s3->data[0]->SAMPLE_NUMBER. "<br />" . $sample_number_s3->data[1]->SA_TY_NA_1210592326 . " : " . $sample_number_s3->data[1]->SAMPLE_NUMBER. "<br />" . $sample_number_s3->data[2]->SA_TY_NA_1210592326 . " : " . $sample_number_s3->data[2]->SAMPLE_NUMBER. "';";
    }

    $restData .= "WebuiPopovers.updateContent('#D0',restData.D0);";
    $restData .= "WebuiPopovers.updateContent('#R0',restData.R0);";
    $restData .= "WebuiPopovers.updateContent('#a1',restData.a1);";
    $restData .= "WebuiPopovers.updateContent('#a2',restData.a2);";
    $restData .= "WebuiPopovers.updateContent('#c1',restData.c1);";
    $restData .= "WebuiPopovers.updateContent('#c2',restData.c2);";
    $restData .= "WebuiPopovers.updateContent('#s1',restData.s1);";
    $restData .= "WebuiPopovers.updateContent('#s2',restData.s2);";
    $restData .= "WebuiPopovers.updateContent('#s3',restData.s3);";
    $restData .= '</script>';

    echo $restData;

    wp_die();
}
add_action( 'wp_ajax_getRestData', 'getRestData_callback' );
add_action( 'wp_ajax_nopriv_getRestData', 'getRestData_callback' );

/**
 * Adds plugin localization
 * Domain: cynetique-labels
 *
 * @return void
 */
function localization() {
    load_plugin_textdomain( 'cynetique-labels', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
}
add_action( 'plugins_loaded', 'localization');