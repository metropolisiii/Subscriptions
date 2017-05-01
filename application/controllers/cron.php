<?php
	class Cron extends CI_Controller{
		function __construct() {
			parent::__construct();
			date_default_timezone_set('America/Denver');
			$this->load->database();
			$this->load->helper('file');
		}
		function index() {
			$this->report();
		}
		public function report(){
			$userinfo = trim(read_file('/etc/confluence'));
			$host = "https://community.Mycompany.com/wiki/plugins/servlet/confluence/default/Global/";
			$records = $this->getSubscriberData();
			$file_content = $this->buildContent($records);
			if (!write_file($this->config->item('upload_path')."/updates.txt", $file_content)){
				 echo 'Unable to write the file';
			}
			else{
				$this->executeWebdav($userinfo, $host);
			}
		
		}
		private function getSubscriberData(){			
			$records=array();
			/* Get the emails that were sent out over the week */
			$this->db->select('l.id, sender_name, email_subject, date_sent, t.name');
			$this->db->from('logs l');
			$this->db->join('logs_themes lt', 'l.id = lt.log_id');
			$this->db->join('themes t', 'lt.theme_id = t.id');
			$this->db->where('l.date_sent >=',date('Y-m-d'));
			$this->db->where('l.date_sent <', date('Y-m-d', strtotime("+1 week")));
			$this->db->order_by('date_sent');
			$this->db->order_by('name');
			$query = $this->db->get();
			
			/* Format results into an array */
			foreach($query->result() as $row){
				if (!array_key_exists($row->id, $records)){
					$records[$row->id] = array();
				}
				//print_r($records);
				if (!array_key_exists('sender', $records[$row->id])){
					$records[$row->id]['sender'] = $row->sender_name;
					$records[$row->id]['email_subject'] = $row->email_subject;
					$records[$row->id]['date_sent'] = $row->date_sent;
					$records[$row->id]['themes'] = array();
				}
				$records[$row->id]['themes'][] = $row->name;				
			}
			return $records;
		}
		private function buildContent($records){
			$file_content = "
				<ac:structured-macro ac:name=\"html\">
				<ac:plain-text-body><![CDATA[<table width='100%'>
					<tr>
						<th>Sender</th>
						<th>Subject</th>
						<th>Date Sent</th>
						<th>Themes</th>
					</tr>
			";
			/* Go through each record and build the html */
			foreach ($records as $rec){
				$file_content.="<tr>
				<td>".$rec['sender']."</td>
				<td>".$rec['email_subject']."</td>
				<td>".$rec['date_sent']."</td>
				<td>";
				foreach ($rec['themes'] as $theme)
					$file_content.=$theme."<br/>";
				$file_content.="</td>
				</tr>";
			}
			$file_content.="</table>]]></ac:plain-text-body>
							</ac:structured-macro>";
			return $file_content;			
		}
		private function executeWebdav($userinfo, $host){
			$weekof=date('Y-m-d');		
			exec("curl --user '{$userinfo}' -k '{$host}SUB/".$weekof."%20subscriptions%20logs/".$weekof."%20subscriptions%20logs.txt'", $pageinfo);
			$pagenotfound=false;
			foreach($pageinfo as $value){
				if (strpos($value, 'Page Not Found') >0){
					$pagenotfound=true;
					break;
				}
			}
			if ($pagenotfound){
				exec("curl --user '{$userinfo}' -k -X MKCOL  SUB/".$weekof."%20subscriptions%20logs");
			}
			exec("curl --user '{$userinfo}' -k -X MKCOL  {$host}SUB/".$weekof."%20subscriptions%20logs");
			exec("curl --user '{$userinfo}' -k -T ".$this->config->item('upload_path')."/updates.txt {$host}SUB/".$weekof."%20subscriptions%20logs/".$weekof."%20subscriptions%20logs.txt");
			echo "Script completed!";			
		}
	}
?>