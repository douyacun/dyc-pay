<?php

namespace DYC\Pay\Exceptions;

class InvalidGatewayException extends Exception
{
    /**
     * Raw error info.
     *
     * @var array|string
     */
    public $raw;

    /**
     * Bootstrap.
     *
     * @author yansongda <me@yansonga.cn>
     *
     * @param string       $message
     * @param array|string $raw
     * @param int|string   $code
     */
    public function __construct($message, $raw = '', $code = 1)
    {
        parent::__construct($message, intval($code));

        $this->raw = $raw;
    }
}
