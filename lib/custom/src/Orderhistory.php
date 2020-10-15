<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Slider
 */


namespace Aimeos\MShop\Swordbros\Orderhistory;

/**
 * Slider item with common methods.
 *
 * @package MShop
 * @subpackage Slider
 */
class Helper
{
	public static function get_cancelorder_button($orderItem){
		return '<button type="button" class="btn btn-default btn-cancel-order btn-cancel-order-'.$orderItem->getBaseId().' sw_confirm" data-message="'. $this->translate( 'client', 'Confitm Message' ).'" data-text="'.$this->translate( 'client', 'Button' ).'" href="https://paltoru2.tulparstudyo.net/jsonapi/orderhistory?base_id='.$orderItem->getBaseId().'&order_id='.$orderItem->getId().'" data-action="cancel-order">';
	}
	private public function translate($string)
	{
		# code...
		return $string;
	}
}
