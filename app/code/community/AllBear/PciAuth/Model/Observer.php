<?php

class AllBear_PciAuth_Model_Observer extends Varien_Object
{
    private $_helper;

    protected function _construct()
    {
        $this->_helper = Mage::helper('allbear_pciauth');
    }

    public function deactivateNotActiveCustomer()
    {
        $session = Mage::getSingleton('customer/session');

        if (!$session->isLoggedIn()) {
            return $this;
        }

        $currentDate      = Mage::getModel('core/date')->date();
        $lastActivityDate = $session->getLastActivityDate();

        $deactivationPeriod = $this->_helper->accountDeactivationPeriod();

        if ($lastActivityDate && $deactivationPeriod) {
            if ($deactivationPeriod < $this->_getActivityMinutesInterval($currentDate, $lastActivityDate)) {
                $session->logout();
                Mage::getSingleton('core/session')->addNotice('Your session was expired');
            }
        }

        $session->setLastActivityDate($currentDate);
    }

    protected function _getActivityMinutesInterval($currentDate, $lastActivityDate)
    {
        $currentDate      = new DateTime($currentDate);
        $lastActivityDate = new DateTime($lastActivityDate);

        return $lastActivityDate->diff($currentDate)->i;
    }
}