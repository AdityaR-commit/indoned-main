<?php

class AUTHORIZATION
{
    public static function validateTimestamp($token)
    {
        $CI =& get_instance();
        $token = self::validateToken($token);
        if ($token != false) {
            // validate timestamp
            if ($CI->config->item('token_timeout') <= 0 || (now() - $token->timestamp < ($CI->config->item('token_timeout') * 60))) { // valid
                return $token;
            }
        }
        return false;
    }

    public static function validateToken($token)
    {
        $CI =& get_instance();
        // strip out string 'bearer'
        $stripOut = explode(' ', $token);
        $token = count($stripOut) > 1 ? $stripOut[1] : $stripOut[0];
        
        return JWT::decode($token, $CI->config->item('jwt_key'));
    }

    public static function generateToken($data)
    {
        $CI =& get_instance();
        // inject timestamp
        $data['timestamp'] = date('Y-m-d H:i:s');
        return JWT::encode($data, $CI->config->item('jwt_key'));
    }

}