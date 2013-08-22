<?php

namespace smvc\controller;
use \smvc\controller\Request;
use \smvc\controller\ApplicationHelper;
use \smvc\command\CommandResolver;
use \smvc\model\DatabaseFactory;

class FrontController
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
		ApplicationHelper::initRegistry();
	}

	private function handleRequest()
	{
		$request = new Request();
		$resolver = new CommandResolver();
		$command = $resolver->getCommand($request);
		$command->run();
		DatabaseFactory::closeConnections();
	}
}
