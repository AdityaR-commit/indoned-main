<?php

class M_index extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$dbUsername = getSession('dbUsername');
		if ($dbUsername) {
			$this->load->database($dbUsername, FALSE, TRUE);
		}
	}
}
