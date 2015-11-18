<?php

class AllBear_PciAuth_Helper_Data extends Mage_Core_Helper_Abstract
{
    const IS_ENABLED_CONFIG_PATH          = 'allbear_pciauth/settings/enabled';
    const PASSWORD_MIN_LENGTH_CONFIG_PATH = 'allbear_pciauth/settings/password_min_length';

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::IS_ENABLED_CONFIG_PATH);
    }

    public function getPasswordMinLength()
    {
        return $this->_getStoreConfigNumber(self::PASSWORD_MIN_LENGTH_CONFIG_PATH);
    }

    protected function _getStoreConfigNumber($path)
    {
        return intval(Mage::getStoreConfig($path));
    }
}