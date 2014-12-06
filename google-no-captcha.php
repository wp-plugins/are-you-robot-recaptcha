<?php
/*
Plugin Name: Are you robot? google recaptcha for wordpress
Plugin URI: http://www.idiotinside.com
Description:
Version: 0.1
Author: Suresh Kumar
Author URI: http://profiles.wordpress.org/sureshdsk/
*/
defined( 'ABSPATH' ) OR exit;
function nocaptcha_login_recaptcha_page() {
    if (!current_user_can('manage_options')) {
        wp_die( __('Access denied') );
    }
    if (isset($_POST['process'])) {
        update_option('nocaptcha_login_recaptcha_options', $_POST['nocaptcha_login_recaptcha_options']);
        _e('<div id="message" class="updated fade"><p>Options saved...</p></div>');
    }
    $opt = get_option('nocaptcha_login_recaptcha_options');
    ?>
    <div class="wrap">
        <h2>Google reCAPTCHA</h2>
        <p><a href="https://www.google.com/recaptcha/admin" target="_blank">Get the reCAPTCHA keys here</a>.</p>
        <p>Both keys must be filled to enable WP reCAPTCHA in login page.</p>
        <form name="form1" method="post" action="">

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Site Key (Public)</th>
                    <td>
                        <input type="text" name="nocaptcha_login_recaptcha_options[site_key]" value="<?php echo trim($opt['site_key']); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Secret Key (Private)</th>
                    <td>
                        <input type="text" name="nocaptcha_login_recaptcha_options[secret_key]" value="<?php echo trim($opt['secret_key']); ?>" />
                    </td>
                </tr>
            </table>
            <p class="submit"> <input type="hidden" name="process" value="1" /> <input type="submit" class="button-primary" value="Save Options" /></p>
    </div>
<?php

}


    function nocaptcha_login_recaptcha_menu_pages() {
        add_options_page('Google New reCAPTCHA', 'Google New reCAPTCHA', 'manage_options', 'google-no-captcha', 'nocaptcha_login_recaptcha_page');
    }


add_action('admin_menu', 'nocaptcha_login_recaptcha_menu_pages');



    function nocaptcha_login_recaptcha_form() {
        global $recaptcha;
        $ropt = get_option('recaptcha_options');
        $login_recaptcha_err = 0;

        if (isset($_GET['login_recaptcha_err'])) {
            $login_recaptcha_err = intval($_GET['login_recaptcha_err']);
        }

        $opt = get_option('nocaptcha_login_recaptcha_options');

        $captcha_code = '';
        if ('' != $opt['site_key'] && '' != $opt['secret_key']) {
            $captcha_code .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
			<div class="g-recaptcha" data-sitekey="'.htmlentities($opt['site_key']).'"></div>';
            if (1 == $login_recaptcha_err) {
                $captcha_code .= '<div style="color:#FF7425;">Human verification failed!</div>';
            }
        }
        echo $captcha_code;
    }

add_action('login_form','nocaptcha_login_recaptcha_form');



if (!function_exists('nocaptcha_login_recaptcha_get_ip')) {
    function nocaptcha_login_recaptcha_get_ip() {
        return $_SERVER['REMOTE_ADDR'];
    }
}

if (!function_exists('nocaptcha_login_recaptcha_get_post')) {
    function nocaptcha_login_recaptcha_get_post($var_name) {
        if (isset($_POST[$var_name])) {
            return $_POST[$var_name];
        } else {
            return '';
        }
    }
}







    function nocaptcha_login_recaptcha_process() {
        if (array() == $_POST) {
            return true;
        }

        $opt = get_option('nocaptcha_login_recaptcha_options');
        $parameters = array(
            'secret' => $opt['secret_key'],
            'response' => nocaptcha_login_recaptcha_get_post('g-recaptcha-response'),
            'remoteip' => nocaptcha_login_recaptcha_get_ip()
        );
        $url = 'https://www.google.com/recaptcha/api/siteverify?' . http_build_query($parameters);

        $response = nocaptcha_login_recaptcha_open_url($url);
        $json_response = json_decode($response, true);

        if (isset($json_response['success']) && true !== $json_response['success']) {
            header('Location: wp-login.php?login_recaptcha_err=1');
            exit();
        }
    }

    add_action('wp_authenticate', 'nocaptcha_login_recaptcha_process', 1);



    function nocaptcha_login_recaptcha_open_url($url) {
        if (function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            $response = file_get_contents($url);
        }
        return trim($response);
    }

function login_style_fix() {

    $wowinitialize = ' <style type="text/css">
                        #login {
                             width: 350px;
                                }
                        .g-recaptcha {
                          margin-bottom: 10px;
                        }
                        </style>';
    echo $wowinitialize;

}
add_action('login_head', 'login_style_fix');



?>