<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class materi_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'judul',
				'label' => 'Nama Pekan',
				'rules' => 'required'
			],
			[
				'field' => 'namaMapel',
				'label' => 'Mata Pelajaran',
				'rules' => 'required'
			],
			[
				'field' => 'semester',
				'label' => 'Semester',
				'rules' => 'required|in_list[1,2]'
			],
			[
				'field' => 'pekan_ke',
				'label' => 'Pekan',
				'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[20]'
			],
        ];
	}

	public function insert($thumbnail, $mapel_uuid = NULL) {
		$uuid = Uuid::uuid4()->toString();
		$judul = $this->input->post('judul');
		$mapel_uuid = $mapel_uuid ?: $this->input->post('namaMapel');

		$data = array(
			'uuid'      => $uuid,
			'mapel_uuid'=> $mapel_uuid,
			'judul'     => $judul,
			'semester'  => (int) $this->input->post('semester'),
			'pekan_ke'  => (int) $this->input->post('pekan_ke'),
			'thumbnail' => $thumbnail,
			'created_by' => $this->session->userdata('uuid')
		);
		$this->db->insert('materi', $data);
		return ($this->db->affected_rows() > 0);
	}

	public function get_all()
	{
		$this->db->select('m.*, g.nama, m.uuid AS materi_uuid, m.created_by AS materi_created_by');
		$this->db->join('guru g', 'm.created_by = g.uuid', 'left');
		$this->db->where('m.deleted_at', NULL, FALSE);
		apply_admin_creator_scope('m.created_by');
		$this->db->order_by('m.modified_at', 'DESC');
		$data = $this->db->get('materi m')->result();

		return $data;
	} 

	public function week_exists($mapel_uuid, $semester, $week, $except_uuid = NULL)
	{
		$this->db->where('mapel_uuid', $mapel_uuid);
		$this->db->where('semester', $semester);
		$this->db->where('pekan_ke', $week);
		$this->db->where('deleted_at', NULL, FALSE);
		if ($except_uuid !== NULL) {
			$this->db->where('uuid !=', $except_uuid);
		}
		return $this->db->count_all_results('materi') > 0;
	}

	public function get_by_mapel_uuid($mapel_uuid, $semester = NULL)
	{
		$this->db->select('m.*, g.nama, m.uuid AS materi_uuid, m.created_by AS materi_created_by');
		$this->db->join('guru g', 'm.created_by = g.uuid', 'left');
		$this->db->where('m.mapel_uuid', $mapel_uuid);
		if ($semester !== NULL) {
			$this->db->where('m.semester', $semester);
		}
		$this->db->where('m.deleted_at', NULL, FALSE);
		apply_admin_creator_scope('m.created_by');
		$this->db->order_by('m.id', 'ASC');
		$data = $this->db->get('materi m')->result();
		$used_weeks = array();
		$unassigned = array();

		foreach ($data as $item) {
			$week = (int) $item->pekan_ke;
			if ($week >= 1 && $week <= 20 && !isset($used_weeks[$week])) {
				$used_weeks[$week] = $item->uuid;
			} else {
				$unassigned[] = $item;
			}
		}

		foreach ($unassigned as $item) {
			for ($week = 1; $week <= 20; $week++) {
				if (!isset($used_weeks[$week])) {
					$this->db->where('uuid', $item->uuid)->update('materi', array('pekan_ke' => $week));
					$item->pekan_ke = $week;
					$used_weeks[$week] = $item->uuid;
					break;
				}
			}
		}

		usort($data, function ($left, $right) {
			return (int) $left->pekan_ke <=> (int) $right->pekan_ke;
		});

		return $data;
	} 

	public function get_by_uuid($uuid)
	{
		$this->db->select('m.*, g.nama, m.uuid AS materi_uuid, m.created_by AS materi_created_by');
		$this->db->join('guru g', 'm.created_by = g.uuid', 'left');
		$this->db->where('m.uuid', $uuid);
		$this->db->where('m.deleted_at', NULL, FALSE);
		apply_admin_creator_scope('m.created_by');
		return $this->db->get('materi m')->row();
	}

	public function update($uuid, $thumbnail)
	{
		$data = array(
			'judul'      => $this->input->post('judul'),
			'semester'   => (int) $this->input->post('semester'),
			'pekan_ke'   => (int) $this->input->post('pekan_ke'),
			'thumbnail'  => $thumbnail,
			'modified_at'=> date("Y-m-d H:i:s")
		);
		$this->db->where('uuid', $uuid);
		apply_admin_creator_scope('created_by');
		$this->db->update('materi', $data);
		return ($this->db->affected_rows() > 0);
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->where('uuid', $uuid);
		apply_admin_creator_scope('created_by');
		$this->db->update('materi', $data);
		return($this->db->affected_rows() > 0) ? true :false;
	}

	
}
?>