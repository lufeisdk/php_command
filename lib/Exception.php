<?php

namespace lib;

class Exception extends \Exception
{
    /**
     * �����쳣ҳ����ʾ�Ķ���Debug����
     * @var array
     */
    protected $data = [];

    /**
     * �����쳣�����Debug����
     * ���ݽ�����ʾΪ����ĸ�ʽ
     *
     * Exception Data
     * --------------------------------------------------
     * Label 1
     *   key1      value1
     *   key2      value2
     * Label 2
     *   key1      value1
     *   key2      value2
     *
     * @access protected
     * @param  string $label ���ݷ��࣬�����쳣ҳ����ʾ
     * @param  array $data ��Ҫ��ʾ�����ݣ�����Ϊ��������
     */
    final protected function setData($label, array $data)
    {
        $this->data[$label] = $data;
    }

    /**
     * ��ȡ�쳣����Debug����
     * ��Ҫ����������쳣ҳ����ڵ���
     * @access public
     * @return array ��setData���õ�Debug����
     */
    final public function getData()
    {
        return $this->data;
    }

//    public function errorMessage()
//    {
//        //error message
//        $errorMsg = $this->getMessage() . PHP_EOL;
//        $errorMsg .= 'Error on line ' . $this->getLine() . ' in ' . $this->getFile() . PHP_EOL;
//        return $errorMsg;
//    }
}