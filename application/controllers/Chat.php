<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('chat_model');
	}

	public function index()
	{
		redirect('home');
	}

	public function contacts()
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array(
				'contacts' => $this->chat_model->get_contacts($this->session->userdata('uuid'))
			)));
	}

	public function unread_count()
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array(
				'count' => $this->chat_model->get_unread_count($this->session->userdata('uuid'))
			)));
	}

	public function messages($contact_uuid = '')
	{
		$current_uuid = $this->session->userdata('uuid');
		$contact_uuid = trim((string) $contact_uuid);
		$contact = $this->chat_model->get_contact($contact_uuid, $current_uuid);

		if (!$contact) {
			show_404();
		}

		$after_value = $this->input->get('after', TRUE);
		$after_id = $after_value === null || $after_value === ''
			? null
			: max(0, (int) $after_value);
		$this->chat_model->mark_conversation_read($current_uuid, $contact_uuid);
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array(
				'messages' => $this->chat_model->get_messages($current_uuid, $contact_uuid, $after_id)
			)));
	}

	public function send()
	{
		if ($this->input->method(TRUE) !== 'POST') {
			show_error('Metode request tidak diizinkan.', 405);
		}

		$current_uuid = $this->session->userdata('uuid');
		$contact_uuid = trim((string) $this->input->post('recipient_uuid', TRUE));
		$message = trim((string) $this->input->post('message', FALSE));
		$contact = $this->chat_model->get_contact($contact_uuid, $current_uuid);
		$character_count = preg_match_all('/./us', $message, $matches);

		if (!$contact) {
			show_404();
		}

		if ($message === '' || $character_count === FALSE || $character_count > 2000) {
			if ($this->input->is_ajax_request()) {
				$this->output
					->set_status_header(422)
					->set_content_type('application/json')
					->set_output(json_encode(array(
						'error' => 'Pesan harus diisi dan maksimal 2.000 karakter.'
					)));
				return;
			}

			$this->session->set_flashdata('error_msg', 'Pesan harus diisi dan maksimal 2.000 karakter.');
			redirect('chat?to=' . rawurlencode($contact_uuid));
		}

		$message_id = $this->chat_model->send_message($current_uuid, $contact_uuid, $message);
		if (!$message_id) {
			if ($this->input->is_ajax_request()) {
				$this->output
					->set_status_header(500)
					->set_content_type('application/json')
					->set_output(json_encode(array(
						'error' => 'Pesan gagal dikirim. Silakan coba kembali.'
					)));
				return;
			}

			show_error('Pesan gagal dikirim. Silakan coba kembali.', 500);
		}

		if ($this->input->is_ajax_request()) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array(
					'message' => $this->chat_model->get_message($message_id)
				)));
			return;
		}

		redirect('chat?to=' . rawurlencode($contact_uuid));
	}
}
