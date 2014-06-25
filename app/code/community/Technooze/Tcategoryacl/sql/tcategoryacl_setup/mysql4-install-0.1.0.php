<?php
/**
 * Tcategory_ACL Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Technooze
 * @package    Tcategoryacl
 * @copyright  Copyright (c) 2014 dltr.org
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Technooze
 * @package    Tcategoryacl
 * @author     Damodar Bashyal @dbashyal
 */
$installer = $this;

$installer->startSetup();

$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('tcategoryacl')} (
      `tcategoryacl_id` int(11) unsigned NOT NULL auto_increment,
      `group_id` int(11) NOT NULL default '0',
      `category_id` int(11) NOT NULL default '0',
      `allow_from` datetime NULL,
      `allow_to` datetime NULL,
      `status` int(1) NOT NULL default '0',
      PRIMARY KEY (`tcategoryacl_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();