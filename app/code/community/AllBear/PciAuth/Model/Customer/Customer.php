<?php

class AllBear_PciAuth_Model_Customer_Customer extends Mage_Customer_Model_Customer
{
    const NATIVE_INVALID_PASSWORD_LENGTH_MESSAGE      = 'The minimum password length is 6';
    const EXCEPTION_TOO_MUCH_UNSUCCESSFUL_LOGIN_TRIES = 10;
    
    private $_helper;

    # I don't know why in Mage_Customer_Model_Customer do they do this method public in Magento 1.9.2
    function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('allbear_pciauth');
    }

    public function authenticate($login, $password)
    {
        $this->loadByEmail($login);

        if (!$this->_helper->isEnabled() || !$this->getId()) {
            return parent::authenticate($login, $password);
        }

        $this->_handleUnlock();

        if ($this->getIsLocked()) {
            throw Mage::exception('Mage_Core', $this->_helper->__('This account is locked because of too much unsuccessful login tries.'),
                self::EXCEPTION_TOO_MUCH_UNSUCCESSFUL_LOGIN_TRIES
            );
        }

        try {
            parent::authenticate($login, $password);

            $this->_resetLock();

            return true;
        } catch (Mage_Core_Exception $e) {
            if ($e->getCode() === self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD) {
                $this->_handleLock();
            }

            throw $e;
        }
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
    
    protected function _handleUnlock()
    {
        if (!$this->getIsLocked()) {
            return;
        }

        if ($this->_helper->getLockoutEffectivePeriod() < $this->_getMinutesFromLock()) {
            $this->setIsLocked(false);
        }
    }

    protected function _increaseUnsuccessfulLoginTries()
    {
        if ($this->_helper->getLoginMaxTriesResetTime() < $this->_getMinutesFromLastUnsuccessfulLogin()) {
            $this->setUnsuccessfulLoginTries(0);
        }

        $this->setUnsuccessfulLoginTries($this->getUnsuccessfulLoginTries() + 1);
    }

    protected function _getMinutesFrom(DateTime $dateTime)
    {
        $currentDateTime = new Datetime(Mage::getModel('core/date')->date());

        return $dateTime->diff($currentDateTime)->i;
    }

    protected function _getMinutesFromLastUnsuccessfulLogin()
    {
        $lastUnsuccessfulTime = new DateTime($this->getLastUnsuccessfulLoginTime());

        return $this->_getMinutesFrom($lastUnsuccessfulTime);
    }

    protected function _getMinutesFromLock()
    {
        $lockTime = new DateTime($this->getLockTime());

        return $this->_getMinutesFrom($lockTime);
    }

    protected function _handleLock()
    {
        $this->_increaseUnsuccessfulLoginTries();

        $currentDate = Mage::getModel('core/date')->date();
        $this->setLastUnsuccessfulLoginTime($currentDate);

        $maxTries = $this->_helper->getLoginMaxTries();
        if ($maxTries && ($maxTries <= $this->getUnsuccessfulLoginTries())) {
            $this->setLockTime($currentDate);
            $this->setIsLocked(true);
        }

        $this->save();
    }

    protected function _resetLock()
    {
        $this->setIsLocked(false);
    }

    protected function _clearRedefinedValidationsErrors(&$errors)
    {
        $messageKeys = array(self::NATIVE_INVALID_PASSWORD_LENGTH_MESSAGE);
        foreach ($messageKeys as $messageKey) {
            $message = Mage::helper('customer')->__($messageKey);

            if (($key = array_search($message, $errors)) !== false) {
                unset($errors[$key]);
            }
        }

        return $errors;
    }
}