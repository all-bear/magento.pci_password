<?php

class AllBear_PciAuth_Model_Customer_PasswordHistory extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('allbear_pciauth/customer_passwordHistory');
    }

    public function getAllPasswordHashesForCustomer(Mage_Customer_Model_Customer $customer)
    {
        $passwordHashes = [];

        $records = $this->getCollection()->addFilter('customer_id', $customer->getId());
        foreach ($records as $record) {
            array_push($passwordHashes, $record->getPasswordHash());
        }

        return $passwordHashes;
    }
}
