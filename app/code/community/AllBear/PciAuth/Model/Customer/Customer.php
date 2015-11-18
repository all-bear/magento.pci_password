<?php

class AllBear_PciAuth_Model_Customer_Customer extends Mage_Customer_Model_Customer
{
    const NATIVE_INVALID_PASSWORD_LENGTH_MESSAGE = 'The minimum password length is 6';

    private $_helper;

    # I don't know why in Mage_Customer_Model_Customer do they do this method public in Magento 1.9.2
    function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('allbear_pciauth');
    }

    public function validate()
    {
        $errors = parent::validate();

        if (!$this->_helper->isEnabled()) {
            return $errors;
        }

        if (!is_array($errors)) {
            $errors = array();
        }

        $this->_clearRedefinedValidationsErrors($errors);

        $password          = $this->getPassword();
        $minPasswordLength = $this->_helper->getPasswordMinLength();

        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array($minPasswordLength))) {
            $errors[] = Mage::helper('customer')->__('The minimum password length is %s', $minPasswordLength);
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    protected function _clearRedefinedValidationsErrors(&$errors)
    {
        $messageKeys = array(self::NATIVE_INVALID_PASSWORD_LENGTH_MESSAGE);
        foreach ($messageKeys as $messageKey) {
            $message = Mage::helper('customer')->__($messageKey);

            if(($key = array_search($message, $errors)) !== false) {
                unset($errors[$key]);
            }
        }

        return $errors;
    }
}