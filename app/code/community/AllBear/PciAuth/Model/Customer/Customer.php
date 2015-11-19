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
        if (!$this->_helper->isEnabled()) {
            return parent::authenticate($login, $password);
        }

        $this->loadByEmail($login);

        if (!$this->getId()) {
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

        if ($this->_isPasswordWasAlreadyInUse()) {
            $errors[] = $this->_helper->__('Password was already used by you some times ago. Use new password.');
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    protected function _afterSave()
    {
        parent::_afterSave();

        if (!$this->hasDataChanges()) {
            return $this;
        }

        $this->_updatePasswordHistory();

        return $this;
    }

    protected function _updatePasswordHistory()
    {
        if (!$this->dataHasChangedFor('password_hash')) {
            return $this;
        }

        $passwordHistory = Mage::getModel('allbear_pciauth/customer_passwordHistory');
        $passwordHistory->setData(array(
            'customer_id' => $this->getId(),
            'password_hash' => $this->getPasswordHash(),
            'created_at' => Mage::getModel('core/date')->timestamp()
        ));
        $passwordHistory->save();
    }

    protected function _isPasswordWasAlreadyInUse()
    {
        $used = false;

        $passwordHistory = Mage::getModel('allbear_pciauth/customer_passwordHistory');

        foreach ($passwordHistory->getAllPasswordHashesForCustomer($this) as $hash) {
            $used = Mage::helper('core')->validateHash($this->getPassword(), $hash);

            if ($used) {
                break;
            }
        }

        return $used;
    }
    
    protected function _handleUnlock()
    {
        if (!$this->getIsLocked()) {
            return;
        }

        if ($this->_helper->getLockoutEffectivePeriod() < $this->_getMinutesFromLock()) {
            $this->setIsLocked(false);
            $this->setUnsuccessfulLoginTries(0);
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
