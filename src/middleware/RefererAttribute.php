<?php

namespace Hiraeth\Middleware;

use Hiraeth;

use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

use Psr\Http\Message\ServerRequestFactoryInterface as RequestFactory;
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
		$referer = $this->factory->createServerRequest('GET', $request->getHeaderLine('Referer'));
		$query   = [];

		if ($referer->getUri()->getQuery()) {
			parse_str($referer->getUri()->getQuery(), $query);
		}

		return $handler->handle($request->withAttribute(
			'referer',
			$referer->withQueryParams($query)
		));
	}
}
