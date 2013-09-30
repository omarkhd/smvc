<?php
namespace smvc\controller;
use Exception, ReflectionClass;

/**
 *	@author omarkhd
 *	@package core.controller
 */
abstract class PageController
{
	protected $request;
	
	public final function __construct()
	{
		$this->checkControllerDefinition();
		$this->request = new Request();
	}

	private function checkControllerDefinition()
	{
		$controller = new ReflectionClass($this);
		$protected_methods = array('init', 'before', 'execute', 'after', 'onException');
		foreach($protected_methods as $method_name) {
			$method = $controller->getMethod($method_name);
			if(!$method->isProtected()) {
				throw new Exception(sprintf('%s::%s should be a protected method',
					$controller->getShortName(), $method->getShortName()));
			}
		}
		$on_exception = $controller->getMethod('onException');
		$on_exception_parameters = $on_exception->getParameters();
		if(count($on_exception_parameters) != 1) {
			throw new Exception(sprintf('%s::%s should receive an Exception parameter',
				$controller->getShortName(), $on_exception->getShortName()));
		}
		$parameter = $on_exception_parameters[0];
		$param_type = $parameter->getClass();
		if($param_type == null || !$param_type->isInstance(new Exception())) {
			throw new Exception(sprintf('Parameter $%s from %s::%s method should be an Exception instance',
				$parameter->getName(), $controller->getShortName(), $on_exception->getShortName()));
		}
	}
	
	protected function init() {}
	protected function before() {}
	protected abstract function execute();
	protected function after() {}

	protected function onException(Exception $exception)
	{
		throw $exception;
	}
	
	/**
	 *	starts the executing sequence
	 */
	public final function run()
	{
		try {
			$this->init();
			$this->before();
			$this->execute();
			$this->after();
		}
		catch(Exception $e) {
			$this->onException($e);
		}
	}
}
