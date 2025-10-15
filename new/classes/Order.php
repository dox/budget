<?php

class Order {
	public $uid;
	public $username;
	public $date;
	public $cost_centre;
	public $po;
	public $order_num;
	public $name;
	public $value;
	public $supplier;
	public $description;
	public $paid;

	protected $db;

	public function __construct($uid = null) {
		$this->db = Database::getInstance();

		if ($uid !== null) {
			$this->getOne($uid);
		}
	}

	public function getOne($uid) {
		$query = "SELECT * FROM orders WHERE uid = ?";
		$row = $this->db->fetch($query, [$uid]);

		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
		}
	}
	
	public function costCenter () {
		return "XYZ";
	}
	
	public function save() {
		if (isset($this->uid)) {
			// update
			$sql = "UPDATE orders 
					SET user_id = ?, budget_code = ?, amount = ?, description = ?, invoice_path = ? 
					WHERE id = ?";
			return $this->db->query($sql, [
				$this->user_id, $this->budget_code, $this->amount,
				$this->description, $this->invoice_path, $this->id
			]);
		} else {
			// insert
			$sql = "INSERT INTO orders (user_id, budget_code, amount, description, invoice_path, created_at) 
					VALUES (?, ?, ?, ?, ?, NOW())";
			$this->db->query($sql, [
				$this->user_id, $this->budget_code, $this->amount,
				$this->description, $this->invoice_path
			]);

			$this->id = $this->db->lastInsertId();
			return $this->id;
		}
	}

	public function delete() {
		if (!isset($this->uid)) return false;
		return $this->db->query("DELETE FROM orders WHERE uid = ?", [$this->uid]);
	}

	public function toArray() {
		return [
			'uid'          => $this->uid,
			'user_id'      => $this->user_id,
			'budget_code'  => $this->budget_code,
			'amount'       => $this->amount,
			'description'  => $this->description,
			'invoice_path' => $this->invoice_path,
			'created_at'   => $this->created_at,
		];
	}
}
