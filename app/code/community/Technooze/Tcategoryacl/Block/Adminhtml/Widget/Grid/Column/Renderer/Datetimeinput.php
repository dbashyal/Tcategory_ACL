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
class Technooze_Tcategoryacl_Block_Adminhtml_Widget_Grid_Column_Renderer_Datetimeinput
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);

        if(!preg_match('/name=".+\[.*?\][^"]"/i', $html)){
            $html = preg_replace('/name="([^"]+)"/i', 'name="$1['.$row->getId().']"', $html);
        }

        preg_match('/name="([^"]+)"/i', $html, $name);
        $id = preg_replace('/\[.*\]|[^a-z0-9_\-]/i', '', $name[1]) . '_tcategory_' . $row->getId();
        if(!preg_match('/id="([^"])"/i', $html)){
            $html = preg_replace('/name=/i', 'id="' . $id . '" name=', $html);
        }

        $html .= '<img src="' . Mage::getSingleton('core/design_package')->getSkinUrl('images/grid-cal.gif') . '" alt="' . $this->helper('core')->__('Select Date') . '" class="v-middle" ';
        $html .= 'title="' . $this->helper('core')->__('Select Date') . '" id="' . $id . '_trig" />';

        $html .=
        '<script type="text/javascript">
        //<![CDATA[
            var calendarSetupObject = {
                inputField  : "' . $id . '",
                ifFormat    : "%Y-%m-%e %H:%M:%S",
                showsTime   : "true",
                button      : "' . $id . '_trig",
                align       : "Bl",
                singleClick : true
            }';

        $calendarYearsRange = $this->getYearsRange();
        if ($calendarYearsRange) {
            $html .= '
                calendarSetupObject.range = ' . $calendarYearsRange . '
                ';
        }

        $html .= '
            Calendar.setup(calendarSetupObject);
        //]]>
        </script>';

        return $html;
    }
}
