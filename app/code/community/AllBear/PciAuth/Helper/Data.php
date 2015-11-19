<?php

class AllBear_PciAuth_Helper_Data extends Mage_Core_Helper_Abstract
{
    const IS_ENABLED_CONFIG_PATH                 = 'allbear_pciauth/settings/enabled';
    const PASSWORD_MIN_LENGTH_CONFIG_PATH        = 'allbear_pciauth/settings/password_min_length';
    const LOGIN_MAX_TRIES_CONFIG_PATH            = 'allbear_pciauth/settings/login_max_tries';
    const LOGIN_MAX_TRIES_RESET_TIME_CONFIG_PATH = 'allbear_pciauth/settings/login_tries_reset_time';
    const LOCKOUT_EFFECTIVE_PERIOD_CONFIG_PATH   = 'allbear_pciauth/settings/lockout_effective_period';

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::IS_ENABLED_CONFIG_PATH);
    }

    public function getPasswordMinLength()
    {
        return $this->_getStoreConfigNumber(self::PASSWORD_MIN_LENGTH_CONFIG_PATH);
    }

    public function getLoginMaxTries()
    {
        return $this->_getStoreConfigNumber(self::LOGIN_MAX_TRIES_CONFIG_PATH);
    }

    public function getLoginMaxTriesResetTime()
    {
        return $this->_getStoreConfigNumber(self::LOGIN_MAX_TRIES_RESET_TIME_CONFIG_PATH);
    }

    public function getLockoutEffectivePeriod()
    {
        return $this->_getStoreConfigNumber(self::LOCKOUT_EFFECTIVE_PERIOD_CONFIG_PATH);
    }

    protected function _getStoreConfigNumber($path)
    {
        return intval(Mage::getStoreConfig($path));
    }
}