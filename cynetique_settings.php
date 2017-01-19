<?php
/**
 * Created by Regis TEDONE.
 * atcg-partners.
 * Date: 16/01/17
 * Time: 21:02
 */

function wpcyn_add_admin_menu() {

    add_options_page( 'cynetique1', 'Cryostem REST configuration', 'manage_options', 'wpcyn_settings', 'wpcyn_options_page' );
}
add_action( 'admin_menu', 'wpcyn_add_admin_menu' );

function wpcyn_settings_init() {

    register_setting( 'pluginPage', 'wpcyn_settings' );

    add_settings_section(
        'wpcyn_pluginPage_section',
        __( 'Paramètres du serveur REST', 'serveur Cryostem' ),
        'wpcyn_settings_section_callback',
        'pluginPage'
    );

    add_settings_field(
        'wpcyn_url_server',
        __( 'Url du serveur', 'serveur Cryostem' ),
        'wpcyn_url_server_render',
        'pluginPage',
        'wpcyn_pluginPage_section'
    );

    add_settings_field(
        'wpcyn_client_id',
        __( 'Client ID', 'serveur Cryostem' ),
        'wpcyn_client_id_render',
        'pluginPage',
        'wpcyn_pluginPage_section'
    );

    add_settings_field(
        'wpcyn_client_secret',
        __( 'Client Secret', 'serveur Cryostem' ),
        'wpcyn_client_secret_render',
        'pluginPage',
        'wpcyn_pluginPage_section'
    );
}
add_action( 'admin_init', 'wpcyn_settings_init' );

function wpcyn_url_server_render() {
    $options = get_option( 'wpcyn_settings' );
    ?>
    <input type='text' size="50" placeholder="https://..." name='wpcyn_settings[wpcyn_url_server]' value='<?php echo $options['wpcyn_url_server']; ?>'>
    <?php
}

function wpcyn_client_id_render() {
    $options = get_option( 'wpcyn_settings' );
    ?>
    <input type='text' size="50" placeholder="paul_bismuth" name='wpcyn_settings[wpcyn_client_id]' value='<?php echo $options['wpcyn_client_id']; ?>'>
    <?php
}

function wpcyn_client_secret_render() {
    $options = get_option( 'wpcyn_settings' );
    ?>
    <input type='text' size="50" name='wpcyn_settings[wpcyn_client_secret]' value='<?php echo $options['wpcyn_client_secret']; ?>'>
    <?php
}

function wpcyn_settings_section_callback() {
    echo __( 'Veuillez saisir ici les paramètres du serveur de données Cryostem', 'serveur Cryostem' );
}


function wpcyn_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Réglages</h2>

        <?php
        settings_fields( 'pluginPage' );
        do_settings_sections( 'pluginPage' );
        submit_button();
        ?>
    </form>
    <?php
}