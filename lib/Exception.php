<?php

namespace lib;

class Exception extends \Exception
{
    public function errorMessage()
    {
        //error message
        $errorMsg = $this->getMessage() . PHP_EOL;
        $errorMsg .= 'Error on line ' . $this->getLine() . ' in ' . $this->getFile() . PHP_EOL;
        return $errorMsg;
    }
}