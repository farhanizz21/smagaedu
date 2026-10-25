<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_model extends CI_Model
{
	private $table = 'chat_messages';

	public function get_contacts($current_uuid)
	{
		$query = $this->db->query(
			'SELECT users.uuid, users.nama, roles.nama AS role_nama,
				latest.message AS latest_message,
				latest.created_at AS latest_message_at,
				COALESCE(unread.unread_count, 0) AS unread_count
			FROM users
			JOIN roles ON roles.id = users.role_id
			LEFT JOIN chat_messages AS latest ON latest.id = (
				SELECT chat_item.id
				FROM chat_messages AS chat_item
				WHERE (chat_item.sender_uuid = users.uuid AND chat_item.recipient_uuid = ?)
					OR (chat_item.sender_uuid = ? AND chat_item.recipient_uuid = users.uuid)
				ORDER BY chat_item.created_at DESC, chat_item.id DESC
				LIMIT 1
			)
			LEFT JOIN (
				SELECT incoming.sender_uuid, COUNT(*) AS unread_count
				FROM chat_messages AS incoming
				LEFT JOIN chat_reads AS chat_read
					ON chat_read.user_uuid = incoming.recipient_uuid
					AND chat_read.contact_uuid = incoming.sender_uuid
				WHERE incoming.recipient_uuid = ?
					AND incoming.id > COALESCE(chat_read.last_read_message_id, 0)
				GROUP BY incoming.sender_uuid
			) AS unread ON unread.sender_uuid = users.uuid
			WHERE users.uuid != ?
				AND users.status = ?
				AND users.deleted_at IS NULL
			ORDER BY latest.created_at IS NULL ASC, latest.created_at DESC, latest.id DESC, users.nama ASC',
			array($current_uuid, $current_uuid, $current_uuid, $current_uuid, 'aktif')
		);

		return $query->result();
	}

	public function get_contact($contact_uuid, $current_uuid)
	{
		return $this->db
			->select('users.uuid, users.nama, roles.nama AS role_nama')
			->from('users')
			->join('roles', 'roles.id = users.role_id')
			->where('users.uuid', $contact_uuid)
			->where('users.uuid !=', $current_uuid)
			->where('users.status', 'aktif')
			->where('users.deleted_at IS NULL', null, false)
			->limit(1)
			->get()
			->row();
	}

	public function get_messages($first_uuid, $second_uuid, $after_id = null)
	{
		$this->db->select('id, sender_uuid, message, created_at');
		$this->db->from($this->table);
		$this->db->group_start()
			->group_start()
				->where('sender_uuid', $first_uuid)
				->where('recipient_uuid', $second_uuid)
			->group_end()
			->or_group_start()
				->where('sender_uuid', $second_uuid)
				->where('recipient_uuid', $first_uuid)
			->group_end()
		->group_end();

		if ($after_id !== null) {
			$this->db->where('id >', $after_id);
			$this->db->order_by('created_at', 'ASC');
			$this->db->order_by('id', 'ASC');
			$this->db->limit(100);
			return $this->db->get()->result();
		}

		$this->db->order_by('created_at', 'DESC');
		$this->db->order_by('id', 'DESC');
		$this->db->limit(100);
		return array_reverse($this->db->get()->result());
	}

	public function get_unread_count($user_uuid)
	{
		$query = $this->db->query(
			'SELECT COUNT(*) AS unread_count
			FROM chat_messages AS messages
			LEFT JOIN chat_reads AS chat_read
				ON chat_read.user_uuid = messages.recipient_uuid
				AND chat_read.contact_uuid = messages.sender_uuid
			WHERE messages.recipient_uuid = ?
				AND messages.id > COALESCE(chat_read.last_read_message_id, 0)',
			array($user_uuid)
		);

		return (int) $query->row()->unread_count;
	}

	public function mark_conversation_read($user_uuid, $contact_uuid)
	{
		$query = $this->db->query(
			'SELECT COALESCE(MAX(id), 0) AS last_read_message_id
			FROM chat_messages
			WHERE recipient_uuid = ? AND sender_uuid = ?',
			array($user_uuid, $contact_uuid)
		);
		$last_read_message_id = (int) $query->row()->last_read_message_id;

		if ($last_read_message_id === 0) {
			return;
		}

		$this->db->query(
			'INSERT INTO chat_reads (user_uuid, contact_uuid, last_read_message_id, updated_at)
			VALUES (?, ?, ?, CURRENT_TIMESTAMP)
			ON DUPLICATE KEY UPDATE
				last_read_message_id = GREATEST(last_read_message_id, VALUES(last_read_message_id)),
				updated_at = CURRENT_TIMESTAMP',
			array($user_uuid, $contact_uuid, $last_read_message_id)
		);
	}

	public function send_message($sender_uuid, $recipient_uuid, $message)
	{
		if (!$this->db->insert($this->table, array(
			'sender_uuid' => $sender_uuid,
			'recipient_uuid' => $recipient_uuid,
			'message' => $message
		))) {
			return false;
		}

		return (int) $this->db->insert_id();
	}

	public function get_message($message_id)
	{
		return $this->db
			->select('id, sender_uuid, recipient_uuid, message, created_at')
			->where('id', $message_id)
			->get($this->table)
			->row();
	}
}
