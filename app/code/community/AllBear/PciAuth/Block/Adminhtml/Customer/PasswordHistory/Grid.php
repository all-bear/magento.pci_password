<?php
class AllBear_PciAuth_Block_Adminhtml_Customer_PasswordHistory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('customer_id');
        $this->setId('allbear_pciauth_adminhtml_customer_passwordHistory_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        return 'allbear_pciauth/customer_passwordHistory_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_id',
            array(
                'header'=> $this->__('Customer Id'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'customer_id'
            )
        );

        $this->addColumn('password_hash',
            array(
                'header'=> $this->__('Password hash'),
                'index' => 'password_hash'
            )
        );

        return parent::_prepareColumns();
    }
}