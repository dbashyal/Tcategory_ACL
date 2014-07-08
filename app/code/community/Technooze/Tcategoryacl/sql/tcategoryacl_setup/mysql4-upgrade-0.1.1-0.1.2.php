<?php
/**
 * Tcategorystatus Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Technooze
 * @package    Tcategorystatus
 * @copyright  Copyright (c) 2014 dltr.org
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Technooze
 * @package    Tcategorystatus
 * @author     Damodar Bashyal @dbashyal
 */

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$profile = Mage::getModel('dataflow/profile');
$data = array(
    'name' => "Export Category Group Permissions",
    'actions_xml' =>   '
<action type="tcategoryacl/convert_adapter_permissionsexport" method="save">
    <var name="type">file</var>
    <var name="path">var/export</var>
    <var name="filename"><![CDATA[category_group_permissions.csv]]></var>
</action>
'
);
if (isset($data)) {
   $profile->addData($data);
}
try {
    $profile->save();
} catch (Exception $e){
    Mage::log($e->getMessage());
}