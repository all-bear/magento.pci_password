<?php
class AllBear_PciAuth_Helper_Data extends Mage_Core_Helper_Abstract
{
    const IS_ENABLED_CONFIG_PATH = 'allbear_pciauth/settings/enabled';

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::IS_ENABLED_CONFIG_PATH);
    }
}