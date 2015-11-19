<?php

class AllBear_PciAuth_Model_Resource_Customer_PasswordHistory_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('allbear_pciauth/customer_passwordHistory');
    }
}