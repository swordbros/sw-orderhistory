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
	static function get_cancelorder_button($orderItem, $contex){
		$statuspayment = $orderItem->get('order.statuspayment');
		$cancelables = array('-1', '4', '5');
		if(in_array($statuspayment, $cancelables)){
			return '<button type="button" class="btn btn-default btn-cancel-order btn-cancel-order-'.$orderItem->getBaseId().' sw_confirm" data-message="'. $contex->translate( 'client', 'Confitm Message' ).'" data-text="'.$contex->translate( 'client', 'Button' ).'" href="'.url('jsonapi/orderhistory').'?base_id='.$orderItem->getBaseId().'&order_id='.$orderItem->getId().'" data-action="cancel-order">Cancel</button>'.self::load_js();
		}
		
	}
	static function get_order_table($orderItems, $contex){
		$enc = $contex->encoder();
		foreach($orderItems as $orderItem){
			$orders[$orderItem->getBaseId()][$orderItem->getId()] =  $orderItem;
		}
		$enc = $contex->encoder();
		$accountTarget = $contex->config( 'client/html/account/history/url/target' );
		$accountController = $contex->config( 'client/html/account/history/url/controller', 'account' );
		$accountAction = $contex->config( 'client/html/account/history/url/action', 'history' );
		$accountConfig = $contex->config( 'client/html/account/history/url/config', [] );
		$dateformat = $contex->translate( 'client', 'Y-m-d' );
		$attrformat = $contex->translate( 'client', '%1$s at %2$s' );
	
			foreach($orders as $baseid=>$order){ 
				$orderItem = current($order);
?>
	<div class="history-item row">
		<div class="col-12">
			<h2 class="order-basic">
				<span class="name">
					<?= $enc->html( $contex->translate( 'client', 'Order ID' ), $enc::TRUST ) ?>
				</span>
				<span class="value">
				#<?= $enc->html( $orderItem->getBaseId() ) ?>
				</span>
			</h2>
		</div>	
		<div class="col-10">
			<div class="row">
				<div class="col-md-6">
					<div class="order-created row">
						<span class="name col-5">
							<?= $enc->html( $contex->translate( 'client', 'Created' ), $enc::TRUST ); ?>
						</span>
						<span class="value col-7">
							<?= $enc->html( date_create( $orderItem->getTimeCreated() )->format( $dateformat ) ); ?>
						</span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="order-channel row">
						<span class="name col-5">
							<?= $enc->html( $contex->translate( 'client', 'Channel' ), $enc::TRUST ); ?>
						</span>
						<span class="value col-7">
							<?php $code = 'order:' . $orderItem->getType(); ?>
							<?= $enc->html( $contex->translate( 'mshop/code', $code ), $enc::TRUST ); ?>
						</span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="order-payment row">
						<span class="name col-5">
							<?= $enc->html( $contex->translate( 'client', 'Payment' ), $enc::TRUST ); ?>
						</span>
						<span class="value col-7">
							<?php if( ( $date = $orderItem->getDatePayment() ) !== null ) : ?>
								<?php $code = 'pay:' . $orderItem->getPaymentStatus(); $paystatus = $contex->translate( 'mshop/code', $code ); ?>
								<?= $enc->html( sprintf( $attrformat, $paystatus, date_create( $date )->format( $dateformat ) ), $enc::TRUST ); ?>
							<?php endif; ?>
						</span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="order-delivery row">
						<span class="name col-5">
							<?= $enc->html( $contex->translate( 'client', 'Delivery' ), $enc::TRUST ); ?>
						</span>
						<span class="value col-7">
							<?php if( ( $date = $orderItem->getDateDelivery() ) !== null ) : ?>
								<?php $code = 'stat:' . $orderItem->getDeliveryStatus(); $status = $this->translate( 'mshop/code', $code ); ?>
								<?= $enc->html( sprintf( $attrformat, $status, date_create( $date )->format( $dateformat ) ), $enc::TRUST ); ?>
							<?php endif; ?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="action col-md-2">
			<?php $params = ['his_action' => 'order', 'his_id' => $orderItem->getId()] ?>
			<a class="btn btn-outline" href="<?= $enc->attr( $contex->url( $accountTarget, $accountController, $accountAction, $params, [], $accountConfig ) ); ?>">
				<?= $enc->html( $contex->translate( 'client', 'Show' ) ) ?>
			</a>
		</div>
		<div class="col-12 process-table"  style="display: none;">
		<table class="table mt-2">
		<tr><th colspan="5"><?= $enc->html( $contex->translate( 'client', 'Order Timeline' ), $enc::TRUST ) ?></th></tr>
		<tr>
		<th><?= $enc->html( $contex->translate( 'client', 'Process Number' ), $enc::TRUST ) ?></th>
		<th><?= $enc->html( $contex->translate( 'client', 'Created Date' ), $enc::TRUST ) ?></th>
		<th><?= $enc->html( $contex->translate( 'client', 'Payment Status' ), $enc::TRUST ) ?></th>
		<th><?= $enc->html( $contex->translate( 'client', 'Delivery Status' ), $enc::TRUST ) ?></th>			
		</tr>
		<?php foreach($order as $listItem){?>
		<tr>
		<td><?=$listItem->getBaseId()?>.<?=$listItem->getId()?></td>
		<td><?= $enc->html( date_create( $listItem->getTimeCreated() )->format( $dateformat ) ); ?></td>
		<td><?php if( ( $date = $listItem->getDatePayment() ) !== null ) : ?>
								<?php $code = 'pay:' . $listItem->getPaymentStatus(); $paystatus = $contex->translate( 'mshop/code', $code ); ?>
								<?= $enc->html( sprintf( $attrformat, $paystatus, date_create( $date )->format( $dateformat ) ), $enc::TRUST ); ?>
							<?php endif; ?></td>
		<td><?php if( ( $date = $listItem->getDateDelivery() ) !== null ) : ?>
								<?php $code = 'stat:' . $listItem->getDeliveryStatus(); $status = $this->translate( 'mshop/code', $code ); ?>
								<?= $enc->html( sprintf( $attrformat, $status, date_create( $date )->format( $dateformat ) ), $enc::TRUST ); ?>
							<?php endif; ?></td>
		</tr>
		<?php }?>
		</table>
		</div>
	</div>

<?php
		}	
	}


	static function load_js(){
		if(!defined('sw_order_history_js')){ 
			define('sw_order_history_js', TRUE);
		return '<script>
			// order history
			$("body").off("click", ".action-cancel-order");
					$("body").on("click", ".action-cancel-order", function(e){
					e.preventDefault();
					var url = "'.url("jsonapi").'";
					var cancel_order= [];
					cancel_order["url"] =  $(this).attr("href");
					cancel_order["ajax"] = $.ajax( url, {
						method: "OPTIONS",
						dataType: "json"
					});
					
					console.log(cancel_order);
					cancel_order["ajax"].done( function( servicedesc ) {
						console.log(cancel_order);
						
						var params = {}, param = {};

						if(servicedesc.meta && servicedesc.meta.csrf) {
							param[servicedesc.meta.csrf.name] = servicedesc.meta.csrf.value;
						}
						if(servicedesc.meta && servicedesc.meta.prefix) {
							params[servicedesc.meta.prefix] = param;
						} else {
							params = param;
						}
						var result = $.ajax({
							method: "POST",
							dataType: "json",
							url: cancel_order["url"],
							data: params
						}).done( function( result ) {
							$(".btn-cancel-order-" + result.meta.baseid ).remove();
							if($("body").find(".popup-message").length){
							$("body").find(".popup-message").html(result.meta.message);
							} else{
								alert(result.meta.message)
							}
						}).fail( function( result ) {
							alert("Network error. Please tray again later");
						}).always( function( result ) {

						});
						
					});	
					return false;	
				});
			// order history.		
		</script>';
		}
		return "";
	}

}
