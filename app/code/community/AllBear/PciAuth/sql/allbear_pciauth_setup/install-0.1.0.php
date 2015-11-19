<?php

$installer = $this;

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('customer');

$installer->addAttribute($entityTypeId, 'unsuccessful_login_tries', array(
    'group'          => 'Default',
    'label'          => 'Unsuccessful login tries count',
    'type'           => 'int',
    'input'          => 'text',
    'backend'        => '',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'source'         => '',
    'visible'        => true,
    'required'       => false,
    'default'        => 0,
    'frontend'       => '',
    'unique'         => false,
    'user_defined'   => 1,
    'adminhtml_only' => 1
));

$installer->addAttribute($entityTypeId, 'last_unsuccessful_login_time', array(
    'group'          => 'Default',
    'label'          => 'Last unsuccessful login time',
    'type'           => 'datetime',
    'input'          => 'datetime',
    'backend'        => '',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'source'         => '',
    'visible'        => true,
    'required'       => false,
    'default'        => '',
    'frontend'       => '',
    'unique'         => false,
    'user_defined'   => 1,
    'adminhtml_only' => 1
));

$installer->addAttribute($entityTypeId, 'is_locked', array(
    'group'          => 'Default',
    'label'          => 'Locked',
    'type'           => 'int',
    'input'          => 'boolean',
    'backend'        => '',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'source'         => '',
    'visible'        => true,
    'required'       => false,
    'default'        => 0,
    'frontend'       => '',
    'unique'         => false,
    'user_defined'   => 1,
    'adminhtml_only' => 1
));

$installer->addAttribute($entityTypeId, 'lock_time', array(
    'group'          => 'Default',
    'label'          => 'Lock time',
    'type'           => 'datetime',
    'input'          => 'datetime',
    'backend'        => '',
    'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'source'         => '',
    'visible'        => true,
    'required'       => false,
    'default'        => '',
    'frontend'       => '',
    'unique'         => false,
    'user_defined'   => 1,
    'adminhtml_only' => 1
));


$forms = array(
    'adminhtml_customer'
);

foreach (array('unsuccessful_login_tries', 'last_unsuccessful_login_time', 'is_locked', 'lock_time') as $code) {
    $attribute = Mage::getSingleton('eav/config')->getAttribute($entityTypeId, $code);
    $attribute->setData('used_in_forms', $forms);
    $attribute->save();
}

$installer->endSetup();