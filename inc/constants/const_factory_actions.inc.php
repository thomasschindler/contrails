<?
	/**
	 *	The purpose of this file is to store the positions of each status within the _status array base_model
	 *	abstract class. The point is to avoid magic numbers and use meaningful names for the positions.
	 *
	 */
	final class factory_actions{
		const Update 	= 0;
		const Create 	= 1;
		const Delete 	= 2;
		const Unchanged	= 3;
	} 