<?php

namespace system\controller;
use \system\controller\Request;
use \system\controller\ApplicationHelper;
use \system\command\CommandResolver;
use \system\model\DatabaseFactory;

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
