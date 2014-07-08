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
 * @category        Technooze
 * @package         Tcategoryacl
 * @author          Damodar Bashyal @dbashyal
 * @original_author https://github.com/Vinai/groupscatalog2
 */
class Technooze_Tcategoryacl_Model_System_Config_Source_HiddenEntityHandling
{
    const HIDDEN_ENTITY_HANDLING_NOROUTE = '404';
    const HIDDEN_ENTITY_HANDLING_REDIRECT = '302';
    const HIDDEN_ENTITY_HANDLING_REDIRECT_PARENT = '302-parent';

    public function toOptionArray()
    {
        $helper = Mage::helper('tcategoryacl');
        return array(
            array(
                'value' => self::HIDDEN_ENTITY_HANDLING_NOROUTE,
                'label' => $helper->__('Show 404 Page')
            ),
            array(
                'value' => self::HIDDEN_ENTITY_HANDLING_REDIRECT,
                'label' => $helper->__('Redirect to target route')
            ),
            array(
                'value' => self::HIDDEN_ENTITY_HANDLING_REDIRECT_PARENT,
                'label' => $helper->__('Redirect to parent directory')
            )
        );
    }
}
