<?php

namespace Hiraeth\Middleware;

use Hiraeth;

use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

use Psr\Http\Message\RequestFactoryInterface as RequestFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class RefererAttribute implements Middleware
{
	/**
	 *
	 */
	public function __construct(RequestFactory $factory)
	{
		$this->factory = $factory;
	}


	/**
	 *
	 */
	public function process(Request $request, Handler $handler): Response
	{
		$request = $request->withAttribute(
			'referer',
			$this->factory->createRequest('GET', $request->getHeaderLine('Referer'))
		);

		return $handler->handle($request);
	}
}
