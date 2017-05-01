<?php 
	class Report extends CI_Model{
		private $data;
		private $subscriber;
		function __construct(){
			// Call the Model constructor
			parent::__construct();
			$this->load->database();
			$this->load->model('subscriber');
			$this->data = array();
			$this->subscribers = array();
		}
		function update_item($table, $data, $where){
			$this->db->update($table, $data, $where);
			return $this->get_item($table, $where);
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
		
		function getSubscribers(){
			//Go through list of subscribers
			$old_subscriber="";
			$result = mysql_query("select subscribers.id as id, username, email, name, type from subscribers inner join themes_subscribers on subscribers.id = subscriber_id inner join themes on themes.id=theme_id order by email, name");
			while ($subscriber = mysql_fetch_object($result)){
				$this->subscribers[$subscriber->email][]=$subscriber->name;
			}
			return $this->subscribers;
		}
		
		function getMarketwatchData(){
			//The total # of users who selected one or more theme, and/or MarketWatch:
			$data=array();
			$result=mysql_query("SELECT username FROM subscribers WHERE id IN (SELECT subscriber_id FROM themes_subscribers) AND type='member'");
			$data['members'] = mysql_num_rows($result);
			$result=mysql_query("SELECT username FROM subscribers WHERE id IN (SELECT subscriber_id FROM themes_subscribers) AND type='employee'");
			$data['employees']=mysql_num_rows($result);
			$result=mysql_query("select username from subscribers where id in (select subscriber_id from themes_subscribers where theme_id != 15) AND type='member'");
			$data['members_no_market_watch']=mysql_num_rows($result);
			$result=mysql_query("select username from subscribers where id in (select subscriber_id from themes_subscribers where theme_id != 15) AND type='employee'");
			$data['employees_no_market_watch']=mysql_num_rows($result);
			$result=mysql_query("select username from subscribers where id in (select subscriber_id from themes_subscribers where theme_id = 15) AND type='member'");
			$data['members_market_watch']=mysql_num_rows($result);
			$result=mysql_query("select username from subscribers where id in (select subscriber_id from themes_subscribers where theme_id = 15) AND type='employee'");
			$data['employees_market_watch']=mysql_num_rows($result);
			return $data;
		}
		
		function getThemesReport(){
			$data=array();
			$records = array('theme'=>array(), 'num_emails'=>array(),'subjects'=>array());
			$result=mysql_query("select t.id, t.name Theme, count(l.email_subject) 'Emails'  from logs_themes lt inner join logs l on l.id = lt.log_id inner join themes t on lt.theme_id = t.id group by t.id;");
			$i=0;
			while ($rec = mysql_fetch_object($result)){
				$records['theme'][$i] = $rec->Theme;
				$records['num_emails'][$i] = $rec->Emails;
				$results2 = mysql_query("select email_subject, date_sent from logs inner join logs_themes on logs.id = logs_themes.log_id where theme_id=".$rec->id);
				while ($rec2 = mysql_fetch_object($results2)){
					$records['subjects'][$i][]=$rec2->email_subject." (".$rec2->date_sent.")";
				}
				$i++;				
			}
			return $records;
		}
	}