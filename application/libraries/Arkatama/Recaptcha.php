<?php

namespace app\libraries\Arkatama;

defined('BASEPATH') or exit('No direct script access allowed');


class Recaptcha
{
    private $_site_key;
    private $_secret_key;
    const API = 'https://www.google.com/recaptcha/api/siteverify';
    private $_ci;
    private $_parameters;
    private $_userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36';

    public function __construct($options = NULL)
    {
        $this->_ci =& get_instance();

        $this->_ci->config->load('recaptcha', FALSE, TRUE);

        $config = array(
            'site_key'   => $this->_ci->config->item('site_key', 're_keys'),
            'secret_key' => $this->_ci->config->item('secret_key', 're_keys'),
            'parameters' => $this->_ci->config->item('re_parameters')
        );

        if (is_array($options)) {
            $config = array_merge($config, $options);
        }

        $this->set_keys($config['site_key'], $config['secret_key']);

        if (!empty($config['parameters'])) {
            $this->set_parameters($config['parameters']);
        }

        log_message('info', 'reCaptcha Class Initialized');
    }

    public function set_keys($site, $secret)
    {
        $this->_site_key   = $site;
        $this->_secret_key = $secret;

        log_message('info', 'reCaptcha Class: Keys were set');
    }

    public function set_parameter($name, $value)
    {
        $this->_parameters[$name] = $value;

        log_message('info', 'reCaptcha Class: Rendering parameter "' . $name . '" was set to "' . $value . '"');
    }

    public function set_parameters($array)
    {
        foreach ($array as $name => $value) {
            $this->set_parameter($name, $value);
        }
    }

    public function create_box($attr = NULL)
    {
        if (empty($this->_site_key) || empty($this->_secret_key)) {
            if (ENVIRONMENT === 'development') {
                show_error('Please set both the Site key and Secret key for the reCAPTCHA library.', 500, 'reCAPTCHA library: Missing keys');
            } else {
                log_message('error', 'reCaptcha Class: No keys are set');
            }

            return FALSE;
        }

        $box = '<div';
        $box .= ' data-sitekey="' . html_escape($this->_site_key) . '"';
        foreach ($this->_parameters as $parameter => $value) {
            if ($value !== NULL) {
                $box .= ' data-' . html_escape($parameter) . '="' . html_escape($value) . '"';
            }
        }

        if ($attr === NULL) {
            $box .= ' class="g-recaptcha"';
        } else {

            if (empty($attr['class'])) {
                $attr['class'] = 'g-recaptcha';

            } else {
                $attr['class'] .= ' g-recaptcha';
            }
            foreach ($attr as $attrib => $value) {
                $box .= ' ' . html_escape($attrib) . '="' . html_escape($value) . '"';
            }
        }

        $box .= '></div>';

        log_message('info', 'reCaptcha Class: Box was generated');

        return $box;
    }

    public function is_valid($response = NULL, $ip = FALSE)
    {
        if (empty($this->_site_key) || empty($this->_secret_key)) {
            if (ENVIRONMENT === 'development') {
                show_error('Please set both the Site key and Secret key for the reCAPTCHA library.', 500, 'reCAPTCHA library: Missing keys');
            } else {
                log_message('error', 'reCaptcha Class: No keys are set');
            }

            return array(
                'success' => FALSE,
            );
        }

        log_message('info', 'reCaptcha Class: Validating the response');

        $post_data = array(
            'response' => $response
        );

        if ($response === NULL) {
            $post_data['response'] = $this->_ci->input->post('g-recaptcha-response');
        }

        if (!empty($ip)) {
            $post_data['remoteip'] = $ip;
        } elseif ($ip === NULL) {
            $post_data['remoteip'] = $this->_ci->input->ip_address();
        }

        if (empty($post_data['response'])) {
            return array(
                'success' => FALSE,
            );
        }

        $post_data['secret'] = $this->_secret_key;
        $curl                = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_URL, self::API);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        $response = curl_exec($curl);
        if ($response === FALSE) {
            log_message('error', "reCAPTCHA library: cURL failed with error:" . curl_error($curl));
            $return = array(
                'success'       => FALSE,
                'error'         => TRUE,
                'error_message' => curl_error($curl)
            );
        } else {
            $return = json_decode($response, TRUE);
        }

        curl_close($curl);
        return $return;
    }
}
