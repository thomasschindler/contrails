<?php
	ob_start();		 
	/**
	*	include necessary files:
	*	pre allows you to do some prelim checks before actually starting anything.
	*/
	if(!@include_once("../inc/etc/config/".$_SERVER['HTTP_HOST'].".pre.php"))
	{
		@include_once("../inc/etc/config/default.pre.php");
	}
	/**
	*	get the system
	*/
	include_once('../inc/bootstrap.php');
    /**
    *	do some logging
    **/
    $LOG = &LOG::singleton();
	$LOG->start();
	/*
	*	initialize the client
	*/
	$CLIENT = &CLIENT::singleton(true);
	/**
	* 	add caching - this will output a cached version of the requested file if apprpriate
	*/
	$CACHE = &CACHE::singleton();
	/*
	*	create the output controller
	*/
	$OPC = &OPC::singleton();
	/*
	* create the main controller
	*/
	$MC  = &MC::singleton();
	/**
	*	start the session
	*/
	$SESS = &SESS::singleton();
	$SESS->start();
	/*
	*	instantiate the model factory
	*/
	$MOF  = &MF::singleton();
	/**
	* define the action
	*/
    $action = array
    (
    	'directevent' => util::getPost('directevent'),
    	'event' => util::getPost('event'),
        'mod'   => util::getPost('mod'),
    );
    $continue = true;
    if ($action['directevent']) 
    {
    	$continue = $MC->call_direct_action($action);
    }    
    if ($action['mod'] && $continue) 
    {
    	$MC->call_action($action);
    }
    /**
    *	set the start view
    */
	list($vid, $mod, $view) = $OPC->get_start_view();
	/**
	* call the view  and output
	*/
    $OPC->call($mod, $view, $vid);
	/**
	*	finalize cache
	*/
	$CACHE->write();
	/**
	*	finalize the models (actually write all changes to the db)
	*/
	$MOF->flush();
	/**
	*	write the log entry
	*/
	$LOG->end();
?>
