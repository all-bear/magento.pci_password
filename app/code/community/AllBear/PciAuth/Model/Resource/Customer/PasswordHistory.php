<?php

class AllBear_PciAuth_Model_Resource_Customer_PasswordHistory extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('allbear_pciauth/customer_password_history_entity', 'entity_id');
    }
}
