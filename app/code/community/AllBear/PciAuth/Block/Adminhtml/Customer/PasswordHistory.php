<?php

class AllBear_PciAuth_Block_Adminhtml_Customer_PasswordHistory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'allbear_pciauth';
        $this->_controller = 'adminhtml_customer_passwordHistory';
        $this->_headerText = '';

        parent::__construct();
    }
}