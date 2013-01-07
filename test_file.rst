.. src/api.php generated using docpx on 01/07/13 06:24pm
Functions
---------
.. function::  signal
   Creates a new signal processr.:param string|integer|object $signal: param
:param object $callable: param
:param object|boolean: return
:param string|integer|object $signal: param
:param object $callable: param
.. function::  null_exhaust
   Creates a never exhausting signal processr.:param callable|process $process: param
:param object: return
:param callable|process $process: param
.. function::  high_priority
   Creates or sets a process with high priority.:param callable|process $process: param
:param object: return
:param callable|process $process: param
.. function::  low_priority
   Creates or sets a process with low priority.:param callable|process $process: param
:param object: return
:param callable|process $process: param
.. function::  priority
   Sets a process priority.:param callable|process $process: param
:param integer $priority: param
:param object: return
:param callable|process $process: param
:param integer $priority: param
.. function::  remove_process
   Removes an installed signal process.:param string|integer|object $signal: param
:param object $process: param
:param void: return
:param string|integer|object $signal: param
:param object $process: param
.. function::  emit
   Signals an event.:param string|integer|object $signal: param
:param array $vars: param
:param object $event: param
:param object: return
:param string|integer|object $signal: param
:param array $vars: param
:param object $event: param
.. function::  signal_history
   Returns the signal history.:param array: return
.. function::  register_signal
   Registers a signal in the processor.:param string|integer|object $signal: param
:param object: return
:param string|integer|object $signal: param
.. function::  search_signals
   Searches for a signal in storage returning its storage node if found,
optionally the index can be returned.:param string|int|object $signal: param
:param boolean $index: param
:param null|array: return
:param string|int|object $signal: param
:param boolean $index: param
.. function::  loop
   Starts the XPSPL loop.:param void: return
.. function::  shutdown
   Sends the loop the shutdown signal.:param void: return
.. function::  import
   Import a module.:param string $name: param
:param string|null $dir: param
:param void: return
:param string $name: param
:param string|null $dir: param
.. function::  before
   Registers a function to interrupt the signal stack before a signal fires,
allowing for manipulation of the event before it is passed to processs.:param string|object $signal: param
:param object $process: param
:param boolean: return
:param string|object $signal: param
:param object $process: param
.. function::  after
   Registers a function to interrupt the signal stack after a signal fires.
allowing for manipulation of the event after it is passed to processs.:param string|object $signal: param
:param object $process: param
:param boolean: return
:param string|object $signal: param
:param object $process: param
.. function::  XPSPL
   Returns the XPSPL processor.:param object: return
.. function::  clean
   Cleans any exhausted signal queues from the processor.:param boolean $history: param
:param void: return
:param boolean $history: param
.. function::  delete_signal
   Delete a signal from the processor.:param string|object|int $signal: param
:param boolean $history: param
:param boolean: return
:param string|object|int $signal: param
:param boolean $history: param
.. function::  erase_signal_history
   Erases any history of a signal.:param string|object $signal: param
:param void: return
:param string|object $signal: param
.. function::  disable_signaled_exceptions
   Disables the exception processr.:param boolean $history: param
:param void: return
:param boolean $history: param
.. function::  erase_history
   Cleans out the entire event history.:param void: return
.. function::  save_signal_history
   Sets the flag for storing the event history.:param boolean $flag: param
:param void: return
:param boolean $flag: param
.. function::  listen
   Registers a new event listener object in the processor.:param object $listener: param
:param void: return
:param object $listener: param
.. function::  dir_include
   Performs a inclusion of the entire directory content, including 
subdirectories, with the option to start a listener once the file has been 
included.:param string $dir: param
:param boolean $listen: param
:param string $path: param
:param void: return
:param string $dir: param
:param boolean $listen: param
:param string $path: param
.. function::  $i
   This is some pretty narly code but so far the fastest I have been able 
to get this to run... function::  current_signal
   Returns the current signal in execution.:param integer $offset: param
:param object: return
:param integer $offset: param
.. function::  current_event
   Returns the current event in execution.:param integer $offset: param
:param object: return
:param integer $offset: param
.. function::  on_shutdown
   Call the provided function on processor shutdown.:param callable|object $function: param
:param object: return
:param callable|object $function: param
.. function::  on_start
   Call the provided function on processor start.:param callable|object $function: param
:param object: return
:param callable|object $function: param
.. function::  XPSPL_flush
   Empties the storage, history and clears the current state.:param void: return
