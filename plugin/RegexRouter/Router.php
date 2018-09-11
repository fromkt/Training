<?php

class Router {

	private $routes = array();

	public function route($route_name, $method, $pattern, $callback, $args) {
		$pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
		$this->routes[$route_name] = array(
			'method' => strtolower($method),
			'pattern' => $pattern,
			'callback' => $callback,
			'args' => $args
		);
	}

	public function execute($uri) {
		foreach ($this->routes as $name => $value) {
			$http_method = strtolower($_SERVER['REQUEST_METHOD']);

			if('post' == $http_method) {
				$http_method = $_POST[strtolower('_method')] == 'get' ? 'post' : $_POST[strtolower('_method')];
			}

			if ($value['method'] == $http_method && preg_match($value['pattern'], $uri, $params) === 1) {
				array_shift($params);

                if(count($value['args']) > 0) {
	                return call_user_func_array($value['callback'], $value['args']);
                } else {
                    return call_user_func($value['callback']);
                }
			}
		}
	}

}
