<?php
/**
 * @author     Jason at TwinCreations <twincreations.co.uk>
 * @copyright  Copyright (c) 2014 TwinCreations <http://twincreations.co.uk/>
 */
class TC_Currency_Model_Yahoo extends Mage_Directory_Model_Currency_Import_Abstract
{
    protected $_url = 'http://quote.yahoo.com/d/quotes.csv?s={{CURRENCY_FROM}}{{CURRENCY_TO}}=X&f=l1&e=.csv';
    protected $_messages = array();
 
    protected function _convert($currencyFrom, $currencyTo, $retry=0) {
        $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->_url);
        $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);
 
        try {
            sleep(1);
            $handle = fopen($url, "r");
            $exchange_rate = fread($handle, 2000);
            fclose($handle);
 
            if (!$exchange_rate) {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s', $url);
                return null;
            }
 
            return (float) $exchange_rate * 1.0;
        } catch (Exception $e) {
            if ($retry == 0) {
                $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s', $url);
            }
        }
    }
}
