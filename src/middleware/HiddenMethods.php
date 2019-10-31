<?php

namespace Hiraeth\Middleware;

use Hiraeth;

use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class HiddenMethods implements Middleware
{
	/**
	 *
	 */
	public function __construct(Hiraeth\Application $app)
	{
		$this->app = $app;
	}


	/**
	 *
	 */
	public function process(Request $request, Handler $handler): Response
	{
		$config = [
			'param' => 'action',
			'map'   => [
				'PUT'    => ['update'],
				'DELETE' => ['remove']
			]
		];

		foreach ($this->app->getConfig('*', 'middleware', []) as $middleware) {
			if (isset($middleware['class']) && $middleware['class'] == __CLASS__) {
				$config = $middleware + $config;
			}
		}

		$param = $config['param'];
		$map   = $config['map'];

		if ($request->getMethod() == 'POST') {
			$action = $request->getQueryParams()[$param] ?? ($request->getParsedBody()[$param] ?? NULL);

			foreach ($map as $method => $actions) {
				if (in_array($action, $actions)) {
					$request = $request->withMethod($method);
					break;
				}
			}
		}

		return $handler->handle($request);
	}
}
