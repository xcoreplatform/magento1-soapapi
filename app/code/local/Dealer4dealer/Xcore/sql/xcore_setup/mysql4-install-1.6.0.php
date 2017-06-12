<?php
/**
 * Created by PhpStorm.
 * User: Eelco Dam
 * Date: 8-6-2017
 * Time: 13:37
 */

// Set the installer
$installer = $this;
// Start the setup
$installer->startSetup();
// Get the connection and add the new column
$installer->getConnection()
          ->addColumn(
              $installer->getTable('sales_flat_shipment'),
              'xcore_your_ref',
              array(
                  'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
                  'nullable' => true,
                  'length'   => 50,
                  'comment'  => 'xCore Your Reference'
              )
          );

$installer->endSetup();