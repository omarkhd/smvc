<?php

namespace system\controller;

class Controller
{
	private function __construct() {}

	public static function run() //the entry point for our system
	{
		$instance = new self();
		$instance->init();
		$instance->handleRequest();
	}

	private function init()
	{
		//initializing the registry, by default the request registry
		\system\controller\ApplicationHelper::initRegistry();
	}

	private function handleRequest()
	{
		$request = new \system\controller\Request();
		$resolver = new \system\command\CommandResolver();
		$cmd = $resolver->getCommand($request);
		$cmd->execute();
		\system\model\DatabaseFactory::closeConnections();
	}
}
