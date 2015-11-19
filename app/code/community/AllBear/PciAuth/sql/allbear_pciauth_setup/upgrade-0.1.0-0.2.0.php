<?php

$installer = $this;

$installer->startSetup();

$tableName = 'allbear_pciauth/customer_password_history_entity';
$table     = $this->getConnection()
    ->newTable($this->getTable($tableName))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Entity ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Customer ID')
    ->addColumn('password_hash', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Password')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Created At')
    ->addIndex($this->getIdxName($tableName, array('customer_id')), array('customer_id'))
    ->addForeignKey($this->getFkName($tableName, 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $this->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE);
$this->getConnection()->createTable($table);

$installer->endSetup();