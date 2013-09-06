<?php
namespace smvc\model;
use Exception;

class SubmodelManager
{
	private $container;
	private $master;

	public function __construct(Model $master_model)
	{
		$this->master = $master_model;
		$this->container = array();
	}

	public function register($name, Model $model)
	{
		$this->container[$name] = $model;
	}

	public function __get($property)
	{
		if(!isset($this->container[$property])) {
			throw new Exception(sprintf('Not a registered model named [%s] in [%s]', $this->master, $property));
		}
		$submodel = $this->container[$property];
		$submodel->setStrategy($this->master->getStrategy());
		return $submodel;
	}
}