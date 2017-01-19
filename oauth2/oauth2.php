<?php
/**
 * Created by Regis TEDONE.
 * atcg-partners.
 * Date: 14/01/17
 * Time: 13:09
 */

class oauth2 {

    private static $base_url;
    private static $client_id;
    private static $client_secret;

    private static function init() {
        $options = get_option( 'wpcyn_settings' );
        self::$base_url = $options['wpcyn_url_server'];
        self::$client_id = $options['wpcyn_client_id'];
        self::$client_secret = $options['wpcyn_client_secret'];
    }

    /*
     * Call Restfull service
     * @param string $url 'Request URL'
     * @param string $method 'POST or GET'
     * @param string $token 'Access token'
     * @param array $arguments 'Passed arguments'
     * @return array $response
     */
    private function call($url, $method, $token, $arguments) {

        $method = strtoupper($method);

        //   Build query with received arguments
        $data_arguments = http_build_query($arguments);

        // Init CURL Request
        $handle = curl_init();

        if( $method == 'POST' ) {
            //   POST Request
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data_arguments);

        } elseif( $method == 'GET' ) {
            //  GET Request
            curl_setopt($handle, CURLOPT_HTTPGET, true);
            $url .= '?' . $data_arguments;
        }

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        if(!empty($token)) {
            curl_setopt($handle, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/x-www-form-urlencoded',
                                'Authorization:Bearer ' . $token
            ));
        }

        curl_setopt($handle, CURLOPT_URL, $url);

        //Exec request and Convert response to UTF-8
        $response = iconv("ISO-8859-15", "utf-8", curl_exec($handle));
        curl_close($handle);

        return json_decode($response);
    }


    /*
     * Get a token
     *
     * @return array $token_response
     */
    private function getToken() {

        $oauth2_token_arguments = [
            'grant_type' => 'client_credentials',
            'client_id' => self::$client_id,
            'client_secret' => self::$client_secret
        ];
        $url_token = self::$base_url . '/api/token/';
        $_SESSION['cryostemToken'] = self::call($url_token, 'POST', '', $oauth2_token_arguments);

    }

    /*
     * Get an access token from client cookie if valid or request a new one
     *
     * @return string $token
     */
    public static function determineToken() {

        self::init();

        if(!isset($_COOKIE['cryostemToken'])) {

            //Get token from server
            self::getToken();
            $token = $_SESSION['cryostemToken']->access_token;

        } else {

            //Get token from cookie
            $token = $_COOKIE['cryostemToken'];

        }
        return $token;
    }


    /*
     * Get Rest data
     * @param string $token 'Access token'
     * @param string $endpoint 'Endpoint to request'
     * @param array $parameters 'Passed arguments'
     * @return array $data
     */
    public static function getData($token, $endpoint, $parameters) {

        $url_data = self::$base_url . '/api/' . $endpoint . '/';
        $data = self::call($url_data, 'GET', $token, $parameters);
        return $data;

    }
}