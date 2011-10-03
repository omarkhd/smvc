<?php

namespace system\controller;

class Controller
{
	private function __construct() {}

	public static function Run() //the entry point for our system
	{
		$instance = new self();
		$instance->Init();
		$instance->HandleRequest();
	}

	private function Init()
	{
		//initializing the registry, by default the request registry
		\system\controller\ApplicationHelper::InitRegistry();
	}

	private function HandleRequest()
	{
		$request = new \system\controller\Request();
		$resolver = new \system\command\CommandResolver();
		$cmd = $resolver->GetCommand($request);
		$cmd->Execute();
	}
}
