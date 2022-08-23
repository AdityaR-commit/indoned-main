<?php

/**
 * Created by PhpStorm
 * User : Bambang S
 * Date : 06-03-2021
 * Time: 03:59
 */

namespace app\libraries\Arkatama\exception;

abstract class CustomException extends \Exception implements IException
{
    protected $message = 'Unknown exception';       // Exception message
    private $string;                                // Unknown
    protected $code = 0;                            // User-defined exception code
    protected $file;                                // Source filename of exception
    protected $line;                                // Source line of exception
    private $trace;                                 // Unknown

    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }

}