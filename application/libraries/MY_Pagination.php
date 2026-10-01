<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Pagination
 *
 * Ekstensi library native CI_Pagination untuk memperbaiki peringatan
 * deprecation PHP 8.1 berikut saat mode query string dipakai:
 *
 *   ctype_digit(): Argument of type null will be interpreted as string in the future
 *   (system/libraries/Pagination.php, baris 526)
 *
 * Penyebab: pada mode query string, CI_Pagination::create_links() mengambil
 * nomor halaman lewat input->get($query_string_segment). Bila parameter
 * tersebut belum ada di URL (mis. saat pertama kali membuka halaman),
 * input->get() mengembalikan NULL sehingga ctype_digit(NULL) memicu warning.
 *
 * Perbaikan: normalisasi nilai parameter halaman menjadi string digit positif
 * sebelum pemrosesan dilakukan oleh parent::create_links(). Nilai yang
 * diinjeksi pada $_GET otomatis di-unset kembali oleh parent saat menyusun
 * link (opsi reuse_query_string), sehingga tidak mengotori URL yang dihasilkan.
 */
class MY_Pagination extends CI_Pagination {

	public function create_links()
	{
		// Hanya relevan untuk mode query string.
		if ($this->page_query_string === TRUE)
		{
			$current = $this->CI->input->get($this->query_string_segment);

			// Bila parameter halaman tidak ada atau bukan angka positif,
			// set ke "1" agar tidak ada nilai NULL yang diproses ctype_digit().
			if ( ! ctype_digit((string) $current) OR (int) $current < 1)
			{
				$_GET[$this->query_string_segment] = '1';
			}
		}

		return parent::create_links();
	}
}
