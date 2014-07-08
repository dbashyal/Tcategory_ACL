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
    'name' => "Import Category Group Permissions",
    'actions_xml' =>   '
<action type="tcategoryacl/convert_adapter_io" method="loader">
    <var name="remove_inactive">1</var>
    <var name="type">file</var>
    <var name="path">var/import</var>
    <var name="filename"><![CDATA[category_group_permissions.csv]]></var>
    <var name="format"><![CDATA[csv]]></var>
</action>
<action type="dataflow/convert_parser_csv" method="parse">
    <var name="delimiter"><![CDATA[,]]></var>
    <var name="enclose"><![CDATA["]]></var>
    <var name="fieldnames">true</var>
    <var name="store"><![CDATA[0]]></var>
    <var name="number_of_records">1</var>
    <var name="decimal_separator"><![CDATA[.]]></var>
    <var name="adapter">tcategoryacl/convert_adapter_permissionsimport</var>
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