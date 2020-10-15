<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2020
 * @package Client
 * @subpackage JsonApi
 */

$enc = $this->encoder();

$target = $this->config( 'client/jsonapi/url/target' );
$cntl = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$action = $this->config( 'client/jsonapi/url/action', 'get' );
$config = $this->config( 'client/jsonapi/url/config', [] );

$ref = array( 'resource', 'id', 'related', 'relatedid', 'filter', 'page', 'sort', 'include', 'fields' );
$params = array_intersect_key( $this->param(), array_flip( $ref ) );

$pretty = $this->param( 'pretty' ) ? JSON_PRETTY_PRINT : 0;

?>
{
	"meta": {
		"total": <?= $this->get( 'total', 0 ); ?>,
		"prefix": <?= json_encode( $this->get( 'prefix' ) ); ?>,
		"content-baseurl": "<?= $this->config( 'resource/fs/baseurl' ); ?>",
		"status":"<?=$this->get( 'status' )?>",
		"message":"<?=$this->get( 'message' )?>",
		"baseid":"<?=$this->get( 'baseid' )?>"
		<?php if( $this->csrf()->name() != '' ) : ?>
			, "csrf": {
				"name": "<?= $this->csrf()->name(); ?>",
				"value": "<?= $this->csrf()->value(); ?>"
			}
		<?php endif; ?>

	},
	"links": {
		"self": "<?= $this->url( $target, $cntl, $action, $params, [], $config ); ?>"
	}
	<?php if( isset( $this->errors ) ) : ?>
		,"errors": <?= json_encode( $this->errors, $pretty ); ?>

	<?php endif; ?>

}
