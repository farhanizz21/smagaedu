<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class ujian_model extends CI_Model {

    public function rules()
	{
		return[
			[
				'field' => 'namaUjian',
				'label' => 'Nama Ujian',
				'rules' => 'required'
			],
			[
				'field' => 'jenis_penilaian',
				'label' => 'Jenis Penilaian',
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
			]
		];
	}

	public function get_by_sub_materi($sub_materi_uuid)
	{
		$this->db->select("u.*,m.nama AS mapel_nama, DATE_FORMAT(u.tgl_mulai, '%H.%m WIB, %d %M %Y') as tgl_mulai_formatted, DATE_FORMAT(u.tgl_selesai, '%H.%m WIB, %d %M %Y') as tgl_selesai_formatted, g.nama AS guru_nama", FALSE);
		$this->db->from('ujian u');
		$this->db->join('guru g', 'g.uuid = u.created_by', 'left');
		$this->db->join('mapel m', 'm.uuid = u.mapel_uuid', 'left');
		$this->db->where('u.sub_materi_uuid', $sub_materi_uuid);
		$this->db->where('u.deleted_at', NULL, FALSE);
		$this->db->order_by('u.modified_at', 'DESC');
		
		return $this->db->get()->result();
	}

    public function get_all()
	{
		$this->db->select("u.*,m.nama AS mapel_nama, DATE_FORMAT(u.tgl_mulai, '%H.%m WIB, %d %M %Y') as tgl_mulai_formatted, DATE_FORMAT(u.tgl_selesai, '%H.%m WIB, %d %M %Y') as tgl_selesai_formatted, g.nama AS guru_nama, b.judul AS bab_judul", FALSE);
		$this->db->from('ujian u');
		$this->db->join('guru g', 'g.uuid = u.created_by', 'left');
		$this->db->join('mapel m', 'm.uuid = u.mapel_uuid', 'left');
		$this->db->join('bab b', 'b.uuid = u.bab_uuid', 'left');
		$this->db->where('u.deleted_at', NULL, FALSE);
		$this->db->order_by('u.modified_at', 'DESC');
		
		return $this->db->get()->result();
	}

	/**
	 * Get all ujian yang mapel-nya diampu untuk kumpulan mapel UUID tertentu.
	 * Digunakan untuk memfilter ujian yang dapat dilihat siswa berdasarkan kelasnya.
	 *
	 * @param array $mapel_uuids Daftar UUID mapel yang diampu untuk kelas siswa
	 * @return array Daftar ujian
	 */
	public function get_all_by_mapel_uuids($mapel_uuids = [])
	{
		if (empty($mapel_uuids)) {
			return [];
		}

		$this->db->select("u.*,m.nama AS mapel_nama, DATE_FORMAT(u.tgl_mulai, '%H.%m WIB, %d %M %Y') as tgl_mulai_formatted, DATE_FORMAT(u.tgl_selesai, '%H.%m WIB, %d %M %Y') as tgl_selesai_formatted, g.nama AS guru_nama, b.judul AS bab_judul", FALSE);
		$this->db->from('ujian u');
		$this->db->join('guru g', 'g.uuid = u.created_by', 'left');
		$this->db->join('mapel m', 'm.uuid = u.mapel_uuid', 'left');
		$this->db->join('bab b', 'b.uuid = u.bab_uuid', 'left');
		$this->db->where('u.deleted_at', NULL, FALSE);
		$this->db->where_in('u.mapel_uuid', $mapel_uuids);
		$this->db->order_by('u.modified_at', 'DESC');
		
		return $this->db->get()->result();
	}
	
    public function insert()
	{
		$uuid = Uuid::uuid4()->toString();
        $mapel_uuid = $this->input->post('namaMapel');
		$namaUjian = $this->input->post('namaUjian');
		$tgl_mulai = $this->input->post('tgl_mulai');
		$tgl_selesai = $this->input->post('tgl_selesai');
		$user = $this->session->userdata('uuid');

				$jenis_penilaian = $this->input->post('jenis_penilaian');
		$durasi = $this->input->post('durasi') ? (int)$this->input->post('durasi') : 60;
		
		$data = array(
			'uuid' => $uuid,
			'mapel_uuid' => $mapel_uuid,
			'nama' => $namaUjian,
			'jenis_penilaian' => $jenis_penilaian,
			'tgl_mulai' => $tgl_mulai,
			'tgl_selesai' => $tgl_selesai,
			'durasi' => $durasi,
			'created_by' => $user
		);

		$this->db->insert('ujian', $data);
		if ($this->db->affected_rows() > 0) {
			// return true;
			return $data['uuid'];
		} else {
			return false;
		}
	}

	public function insert_sub($sub_materi_uuid)
	{
		$uuid = Uuid::uuid4()->toString();
		$mapel_uuid = $this->input->post('namaMapel');
		$namaUjian = $this->input->post('namaUjian');
		$tgl_mulai = $this->input->post('tgl_mulai');
		$tgl_selesai = $this->input->post('tgl_selesai');
		$user = $this->session->userdata('uuid');

			$jenis_penilaian = $this->input->post('jenis_penilaian');
		$durasi = $this->input->post('durasi') ? (int)$this->input->post('durasi') : 60;
		
		$data = array(
			'uuid' => $uuid,
			'mapel_uuid' => $mapel_uuid,
			'sub_materi_uuid' => $sub_materi_uuid,
			'nama' => $namaUjian,
			'jenis_penilaian' => $jenis_penilaian,
			'tgl_mulai' => $tgl_mulai,
			'tgl_selesai' => $tgl_selesai,
			'durasi' => $durasi,
			'created_by' => $user
		);

		$this->db->insert('ujian', $data);
		if ($this->db->affected_rows() > 0) {
			return $data['uuid'];
		} else {
			return false;
		}
	}

	/**
	 * Insert ujian yang terhubung langsung dengan bab (sub bab).
	 * Digunakan saat membuat ujian dari form Tambah Sub Bab.
	 *
	 * @param string $bab_uuid UUID bab (sub bab) yang dibuat
	 * @param string $mapel_uuid UUID mata pelajaran
	 * @return string|bool UUID ujian baru atau false jika gagal
	 */
	public function insert_bab($bab_uuid, $mapel_uuid)
	{
		$uuid = Uuid::uuid4()->toString();
		$namaUjian = $this->input->post('namaUjian');
		$tgl_mulai = $this->input->post('tgl_mulai');
		$tgl_selesai = $this->input->post('tgl_selesai');
		$user = $this->session->userdata('uuid');

			$jenis_penilaian = $this->input->post('jenis_penilaian');
		$durasi = $this->input->post('durasi') ? (int)$this->input->post('durasi') : 60;
		
		$data = array(
			'uuid' => $uuid,
			'mapel_uuid' => $mapel_uuid,
			'bab_uuid' => $bab_uuid,
			'nama' => $namaUjian,
			'jenis_penilaian' => $jenis_penilaian,
			'tgl_mulai' => $tgl_mulai,
			'tgl_selesai' => $tgl_selesai,
			'durasi' => $durasi,
			'created_by' => $user
		);

		$this->db->insert('ujian', $data);
		if ($this->db->affected_rows() > 0) {
			return $data['uuid'];
		} else {
			return false;
		}
	}
	public function update($uuid)
	{
		$durasi_post = $this->input->post('durasi');
		$data = array(
			'nama'            => $this->input->post('namaUjian'),
			'mapel_uuid'      => $this->input->post('namaMapel'),
			'jenis_penilaian' => $this->input->post('jenis_penilaian'),
			'tgl_mulai'       => $this->input->post('tgl_mulai'),
			'tgl_selesai'     => $this->input->post('tgl_selesai'),
			'durasi'          => $durasi_post ? (int)$durasi_post : 60,
			'modified_at'     => date('Y-m-d H:i:s')
		);

		$this->db->update('ujian', $data, array('uuid' => $uuid));
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Check jika ada siswa yang sudah mengerjakan (submit jawaban) ujian tertentu.
	 * Ujian yang sudah dikerjakan tidak dapat di edit.
	 *
	 * @param string $ujian_uuid UUID ujian
	 * @return bool true jika ada siswa yang sudah mengerjakan
	 */
	public function has_attempts($ujian_uuid)
	{
		$this->db->where('ujian_uuid', $ujian_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$count = $this->db->count_all_results('ujian_jawaban');
		return $count > 0;
	}

	/**
	 * Get ujian yang terhubung dengan bab (sub bab) tertentu.
	 *
	 * @param string $bab_uuid UUID bab (sub bab)
	 * @return array Daftar ujian
	 */
	public function get_by_bab($bab_uuid)
	{
		$this->db->select("u.*,m.nama AS mapel_nama, DATE_FORMAT(u.tgl_mulai, '%H.%m WIB, %d %M %Y') as tgl_mulai_formatted, DATE_FORMAT(u.tgl_selesai, '%H.%m WIB, %d %M %Y') as tgl_selesai_formatted, g.nama AS guru_nama", FALSE);
		$this->db->from('ujian u');
		$this->db->join('guru g', 'g.uuid = u.created_by', 'left');
		$this->db->join('mapel m', 'm.uuid = u.mapel_uuid', 'left');
		$this->db->where('u.bab_uuid', $bab_uuid);
		$this->db->where('u.deleted_at', NULL, FALSE);
		$this->db->order_by('u.modified_at', 'DESC');
		
		return $this->db->get()->result();
	}

	public function delete_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('ujian', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function get_by_uuid($uuid)
	{
		// $data = $this->db->get_where('ujian', array('uuid' => $uuid))->row();

		$this->db->select("u.*,m.nama AS mapel_nama, DATE_FORMAT(u.tgl_mulai, '%H.%m WIB, %d %M %Y') as tgl_mulai_formatted, DATE_FORMAT(u.tgl_selesai, '%H.%m WIB, %d %M %Y') as tgl_selesai_formatted, g.nama AS guru_nama", FALSE);
		$this->db->from('ujian u');
		$this->db->join('guru g', 'g.uuid = u.created_by', 'left');
		$this->db->join('mapel m', 'm.uuid = u.mapel_uuid', 'left');
		$this->db->where('u.deleted_at', NULL, FALSE);
		$this->db->where('u.uuid', $uuid);
		$this->db->order_by('u.modified_at', 'DESC');
		
		return $this->db->get()->row();
		
		// return $data;
	}

	public function get_ujian_count_by_sub_materi($sub_materi_uuid)
	{
		$this->db->where('sub_materi_uuid', $sub_materi_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		return $this->db->count_all_results('ujian');
	}

	public function get_jawaban_siswa_by_ujian_siswa_uuid($ujian_uuid , $siswa_uuid)
	{
		$this->db->select(
			'uj.*, uo.*, u.*'
		);
		$this->db->from('ujian u');
		$this->db->join('ujian_soal uo', 'uo.ujian_uuid = u.uuid', 'left');
		$this->db->join('ujian_jawaban uj', 'uj.soal_uuid = uo.uuid', 'left');
		$this->db->where('u.uuid', $ujian_uuid);
		$this->db->where('uj.created_by', $siswa_uuid);
		$this->db->where('uj.deleted_at', NULL, FALSE);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('uj.created_by', 'DESC');
		
		return $this->db->get()->result();
	}

	public function get_pengumpulan_siswa($ujian_uuid , $siswa_uuid)
	{
			$this->db->select(
				"modified_at"
			);
			$this->db->from('ujian_jawaban');
			$this->db->where('ujian_uuid', $ujian_uuid);
			$this->db->where('created_by', $siswa_uuid);
			$this->db->where('deleted_at', NULL, FALSE);
			$this->db->order_by('id', 'DESC');
			$this->db->group_by('ujian_uuid');
			
			// return $this->db->get()->result();
			$data = $this->db->get()->row(); 
			return $data;
			// echo"<pre>";
			// print_r($data);
			// echo"</pre>";
			// exit;

	}

}
?>