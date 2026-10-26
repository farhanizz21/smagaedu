<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class proyek_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'judul',
				'label' => 'Judul proyek',
				'rules' => 'required'
			],
			[
				'field' => 'tgl_mulai',
				'label' => 'Tanggal Mulai',
				'rules' => 'required'
			],
			[
				'field' => 'tgl_selesai',
				'label' => 'Tanggal Selesai',
				'rules' => 'required'
			],
			// [
			// 	'field' => 'berkas',
			// 	'label' => 'Berkas',
			// 	'rules' => 'uploaded[berkas]|max_size[berkas,5120]' // 5MB
			// ],
			[
				'field' => 'namaMapel',
				'label' => 'Mata Pelajaran',
				'rules' => 'required'
			]
        ];
	}

	public function get_all($created_by = null, $limit = NULL, $offset = 0)
	{
		$this->db->select('p.*');
		$this->db->from('proyek p');
		$this->db->where('p.deleted_at', NULL, FALSE);
		if ($created_by !== null) {
			$this->db->where('p.created_by', $created_by);
		} elseif (has_role('admin')) {
			$this->db->where('p.created_by', $this->session->userdata('uuid'));
		}
		$this->db->order_by('p.modified_at', 'DESC');
		if ($limit !== NULL) {
			$this->db->limit((int) $limit, (int) $offset);
		}
		$data = $this->db->get()->result();

		return $data;
	} 

	public function count_all($created_by = null)
	{
		$this->db->from('proyek p');
		$this->db->where('p.deleted_at', NULL, FALSE);
		if ($created_by !== null) {
			$this->db->where('p.created_by', $created_by);
		} elseif (has_role('admin')) {
			$this->db->where('p.created_by', $this->session->userdata('uuid'));
		}
		return $this->db->count_all_results();
	}

	/**
	 * Get all proyek yang mapel-nya diampu untuk kumpulan mapel UUID tertentu.
	 * Digunakan untuk memfilter proyek yang dapat dilihat siswa berdasarkan kelasnya.
	 *
	 * @param array $mapel_uuids Daftar UUID mapel yang diampu untuk kelas siswa
	 * @return array Daftar proyek
	 */
	public function get_all_by_mapel_uuids($mapel_uuids = [], $limit = NULL, $offset = 0, $siswa_uuid = NULL)
	{
		if (empty($mapel_uuids)) {
			return [];
		}

		$this->db->from('proyek p');
		$this->db->where('p.deleted_at', NULL, FALSE);
		$this->db->where_in('p.mapel_uuid', $mapel_uuids);
		$this->apply_siswa_project_scope($siswa_uuid);
		$this->db->order_by('p.modified_at', 'DESC');
		if ($limit !== NULL) {
			$this->db->limit((int) $limit, (int) $offset);
		}
		$data = $this->db->get()->result();

		return $data;
	}

	public function count_by_mapel_uuids($mapel_uuids = [], $siswa_uuid = NULL)
	{
		if (empty($mapel_uuids)) {
			return 0;
		}

		$this->db->from('proyek p');
		$this->db->where('p.deleted_at', NULL, FALSE);
		$this->db->where_in('p.mapel_uuid', $mapel_uuids);
		$this->apply_siswa_project_scope($siswa_uuid);
		return $this->db->count_all_results();
	}

	private function apply_siswa_project_scope($siswa_uuid)
	{
		if ($siswa_uuid === NULL) {
			return;
		}

		$this->db->where(
			'EXISTS (
				SELECT 1 FROM kelompok_siswa ks
				INNER JOIN kelompok k ON k.uuid = ks.kelompok_uuid
				WHERE ks.siswa_uuid = ' . $this->db->escape($siswa_uuid) . '
					AND k.proyek_uuid = p.uuid
					AND ks.deleted_at IS NULL
			)',
			NULL,
			FALSE
		);
	}
	
	public function insert($berkas)
	{
		$uuid = Uuid::uuid4()->toString();
        $judul = $this->input->post('judul');
        $mapel_uuid = $this->input->post('namaMapel');
        $deskripsi = $this->input->post('deskripsi');
        $tgl_selesai = $this->input->post('tgl_selesai');
        $tgl_mulai = $this->input->post('tgl_mulai');
        $user_uuid = $this->session->userdata('uuid');


		$data = array(
			'uuid' => $uuid,
			'judul' => $judul,
			'mapel_uuid' => $mapel_uuid,
			'file' => $berkas,
			'deskripsi' => $deskripsi,
			'tgl_mulai' => $tgl_mulai,
			'tgl_selesai' => $tgl_selesai,
			'created_by' => $user_uuid
		);

		$this->db->insert('proyek', $data);
		$this->db->last_query();  // Menampilkan query terakhir yang dijalankan

		
		if ($this->db->affected_rows() > 0) {
			return $uuid; 
		} else {
			return false;
		}
	}

	public function get_by_uuid($uuid)
	{
		$this->db->where('uuid', $uuid);
		apply_admin_creator_scope('created_by');
		$data = $this->db->get('proyek')->row();

		return $data;
	}

	public function get_komentar_by_proyek_uuid($uuid)
	{
		$this->db->where('proyek_uuid', $uuid);
		$data = $this->db->get('komentar')->row();

		return $data;
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->where('uuid', $uuid);
		apply_admin_creator_scope('created_by');
		$this->db->update('proyek', $data);
		return($this->db->affected_rows() > 0) ? true :false;
	}

	
}
?>