<?php

namespace EllisLab\ExpressionEngine\Service\Event;

use EllisLab\ExpressionEngine\Library\Mixin\Mixin as MixinInterface;

use Closure;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Event Mixin
 *
 * Allows any Mixable class to receive and emit events.
 *
 * @package		ExpressionEngine
 * @subpackage	Event
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Mixin implements MixinInterface {

	/**
	 * @var The parent scope
	 */
	protected $scope;

	/**
	 * @var An event emitter instance
	 */
	protected $emitter;

	/**
	 * @param Object $scope The parent scope
	 */
	public function __construct($scope)
	{
		$this->scope = $scope;

		if ($scope instanceOf Reflexive)
		{
			$this->bootReflexiveEvents();
		}
	}

	/**
	 * Get the mixin name
	 *
	 * @return String mixin name
	 */
	public function getName()
	{
		return 'Event';
	}

	/**
	 * Initialize the reflexive event listeners if the class supports it.
	 */
	protected function bootReflexiveEvents()
	{
		foreach ($this->scope->getEvents() as $event)
		{
			$this->on($event, function() use ($event) {
				$args = func_get_args();
				$model = array_shift($args);
				$event = 'on'.ucfirst($event);

				call_user_func_array(array($model, $event), $args);
			});
		}
	}

	/**
	 * Bind an event listener
	 *
	 * @param String $event Event name
	 * @param Closure $listener Event listener
	 * @return Scope object
	 */
	public function on($event, Closure $listener)
	{
		$this->getEventEmitter()->on($event, $listener);

		return $this->scope;
	}

	/**
	 * Emit an event
	 *
	 * @param String $event Event name
	 * @param Mixed ...rest Additional arguments to pass to the listener
	 * @return Scope object
	 */
	public function emit(/* $event, ...$args */)
	{
		$args = func_get_args();
		array_splice($args, 1, 0, array($this->scope));

		call_user_func_array(
			array($this->getEventEmitter(), 'emit'),
			$args
		);

		return $this->scope;
	}

	/**
	 * Get the current event emitter instance
	 *
	 * @return Emitter object
	 */
	public function getEventEmitter()
	{
		if ( ! isset($this->emitter))
		{
			$this->setEventEmitter($this->newEventEmitter());
		}

		return $this->emitter;
	}

	/**
	 * Get the current event emitter instance
	 *
	 * @param Emitter $emitter Event emitter instance
	 * @return Scope object
	 */
	public function setEventEmitter(Emitter $emitter)
	{
		$this->emitter = $emitter;

		return $this->scope;
	}

	/**
	 * Create the default event emitter
	 *
	 * @return Emitter object
	 */
	protected function newEventEmitter()
	{
		return new Emitter();
	}

}