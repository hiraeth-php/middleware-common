<?php

namespace Hiraeth\Middleware;

use Hiraeth;

use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

use Psr\Http\Message\UploadedFileInterface as UploadedFile;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class ExpandInputs implements Middleware
{
	/**
	 *
	 */
	public function process(Request $request, Handler $handler): Response
	{
		$query  = array();
		$params = array();
		$files  = array();

		foreach ($request->getQueryParams() as $key => $value) {
			if (strpos($key, '_') === FALSE) {
				$query[$key] = $value;
				continue;
			}

			$head = &$query;

			foreach (explode('_', $key) as $segment) {
				$head = &$head[$segment];
			}

			$head = $value;
		}

		foreach ($request->getParsedBody() as $key => $value) {
			if (strpos($key, '_') === FALSE) {
				$params[$key] = $value;
				continue;
			}

			$head = &$params;

			foreach (explode('_', $key) as $segment) {
				$head = &$head[$segment];
			}

			$head = $value;
		}

		foreach ($request->getUploadedFiles() as $key => $value) {
			if ($value instanceof UploadedFile && !$value->getClientFilename()) {
				continue;
			}

			if (strpos($key, '_') === FALSE) {
				$files[$key] = $value;
				continue;
			}

			$head = &$files;

			foreach (explode('_', $key) as $segment) {
				$head = &$head[$segment];
			}

			if ($value->getSize()) {
				$head = $value;
			}
		}

		$request = $request
			-> withQueryParams($query)
			-> withParsedBody($params)
			-> withUploadedFiles($files)
		;

		return $handler->handle($request);
	}
}
