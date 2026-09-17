<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class jawaban_model extends CI_Model {

    // public function rules()
	// {
	// 	return[
	// 		[
	// 			'field' => 'jawaban',
	// 			'label' => 'jawaban',
	// 			'rules' => 'required'
	// 		]
	// 	];
	// }

public function insert()
    {
        $ujian_uuid = $this->input->post('ujian_uuid');
        $jawaban = $this->input->post('jawaban');

        foreach ($jawaban as $soal_uuid => $jawaban_siswa) {
            if (is_array($jawaban_siswa)) {
                $jawaban_siswa = json_encode($jawaban_siswa);
            }
            $data = [
                'ujian_uuid' => $ujian_uuid,
                'soal_uuid' => $soal_uuid,
                'jawaban_siswa' => $jawaban_siswa,
                'created_by' => $this->session->userdata('uuid')
            ];
            $this->db->insert('ujian_jawaban', $data);
        }
        redirect('ujian');
    }

	public function get_by_soal_uuid($soal_uuid, $siswa_uuid)
	{
		$this->db->select("jawaban_siswa, nilai");
		$this->db->where('soal_uuid', $soal_uuid);
		$this->db->where('created_by', $siswa_uuid);
		$data = $this->db->get('ujian_jawaban');

		return $data->result();
	}

	public function insert_nilai($ujian_uuid, $siswa_uuid)
	{
		$nilai_data = $this->input->post('nilai');
		$total_nilai = 0;
		$jumlah_soal = count($nilai_data); // Menghitung jumlah soal

		foreach ($nilai_data as $soal_uuid => $nilai) {
			$this->db->where('ujian_uuid', $ujian_uuid);
			$this->db->where('created_by', $siswa_uuid);
			$this->db->where('soal_uuid', $soal_uuid);
			$this->db->update('ujian_jawaban', ['nilai' => $nilai]);
			$total_nilai += $nilai;
		}
		
		$rata_rata = ($jumlah_soal > 0) ? ($total_nilai / $jumlah_soal) : 0;
		$ujian_nilai = number_format($rata_rata, 2);

		$this->db->where('ujian_uuid', $ujian_uuid);
		$this->db->where('siswa_uuid', $siswa_uuid);
		$this->db->update('ujian_siswa', ['ujian_nilai' => $ujian_nilai]);

		return ($this->db->affected_rows() > 0) ? true : false;
	}

	public function submit_proyek_answer($proyek_uuid, $jawaban_text, $jawaban_file)
	{
		$uuid = Uuid::uuid4()->toString();
		$kelompok_nama = $this->input->post('kelompok_nama');
		$kelompok_uuid = $this->input->post('kelompok_uuid');
		$keterangan_file = $this->input->post('keterangan_file');
		
		$data = [
			'uuid' => $uuid,
			'proyek_uuid' => $proyek_uuid,
			'kelompok_nama' => $kelompok_nama,
			'kelompok_uuid' => $kelompok_uuid,
			'jawaban_text' => $jawaban_text,
			'jawaban_file' => $jawaban_file,
			'keterangan_file' => $keterangan_file,
			'created_by' => $this->session->userdata('uuid')
		];
		
		$this->db->insert('proyek_jawaban', $data);
		if ($this->db->affected_rows() > 0) {
			return $uuid; 
		} else {
			return false;
		}
	}

	public function get_by_proyek_uuid($proyek_uuid)
	{
		$this->db->select("*");
		$this->db->where('proyek_uuid', $proyek_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('modified_at', 'DESC');
		$data = $this->db->get('proyek_jawaban');

		return $data->result();
	}

	public function get_by_kelompok_uuid($kelompok_uuid)
	{
		$this->db->select("*");
		$this->db->where('kelompok_uuid', $kelompok_uuid);
		$this->db->where('deleted_at', NULL, FALSE);
		$this->db->order_by('modified_at', 'DESC');
		$data = $this->db->get('proyek_jawaban');

		return($this->db->affected_rows() > 0) ? true :false;
	}

	public function delete_jawaban_proyek_by_uuid($uuid)
	{
		$data = array(
			'deleted_at' => date("Y-m-d H:i:s")
		);
		$this->db->update('proyek_jawaban', $data, array('uuid' => $uuid));
		return($this->db->affected_rows() > 0) ? true :false;
	}
	
	public function insert_nilai_jawaban_proyek_by_uuid($jawaban_uuid)
	{
		$nilai = $this->input->post('nilai_kelompok');
		
		$data = array(
			'nilai' => $nilai
		);
		
		$this->db->update('proyek_jawaban', $data, array('uuid' => $jawaban_uuid));
		return($this->db->affected_rows() > 0) ? true :false;

	}

}
?>