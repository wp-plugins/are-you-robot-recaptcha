<?php
/*
Plugin Name: Are you robot? google recaptcha for wordpress
Plugin URI: http://www.idiotinside.com
Description: Adds the new google recaptcha to wp-login page, registration page, comments section and buddy press registration page.
Version: 2.2
Author: Suresh Kumar
Author URI: http://profiles.wordpress.org/sureshdsk/
*/
defined( 'ABSPATH' ) OR exit;

$nocaptcha_opts = get_option('nocaptcha_login_recaptcha_options');
function nocaptcha_login_recaptcha_page() {
    if (!current_user_can('manage_options')) {
        wp_die( __('Access denied') );
    }
    if (isset($_POST['process'])) {
        update_option('nocaptcha_login_recaptcha_options', $_POST['nocaptcha_login_recaptcha_options']);
        _e('<div id="message" class="updated"><p>Options saved...</p></div>');
    }
    $opt = get_option('nocaptcha_login_recaptcha_options');
    ?>
    <div class="wrap">
        <table>
            <tr>
                <td style="width: 70%" valign="top">
                    <h2>Are you robot? Google reCAPTCHA settings</h2>
                    <p>Get the public key and private key <a href="https://www.google.com/recaptcha/admin" title="google recaptcha" target="_blank"> Google recaptcha</a></p>
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
                            <tr valign="top">
                                <th scope="row">Enable on Login page</th>
                                <td>
                                    <select name="nocaptcha_login_recaptcha_options[login]">
                                        <option>select an option</option>
                                        <option <?php if($opt['login'] == "1")echo "selected";?> value="1">Yes</option>
                                        <option <?php if($opt['login'] == "0")echo "selected";?> value="0">No</option>
                                    </select>

                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Enable on Registration page</th>
                                <td>
                                    <select name="nocaptcha_login_recaptcha_options[register]">
                                        <option>select an option</option>
                                        <option <?php if($opt['register'] == "1")echo "selected";?> value="1">Yes</option>
                                        <option <?php if($opt['register'] == "0")echo "selected";?> value="0">No</option>
                                    </select>

                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Enable on Comments</th>
                                <td>
                                    <select name="nocaptcha_login_recaptcha_options[comments]">
                                        <option>select an option</option>
                                        <option <?php if($opt['comments'] == "1")echo "selected";?> value="1">Yes</option>
                                        <option <?php if($opt['comments'] == "0")echo "selected";?> value="0">No</option>
                                    </select>

                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Enable on buddypress registration</th>
                                <td>
                                    <select name="nocaptcha_login_recaptcha_options[buddypress]">
                                        <option>select an option</option>
                                        <option <?php if($opt['buddypress'] == "1")echo "selected";?> value="1">Yes</option>
                                        <option <?php if($opt['buddypress'] == "0")echo "selected";?> value="0">No</option>
                                    </select>

                                </td>
                            </tr>


                        </table>
                        <p class="submit"> <input type="hidden" name="process" value="1" /> <input type="submit" class="button-primary" value="Save Options" /></p>
                    </form>
                </td>
                <td style="width: 30%;background: #fff;padding: 10px;" valign="top">
                    <div>
                        <h2>Like us on Facebook</h2>
                        <iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fidiotinside&amp;width&amp;height=290&amp;colorscheme=light&amp;show_faces=true&amp;header=true&amp;stream=false&amp;show_border=true&amp;appId=792614787449533" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:290px;" allowTransparency="true"></iframe>
                    </div>

                </td>
            </tr>
        </table>

    </div>
<?php

}


function nocaptcha_login_recaptcha_menu_pages() {
    add_options_page('reCAPTCHA', 'reCAPTCHA', 'manage_options', 're-captcha-config', 'nocaptcha_login_recaptcha_page');
}


add_action('admin_menu', 'nocaptcha_login_recaptcha_menu_pages');



function nocaptcha_login_recaptcha_form() {


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

if($nocaptcha_opts["login"]=="1") {
    add_action('login_form', 'nocaptcha_login_recaptcha_form');
}

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

if (!function_exists('nocaptcha_login_recaptcha_process')) {
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

        if (!empty($opt['secret_key']) && isset($json_response['success']) && true !== $json_response['success']) {
            header('Location: wp-login.php?login_recaptcha_err=1');
            exit();
        }
    }
}

if($nocaptcha_opts["login"]=="1") {
    add_action('wp_authenticate', 'nocaptcha_login_recaptcha_process', 1);
}

if (!function_exists('nocaptcha_login_recaptcha_open_url')) {
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
if($nocaptcha_opts["login"]=="1") {
    add_action('login_head', 'login_style_fix');
}


/* Registration Page */
if (!function_exists('nocaptcha_login_recaptcha_reg_process')) {

    function nocaptcha_login_recaptcha_reg_process()
    {
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

        if (!empty($opt['secret_key']) && isset($json_response['success']) && true !== $json_response['success']) {
            header('Location: wp-login.php?action=register&login_recaptcha_err=1');
            exit();
        }
    }
}
if($nocaptcha_opts["register"]=="1") {
    add_action('register_form', 'nocaptcha_login_recaptcha_form');
    add_action('register_post', 'nocaptcha_login_recaptcha_reg_process', 10, 3);

}

if($nocaptcha_opts["comments"]=="1") {
    add_action( 'comment_form_after_fields', 'nocaptcha_comment_form');
    add_action( 'comment_form_logged_in_after', 'nocaptcha_comment_form');

}

if (!function_exists('nocaptcha_comment_form')) {

    function nocaptcha_comment_form() {



        if ( is_user_logged_in()) {
            return true;
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

        return true;
    }

}
if($nocaptcha_opts["comments"]=="1") {
    add_filter('preprocess_comment', 'cptch_comment_post');
}

if ( ! function_exists( 'cptch_comment_post' ) ) {
    function cptch_comment_post( $comment ) {


        if ( is_user_logged_in() ) {
            return $comment;
        }

        if ( isset( $_REQUEST['action'] ) && 'replyto-comment' == $_REQUEST['action'] &&
            ( check_ajax_referer( 'replyto-comment', '_ajax_nonce', false ) || check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment', false ) ) ) {
            return $comment;
        }

        if ( '' != $comment['comment_type'] && 'comment' != $comment['comment_type'] ) {
            return $comment;
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

        if (!empty($opt['secret_key']) && isset($json_response['success']) && true !== $json_response['success']) {
            wp_die( __('Human verification failed', 'captcha' ) );
        }else{
            return( $comment );
        }

    }
}

/* buddypress */
if ( ! function_exists( 'bp_add_recaptcha' ) ) {

    function bp_add_recaptcha()
    {
        global $bp;


        $opt = get_option('nocaptcha_login_recaptcha_options');

        $captcha_code = '';
        if ('' != $opt['site_key'] && '' != $opt['secret_key']) {
            $captcha_code .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
			<div class="g-recaptcha" data-sitekey="' . htmlentities($opt['site_key']) . '"></div>';
        }

        $html = '<div class="register-section" id="nocaptcha" style="float: right;">';
        $html .= '<div class="editfield">';
        $html .= '<label>Are you robot?</label>';
        if (!empty($bp->signup->errors['recaptcha_response_field'])) {
            $html .= '<div class="error">';
            $html .= $bp->signup->errors['recaptcha_response_field'];
            $html .= '</div>';
        }
        $html .= $captcha_code;
        $html .= '</div>';
        $html .= '</div>';
        echo $html;
    }
}
if ( ! function_exists( 'bp_validate_recaptcha' ) ) {

    function bp_validate_recaptcha($errors)
    {
        global $bp, $strError;

        $opt = get_option('nocaptcha_login_recaptcha_options');
        $parameters = array(
            'secret' => $opt['secret_key'],
            'response' => nocaptcha_login_recaptcha_get_post('g-recaptcha-response'),
            'remoteip' => nocaptcha_login_recaptcha_get_ip()
        );
        $url = 'https://www.google.com/recaptcha/api/siteverify?' . http_build_query($parameters);

        $response = nocaptcha_login_recaptcha_open_url($url);
        $json_response = json_decode($response, true);

        if (!empty($opt['secret_key']) && isset($json_response['success']) && true !== $json_response['success']) {
            $bp->signup->errors['recaptcha_response_field'] = '<div style="color:#FF7425;">Human verification failed!</div>';
        }
        return;
    }
}

if($nocaptcha_opts["buddypress"]=="1") {

    add_action('bp_before_registration_submit_buttons', 'bp_add_recaptcha');
    add_action('bp_signup_validate', 'bp_validate_recaptcha');
}

/* cf7 */


function is_cf7_active() {
    return in_array(
        'contact-form-7/wp-contact-form-7.php',
        apply_filters(
            'active_plugins',
            get_option(
                'active_plugins' ) ) );
}

add_action('plugins_loaded', 'nocaptchagcaptcha_plugins_loaded');
function nocaptchagcaptcha_plugins_loaded() {
    if(is_cf7_active()){
        wpcf7_add_shortcode('nocaptcha', 'shortcode_no_gcaptcha_handler', true);
        add_filter('wpcf7_validate_nocaptcha', 'nocap_validation_filter_func', 10, 2);

    }

}
function shortcode_no_gcaptcha_handler( $tag ) {
    $type = $tag['type'];
    $name = $tag['name'];
    $opt = get_option('nocaptcha_login_recaptcha_options');

    $captcha_code = '';
    if ('' != $opt['site_key'] && '' != $opt['secret_key']) {
        $captcha_code .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
			<div class="g-recaptcha" data-sitekey="'.htmlentities($opt['site_key']).'"></div>
<span class="wpcf7-form-control-wrap grecaptcha"><input type="text" name="grecaptcha" value="dsk" size="1" class="wpcf7-form-control wpcf7-text" style="display:none;" /></span>';
    }

    return $captcha_code;

}

function nocap_validation_filter_func( $errors, $tag = '' ) {


    $opt = get_option('nocaptcha_login_recaptcha_options');
    $parameters = array(
        'secret' => $opt['secret_key'],
        'response' => nocaptcha_login_recaptcha_get_post('g-recaptcha-response'),
        'remoteip' => $_POST['g-recaptcha-response']
    );
    $url = 'https://www.google.com/recaptcha/api/siteverify?' . http_build_query($parameters);

    $response = nocaptcha_login_recaptcha_open_url($url);
    $json_response = json_decode($response, true);


    if (empty($_POST['g-recaptcha-response']) || true !== $json_response['success']) {

        $errors['valid'] = false;
        $reason = array("grecaptcha" =>  "Recaptcha verification failed.." ) ;
        $errors[ 'reason' ] = array_merge($errors[ 'reason' ],$reason);
        return $errors;
    }

    return $errors;
}



?>