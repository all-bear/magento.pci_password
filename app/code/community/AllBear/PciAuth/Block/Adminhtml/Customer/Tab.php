<?php

class AllBear_PciAuth_Block_Adminhtml_Customer_Tab extends AllBear_PciAuth_Block_Adminhtml_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('allbear/pciauth/customer/tab.phtml');
    }

    public function getTabLabel()
    {
        return $this->__('Password history');
    }

    public function getTabTitle()
    {
        return $this->__('Password history');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}