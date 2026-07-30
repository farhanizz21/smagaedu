<?php 
date_default_timezone_set('Asia/Jakarta');
use Ramsey\Uuid\Uuid;

class bab_model extends CI_Model {

    public function insert($materi_uuid, $dokumentasi)
    {
        $uuid = Uuid::uuid4()->toString();
        $data = array(
            'uuid'        => $uuid,
            'materi_uuid' => $materi_uuid,
            'judul'       => $this->input->post('judul'),
            'deskripsi'   => $this->input->post('deskripsi'),
            'dokumentasi' => $dokumentasi,
            'dokumentasi_link' => $this->input->post('dokumentasi_link'),
            'created_by'  => $this->session->userdata('uuid')
        );
        $this->db->insert('bab', $data);
        if ($this->db->affected_rows() > 0) {
            return $uuid;
        }
        return false;
    }

    public function get_by_materi_uuid($materi_uuid)
    {
        $this->db->where('materi_uuid', $materi_uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        $this->db->order_by('modified_at', 'ASC');
        return $this->db->get('bab')->result();
    }

    public function get_by_uuid($uuid)
    {
        $this->db->where('uuid', $uuid);
        $this->db->where('deleted_at', NULL, FALSE);
        return $this->db->get('bab')->row();
    }

    public function update($uuid, $dokumentasi)
    {
        $data = array(
            'judul'       => $this->input->post('judul'),
            'deskripsi'   => $this->input->post('deskripsi'),
            'dokumentasi' => $dokumentasi,
            'dokumentasi_link' => $this->input->post('dokumentasi_link'),
            'modified_at' => date("Y-m-d H:i:s")
        );
        $this->db->update('bab', $data, array('uuid' => $uuid));
        return ($this->db->affected_rows() > 0);
    }

    public function delete_by_uuid($uuid)
    {
        $data = array('deleted_at' => date("Y-m-d H:i:s"));
        $this->db->update('bab', $data, array('uuid' => $uuid));
        return ($this->db->affected_rows() > 0);
    }
}
?>