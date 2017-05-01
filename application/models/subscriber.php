<?php 
	class Subscriber extends CI_Model{
		function __construct(){
			// Call the Model constructor
			parent::__construct();
		}
		function get_item($table, $where){
			$query = $this->db->get_where($table, $where, 1);
			if ($query->num_rows() > 0){
				foreach ($query->result() as $row)
					return $row->id;
			} 
			else 
				return false;
		}
		function insert_item_into_db($table, $data){
			$this->db->insert($table, $data);
			return $this->db->insert_id();
		}
		function update_item($table, $data, $where){
			$this->db->update($table, $data, $where);
			return $this->subscriber->get_item($table, $where);
		}
		function clear($table, $where){
			$id=$this->get_item($table, $where);
			$this->db->delete($table, $where); 
			return $id;
		}
	}