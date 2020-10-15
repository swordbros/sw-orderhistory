<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2020
 * @package Client
 * @subpackage JsonApi
 */


namespace Aimeos\Client\JsonApi\Orderhistory;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


/**
 * JSON API standard client
 *
 * @package Client
 * @subpackage JsonApi
 */
class Standard
	extends \Aimeos\Client\JsonApi\Base
	implements \Aimeos\Client\JsonApi\Iface
{
	/**
	 * Returns the resource or the resource list
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function get( ServerRequestInterface $request, ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{
		die("GET not supported");
		/*
		$view = $this->getView();
		$ref = $view->param( 'include', [] );

		if( is_string( $ref ) ) {
			$ref = explode( ',', $ref );
		}

		try
		{
			$cntl = \Aimeos\Controller\Frontend::create( $this->getContext(), 'order' )->uses( $ref );

			if( ( $id = $view->param( 'id' ) ) != '' )
			{
				$view->items = $cntl->get( $id );
				$view->total = 1;
			}
			else
			{
				$total = 0;
				$items = $cntl->parse( (array) $view->param( 'filter', [] ) )
					->slice( $view->param( 'page/offset', 0 ), $view->param( 'page/limit', 48 ) )
					->sort( $view->param( 'sort', '-order.id' ) )
					->search( $total );

				$view->items = $items;
				$view->total = $total;
			}

			$status = 200;
		}
		catch( \Aimeos\Controller\Frontend\Exception $e )
		{
			$status = 403;
			$view->errors = $this->getErrorDetails( $e, 'controller/frontend' );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = $this->getErrorDetails( $e, 'mshop' );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = $this->getErrorDetails( $e );
		}

		return $this->render( $response, $view, $status );
		*/
	}


	/**
	 * Creates or updates the resource or the resource list
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function post( ServerRequestInterface $request, ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{

		$view = $this->getView();
		try
		{
			$body = (string) $request->getBody();
			if( !($base_id = \Request::get('base_id')) ) {
				throw new \Aimeos\Client\JsonApi\Exception( sprintf( 'Required attribute "order_id" is missing' ), 400 );
			}

			if(!($customerid = \Auth::id())){
				throw new \Aimeos\Client\JsonApi\Exception( sprintf( 'User is\'nt access' ), 400 );
			}
			$view->baseid = $base_id;
			$view->status = 1;
			$view->message = $this->cancel_order($customerid, $base_id);;
			$status = 200;
		}
		catch( \Aimeos\Client\JsonApi\Exception $e )
		{
			$status = $e->getCode();
			$view->errors = $this->getErrorDetails( $e, 'client/jsonapi' );
		}
		catch( \Aimeos\Controller\Frontend\Exception $e )
		{
			$status = 403;
			$view->errors = $this->getErrorDetails( $e, 'controller/frontend' );
		}
		catch( \Aimeos\MShop\Exception $e )
		{
			$status = 404;
			$view->errors = $this->getErrorDetails( $e, 'mshop' );
		}
		catch( \Exception $e )
		{
			$status = 500;
			$view->errors = $this->getErrorDetails( $e );
		}

		return $this->render( $response, $view, $status );
	}

	private function cancel_order($customerid, $base_id, $siteid=1){
		$cond = [['id', $base_id], ['customerid', $customerid], ['siteid', $siteid]];
		$order_base = \DB::table('mshop_order_base')->where($cond)->first();
		if($order_base){
			$cond = [['baseid', $base_id]];
			$order_base = \DB::table('mshop_order')->where($cond)->orderBy('id', 'DESC')->first();
			$row =[	
					"baseid" => $base_id, 
					"siteid" => $order_base->siteid,
					"type" => $order_base->type,
					"statuspayment" => 1,
					"statusdelivery" => $order_base->statusdelivery,
					"datepayment" => $order_base->datepayment,
					"editor" => 'By Customer. ' . \Auth::user()->email,
					"cdate" => date('Y-m-d'),
					"cmonth" => date('Y-m'),
					"cweek" => date('Y-W'),
					"cwday" => date('w'),
					"chour" => date('H'),
					"ctime" => date('Y-m-d H:i:s'),
					"mtime" => date('Y-m-d H:i:s'),
				];
			$lastId = \DB::table('mshop_order')->insertGetId($row);
			if($lastId){
				return "Your Order cancelled. Your new order process number is"." ". $lastId;
			} else {
				return "We apologize. This is our mistake. Please try again later.";
			}
		} else{
			return "Order not found";
		}
	}

	/**
	 * Returns the response object with the rendered header and body
	 *
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @param \Aimeos\MW\View\Iface $view View instance
	 * @param int $status HTTP status code
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	protected function render( ResponseInterface $response, \Aimeos\MW\View\Iface $view, int $status ) : \Psr\Http\Message\ResponseInterface
	{
		$tplconf = 'client/jsonapi/swordbros/orderhistory/standard/template';
		$default = 'swordbros/orderhistory/standard';

		$body = $view->render( $view->config( $tplconf, $default ) );

		return $response->withHeader( 'Allow', 'GET,OPTIONS,POST' )
			->withHeader( 'Cache-Control', 'no-cache, private' )
			->withHeader( 'Content-Type', 'application/vnd.api+json' )
			->withBody( $view->response()->createStreamFromString( $body ) )
			->withStatus( $status );
	}
}
