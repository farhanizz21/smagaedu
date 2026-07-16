<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class materi_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'judul',
				'label' => 'Judul materi',
				'rules' => 'required'
			],
			[
				'field' => 'namaMapel',
				'label' => 'Mata Pelajaran',
				'rules' => 'required'
			],
        ];
	}

	public function insert($thumbnail) {
		$uuid = Uuid::uuid4()->toString();
		$judul = $this->input->post('judul');
		$mapel_uuid = $this->input->post('namaMapel');

		$data = array(
			'uuid'      => $uuid,
			'mapel_uuid'=> $mapel_uuid,
			'judul'     => $judul,
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
		$this->db->order_by('m.modified_at', 'DESC');
		$data = $this->db->get('materi m')->result();

		return $data;
	} 

	public function get_by_mapel_uuid($mapel_uuid)
	{
		$this->db->select('m.*, g.nama, m.uuid AS materi_uuid, m.created_by AS materi_created_by');
		$this->db->join('guru g', 'm.created_by = g.uuid', 'left');
		$this->db->where('m.mapel_uuid', $mapel_uuid);
		$this->db->where('m.deleted_at', NULL, FALSE);
		$this->db->order_by('m.modified_at', 'DESC');
		$data = $this->db->get('materi m')->result();

		return $data;
	} 

	public function get_by_uuid($uuid)
	{
		$this->db->select('m.*, g.nama, m.uuid AS materi_uuid, m.created_by AS materi_created_by');
		$this->db->join('guru g', 'm.created_by = g.uuid', 'left');
		$this->db->where('m.uuid', $uuid);
		$this->db->where('m.deleted_at', NULL, FALSE);
		return $this->db->get('materi m')->row();
	}

	public function update($uuid, $thumbnail)
	{
		$data = array(
			'judul'      => $this->input->post('judul'),
			'thumbnail'  => $thumbnail,
			'modified_at'=> date("Y-m-d H:i:s")
		);
		$this->db->update('materi', $data, array('uuid' => $uuid));
		return ($this->db->affected_rows() > 0);
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('materi', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}

	
}
?>