<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logger
{
	public function log($type, $message)
	{
		date_default_timezone_set('America/New_York');
		echo '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
	}
}

/* End of file Logger.php */
/* Location: ./application/libraries/Logger.php */
