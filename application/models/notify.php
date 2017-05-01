<?php 
	class Notify extends CI_Model{
		function __construct(){
			// Call the Model constructor
			parent::__construct();
		}
		function get_notifyees($content_categories, $themes){
			//INNER JOIN themes_subscribers on subscribers.id=themes_subscribers.subscriber_id WHERE content_category_id = 1 AND (theme_id=3 or theme_id=10) GROUP BY username;
			$where_cc="true";
			$where_themes="";
			$notifyees=array();
		//	for ($i=0; $i<count($content_categories); $i++){
		//		$where_cc.="content_category_id=".$content_categories[$i];
		//		if ($i<count($content_categories)-1)
		//			$where_cc.=" OR ";
		//	}
			for ($i=0; $i<count($themes); $i++){
				$where_themes.="theme_id=".$themes[$i];
				if ($i<count($themes)-1)
					$where_themes.=" OR ";
			}
	
			$this->db->select("*");
			$this->db->from('subscribers');
			//$this->db->join('content_categories_subscribers','subscribers.id = content_categories_subscribers.subscriber_id');
			$this->db->join('themes_subscribers','subscribers.id=themes_subscribers.subscriber_id');
			$this->db->where('('.$where_cc.') AND ('.$where_themes.')');
			$this->db->group_by('email');
			$query=$this->db->get();
		
			if ($query->num_rows() > 0){
				foreach ($query->result() as $row)
					$notifyees[]=array('username'=>$row->username, 'email'=>$row->email);
				return $notifyees;
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
			$this->db->delete($table, $where); 
		}
	}