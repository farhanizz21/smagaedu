<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Periode_akademik_model extends CI_Model {

	public function get_current()
	{
		return $this->db->get_where('periode_akademik', array('id' => 1))->row();
	}

	public function update_current($pekan, $semester)
	{
		if (!is_int($pekan) || $pekan < 1 || $pekan > 20
			|| !is_int($semester) || !in_array($semester, array(1, 2), true)) {
			return false;
		}

		return $this->db->where('id', 1)->update('periode_akademik', array(
			'pekan' => $pekan,
			'semester' => $semester
		));
	}
}
