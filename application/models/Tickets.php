<?php

/**
 * Tickets
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ShineISP
 * 
 * @author     Shine Software <info@shineisp.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Tickets extends BaseTickets {
	
	public static function grid($rowNum = 10) {
		
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 't.ticket_id', 'alias' => 'ticket_id', 'type' => 'selectall', 'attributes' => array('class' => 'span1') );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'ID' ), 'field' => 't.ticket_id', 'alias' => 'ticket_id', 'sortable' => true, 'direction'=> 'desc', 'searchable' => true, 'type' => 'string', 'attributes' => array('class' => 'span1') );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Creation date' ), 'field' => 't.date_open', 'alias' => 'creation_date', 'sortable' => true, 'direction'=> 'desc', 'searchable' => true, 'type' => 'date', 'attributes' => array('class' => 'span1 hidden-phone  hidden-tablet') );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Update Date' ), 'field' => 't.date_updated', 'alias' => 'updated_at', 'sortable' => true, 'direction'=> 'desc', 'searchable' => true, 'type' => 'date', 'attributes' => array('class' => 'span1 hidden-phone') );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Category' ), 'field' => 'tc.category', 'alias' => 'category', 'sortable' => true, 'searchable' => true, 'type' => 'string', 'attributes' => array('class' => 'hidden-phone hidden-tablet') );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Subject' ), 'field' => 't.subject', 'alias' => 'subject', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Company' ), 'field' => "c.company", 'alias' => 'company', 'sortable' => true, 'searchable' => true, 'type' => 'string', 'attributes' => array('class' => 'hidden-phone hidden-tablet'));
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Fullname' ), 'field' => "CONCAT(c.firstname, ' ', c.lastname)", 'alias' => 'customer', 'sortable' => true, 'searchable' => true, 'type' => 'string', 'attributes' => array('class' => 'hidden-phone'));
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Status' ), 'field' => 's.status', 'alias' => 'status', 'type' => 'index', 'sortable' => true, 'searchable' => true, 'attributes' => array('class' => 'span1'));
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Replies' ), 'field' => '', 'alias' => 'replies', 'type' => 'index', 'searchable' => false, 'attributes' => array('class' => 'span1 hidden-phone hidden-tablet'));
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Attachments' ), 'field' => '', 'alias' => 'files', 'type' => 'index', 'searchable' => false, 'attributes' => array('class' => 'span1 hidden-phone hidden-tablet'));
		
		$config ['datagrid'] ['fields'] = "t.ticket_id,
											t.subject as subject, 
											t.category_id as category_id,
											tc.category as category, 
											s.status as status, 
											DATE_FORMAT(t.date_open, '%d/%m/%Y %H:%i') as creation_date, 
											DATE_FORMAT(t.date_updated, '%d/%m/%Y %H:%i') as updated_at, 
											CONCAT(c.firstname, ' ', c.lastname) as customer,
											c.company as company";
		
		$dq = Doctrine_Query::create ()
							->select ( $config ['datagrid'] ['fields'] )
							->from ( 'Tickets t' )
							->leftJoin ( 't.TicketsCategories tc' )
							->leftJoin ( 't.Customers c' )
							->leftJoin ( 't.Statuses s' )
                            ->addWhere( "c.isp_id = ?", Isp::getCurrentId() )
							->orderBy('ticket_id desc');
		
		$dq->addSelect('( SELECT COUNT( * ) FROM TicketsNotes tn WHERE tn.ticket_id = t.ticket_id) as replies' );
		$dq->addSelect('( SELECT COUNT( * ) FROM Files f WHERE f.id = t.ticket_id) as files' );
		
		$config ['datagrid'] ['dqrecordset'] = $dq;
		$config ['datagrid'] ['rownum'] = $rowNum;
		$config ['datagrid'] ['basepath'] = "/admin/tickets/";
		$config ['datagrid'] ['rowlist'] = array ('10', '50', '100', '1000' );
	
		$config ['datagrid'] ['massactions']['common'] = array ('bulkexport' => 'Export', 'massdelete' => 'Delete' );
		
		$statuses = Statuses::getList('tickets');
		if(!empty($statuses))
			$customacts = array();
			foreach ($statuses as $key => $value) {
				$customacts['bulk_set_status&status=' . $key ] = "Set as $value";
			}
			$config ['datagrid'] ['massactions']['status'] = $customacts;
					
		
		return $config;
	}
	

	/**
	 * findAll
	 * Get records from the DB
	 * @param $currentPage
	 * @param $rowNum
	 * @param $sort
	 * @param $where
	 * @return array
	 */
	public static function findAll($fields = "*", $currentPage = 1, $rowNum = 2, array $sort = array(), array $where = array()) {
		
		$module = Zend_Controller_Front::getInstance ()->getRequest ()->getModuleName ();
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		
		// Defining the url sort
		$uri = isset ( $sort [1] ) ? "/sort/$sort[1]" : "";
		$dq = Doctrine_Query::create ()
		          ->select ( $fields )
		          ->from ( 'Tickets t' )
		          ->leftJoin ( 't.TicketsCategories tc' )
		          ->leftJoin ( 't.Customers c' )
		          ->leftJoin ( 't.Statuses s' )
                  ->addWhere( "c.isp_id = ?", Isp::getCurrentId() );
		
		$pagerLayout = new Doctrine_Pager_Layout ( new Doctrine_Pager ( $dq, $currentPage, $rowNum ), new Doctrine_Pager_Range_Sliding ( array ('chunk' => 10 ) ), "/$module/$controller/list/page/{%page_number}" . $uri );
		
		// Get the pager object
		$pager = $pagerLayout->getPager ();
		
		// Set the Order criteria
		if (isset ( $sort [0] )) {
			$pager->getQuery ()->orderBy ( $sort [0] );
		}
		
		if (isset ( $where ) && is_array ( $where )) {
			foreach ( $where as $filters ) {
				
				if (isset ( $filters [0] ) && is_array ( $filters [0] )) {
					foreach ( $filters as $filter ) {
						$method = $filter ['method'];
						$value = $filter ['value'];
						$criteria = $filter ['criteria'];
						$pager->getQuery ()->$method ( $criteria, $value );
					}
				} else {
					$method = $filters ['method'];
					$value = $filters ['value'];
					$criteria = $filters ['criteria'];
					$pager->getQuery ()->$method ( $criteria, $value );
				}
			}
		}
		
		$pagerLayout->setTemplate ( '<a href="{%url}">{%page}</a> ' );
		$pagerLayout->setSelectedTemplate ( '<a class="active" href="{%url}">{%page}</a> ' );
		
		$records = $pagerLayout->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		$pagination = $pagerLayout->display ( null, true );
		return array ('records' => $records, 'pagination' => $pagination, 'pager' => $pager, 'recordcount' => $dq->count () );
	}	
	
	/**
	 * SortingData
	 * Manage the request of sorting of the tickets 
	 * @return string
	 */
	private function sortingData($sort) {
		$strSort = "";
		if (! empty ( $sort )) {
			$sort = addslashes ( htmlspecialchars ( $sort ) );
			$sorts = explode ( "-", $sort );
			
			foreach ( $sorts as $sort ) {
				$sort = explode ( ",", $sort );
				$strSort .= $sort [0] . " " . $sort [1] . ",";
			}
			
			if (! empty ( $strSort )) {
				$strSort = substr ( $strSort, 0, - 1 );
			}
		}
		
		return $strSort;
	}
	
	/**
	 * setNewStatus
	 * Set the status of all items passed
	 * @param $items
	 * @return void
	 */
	public static function setNewStatus($items) {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if(!empty($request)){
			$status = $request->getParams ( 'params' );
			$params = parse_str ( $status ['params'], $output );
			$status = $output ['status'];
			if (is_array ( $items ) && is_numeric ( $status )) {
				foreach ( $items as $ticketsid ) {
					if (is_numeric ( $ticketsid )) {
						self::setStatus ( $ticketsid, $status );
					}
				}
				return true;
			}
		}
		return false;
	}
	
	/**
	 * massdelete
	 * delete the tickets selected 
	 * @param array
	 * @return Boolean
	 */
	public static function massdelete($items) {
		$retval = Doctrine_Query::create ()->delete ()->from ( 'Domains d' )->whereIn ( 'd.domain_id', $items )->execute ();
		return $retval;
	}
	
	/**
	 * find
	 * Get a record by ID
	 * @param $id
	 * @return Doctrine Record
	 */
	public static function find($id) {
		return Doctrine::getTable ( 'Tickets' )->findOneBy ( 'ticket_id', $id );
	}
	
	/**
	 * getWaitingReply
	 * Get all the tickects that need a answer by the customer
	 * status_id = 22 -> Waiting Reply
	 * @return array
	 */
	public static function getWaitingReply() {
		return Doctrine_Query::create ()->from ( 'Tickets t' )
		->leftJoin ( 't.TicketsCategories tc' )
		->leftJoin ( 't.Customers c' )
		->leftJoin ( 't.Statuses s' )
		->where ( 't.status_id = ?', Statuses::id("processing", "tickets") )
		->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
	}
	
	/**
	 * setStatus
	 * Set a record with a status
	 * @param $id, $status
	 * @return Void
	 */
	public static function setStatus($id, $status) {
		if(is_numeric($id)){
			$object = Doctrine::getTable ( 'Tickets' )
			             ->find ( $id );
			$object->status_id = $status;
			$object->date_close = date ( 'Y-m-d H:i:s' );
			return $object->save ();
		}
		return false;
	}
	
	/**
	 * setSibling
	 * Set a sibling to a ticket
	 * @param $id, $siblingId
	 * @return Void
	 */
	public static function setSibling($id, $siblingId) {
		if(is_numeric($id)){
			$object = Doctrine::getTable ( 'Tickets' )
			             ->find ( $id );
			$object->sibling_id = $siblingId;
			return $object->save ();
		}
		return false;
	}
	
	/**
	 * setOperator
	 * Set the operator to a ticket
	 * @param $id, $userId
	 * @return Void
	 */
	public static function setOperator($id, $userId) {
		if(is_numeric($id)){
			$object = Doctrine::getTable ( 'Tickets' )
			             ->find ( $id );
			$object->user_id = $userId;
			return $object->save ();
		}
		return false;
	}
	
	/**
	 * setOrder
	 * Set the order reference
	 * @param $id, $orderId
	 * @return Void
	 */
	public static function setOrder($id, $orderId) {
		if(is_numeric($id)){
			$object = Doctrine::getTable ( 'Tickets' )
			             ->find ( $id );
			$object->order_id = $orderId;
			return $object->save ();
		}
		return false;
	}
	
	/**
	 * getByCustomerID
	 * Get all data  
	 * @param $customerID
	 * @return Array
	 */
	public static function getByCustomerID($customerID, $fields) {
		$records = Doctrine_Query::create ()->select ( $fields )
							->from ( 'Tickets t' )
							->leftJoin ( 't.Customers c' )
							->leftJoin ( 't.Statuses s' )
							->where ( "customer_id = ?", $customerID )
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		return $records;
	}
	
	/**
	 * getAllInfo
	 * Get all data starting from the ticketID 
	 * @param $id
	 * @return Doctrine Record / Array
	 */
	public static function getAllInfo($id, $fields = "*", $retarray = false) {
		$dq = Doctrine_Query::create ()->select ( $fields )
							->from ( 'Tickets t' )
							->leftJoin ( 't.Customers c' )
							->leftJoin ( 't.Domains d' )
							->leftJoin ( 'd.DomainsTlds dt' )
							->leftJoin ( 'dt.WhoisServers ws' )
							->leftJoin ( 't.Statuses s' )
							->leftJoin ( 't.Tickets t2' )
							->where ( "ticket_id = ?", $id )
							->limit ( 1 );
		
		$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
		$items = $dq->execute ( array (), $retarray );
		
		return $items;
	}
	
	/**
	 * Update the ticket votes
	 *
	 * @param integer $noteid
	 */
	public static function updateTickectVote($noteid){
		$votes = array();
	
		if(!empty($noteid) && is_numeric($noteid)){
				
			// Get the parent tickect
			$Note = Doctrine::getTable ( 'TicketsNotes' )->find ( $noteid );
				
			if(is_numeric($Note->ticket_id)){
	
				// Get all the notes
				$notes = Tickets::Notes($Note->ticket_id, 'vote, admin', true );
				foreach ($notes as $note){
						
					// Get all the admin answers votes
					if($note['admin']){
						$votes[] = $note['vote'];
					}
				}
	
				// Count the occurrences of the votes
				$occurences = count($votes);
				$totalvotes = array_sum($votes);
	
				$average = $totalvotes / $occurences;
				if(is_numeric($average)){
					$Ticket = Doctrine::getTable ( 'Tickets' )->find ( $Note->ticket_id );
					$Ticket->vote = $average;
					$Ticket->save();
				}
				return true;
			}
		}
		return false;
	}	
	
	
	/**
	 * Notes
	 * Get all the Notes starting from the ticketID 
	 * @param $id
	 * @return Doctrine Record / Array
	 */
	public static function Notes($id, $fields = "*", $retarray = false) {
		$dq = Doctrine_Query::create ()->select ( $fields )
				->from ( 'TicketsNotes tn' )
				->leftJoin ( 'tn.Tickets t' )
				->leftJoin ( 't.Customers c' )
				->where( "ticket_id = $id" );
		
		$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
		$items = $dq->execute ( array (), $retarray );
		return $items;
	}
	
	/**
	 * List of the last 10 tickets
	 * @return array
	 */
	public static function Last($customerid = "", $limit=10) {
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		$dq = Doctrine_Query::create ()
								->select ( "t.ticket_id, 
											t.subject as subject, 
											tc.category as category, 
											DATE_FORMAT(t.date_updated, '%d/%m/%Y %H:%i') as updated,
											CONCAT(c.company, ' ', c.lastname, ' ', c.firstname ) as fullname, 
											s.status as status" )
								->from ( 'Tickets t' )
								->orderBy('t.date_updated')
								->leftJoin ( 't.Customers c' )
								->leftJoin ( 't.Statuses s' )
								->leftJoin ( 't.TicketsCategories tc' )
                                ->addWhere( "c.isp_id = ?", Isp::getCurrentId() );
		
		if (is_numeric ( $customerid )) {
			$dq->where ( 't.customer_id = ?', $customerid );
		}
        
		// Open, Processing and Waiting Reply tickets
		$statuses = array(Statuses::id("expectingreply", "tickets"), Statuses::id("processing", "tickets"));
		$dq->whereIn('t.status_id', $statuses);
		
		$dq->orderBy ( 't.date_open desc' )->limit ( $limit );
		$records['data'] = $dq->execute ( null, Doctrine::HYDRATE_ARRAY );
		
		for ($i=0;$i<count($records['data']); $i++){
			$records['data'][$i]['subject'] = Shineisp_Commons_Utilities::truncate($records['data'][$i]['subject']);
		}
		
		// adding the index reference
		$records['index'] = "ticket_id";
		
		// Create the header table columns
		$records['fields'] = array('ticket_id' => array('label' => $translator->translate('ID')),
									'subject' => array('label' => $translator->translate('Subject')),
									'category' => array('label' => $translator->translate('Category'), 'attributes' => array('class' => 'hidden-phone hidden-tablet')),
									'updated' => array('label' => $translator->translate('Updated at'), 'attributes' => array('class' => 'hidden-phone hidden-tablet')),
									'fullname' => array('label' => $translator->translate('Full Name'), 'attributes' => array('class' => 'hidden-phone hidden-tablet')),
									'status' => array('label' => $translator->translate('Status')));
		
		
		return $records;
	}
	
	/**
	 * getList
	 * Get a list ready for the html select object
	 * @return array
	 */
	public static function getList($empty = false) {
		$items = array ();
		$translations = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$records = Doctrine_Query::create ()
		              ->select ( "ticket_id, DATE_FORMAT(date_open, '%d/%m/%Y') as date_open, subject" )
		              ->from ( 'Tickets t' )
                      ->leftJoin ( 't.Customers c' )
		              ->addWhere( "c.isp_id = ?", Isp::getCurrentId() )
		              ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		
		if ($empty) {
			$items [] = $translations->translate ( 'Select ...' );
		}
		
		foreach ( $records as $c ) {
			$items [$c ['ticket_id']] = $c ['ticket_id'] . " - " . $c ['subject'];
		}
		
		return $items;
	}
	
	/**
	 * getListbyCustomerId
	 * Get a list ready for the html select object
	 * @return array
	 */
	public static function getListbyCustomerId($customer_id, $empty = false, $abbreviation=false) {
		$items = array ();
		$translations = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$records = Doctrine_Query::create ()->select ( "ticket_id, DATE_FORMAT(date_open, '%d/%m/%Y') as date_open, subject, s.status as status" )
											->from ( 'Tickets t' )
											->leftJoin ( 't.Statuses s' )
											->leftJoin ( 't.Customers c' )
											->where('t.customer_id = ?', $customer_id)
                                            ->addWhere( "c.isp_id = ?", Isp::getCurrentId() )
											->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
		
		if ($empty) {
			$items [] = $translations->translate ( 'Select ...' );
		}
		
		foreach ( $records as $c ) {
			if($abbreviation){
				$subject = Shineisp_Commons_Utilities::truncate($c ['subject'], 25);
			}else{
				$subject = $c ['subject'];
			}
			$items [$c ['ticket_id']] = $c ['ticket_id'] . " - " . $c ['status'] . " - " . $subject;
		}
		
		return $items;
	}
	
	/**
	 * Save the ticket
	 * 
	 * @param integer $id
	 * @param integer $customer
	 * @param string $subject
	 * @param string $description
	 * @param integer $category
	 * @param integer $status
	 * @param integer $domain
	 * @return Boolean or integer
	 */
	public static function saveIt($id=null, $customer, $subject, $description, $category, $status=null, $domain=null) {
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$isUpdate = false;
		
		if(is_numeric($id)){
			$ticket = self::find($id);
			$isUpdate = true;
		}else{
			$ticket = new Tickets ();
		}
		
		$operatorId = Settings::findbyParam('tickets_operator', 'admin', Isp::getActiveISPID());
		if(!is_numeric($operatorId)){
			$operator = AdminUser::getFirstAdminUser();
		}else{
			$operator = AdminUser::getAllInfo($operatorId);
		}

		if (is_numeric ( $customer )) {
			
			$ticket->subject = ! empty ( $subject ) ? $subject : $translator->translate ( 'Generic Issue' );
			$ticket->description = !empty($description) ? $description : null;
			$ticket->category_id = !empty($category) ? $category : null;
			$ticket->customer_id = $customer;
			$ticket->user_id = $operator['user_id'];
			$ticket->date_open = date ( 'Y-m-d H:i:s' );
			$ticket->date_updated = date ( 'Y-m-d H:i:s' );
			$ticket->domain_id = is_numeric($domain) && $domain > 0 ? $domain : NULL;
			$ticket->status_id = !empty($status) ? $status : Statuses::id("expectingreply", "tickets"); // Expecting a reply as default
			$ticket->save ();
			
			$id = $ticket->getIncremented ();
			
			// Save the upload file
			$attachment = self::UploadDocument($id, $customer);
			
			// Check if the request is an update
			if($isUpdate == false){

				// Create for the first time the fast link
				Fastlinks::CreateFastlink ( 'tickets', 'edit', json_encode ( array ('id' => $id ) ), 'tickets', $id, $customer );
				
				// Send ticket by email
				self::send ( $id, true, $attachment );
			}
			
			return $id;
		}
		
		return false;
	}
	
	/**
	 * Send ticket by email
	 * 
	 * @param integer $id
	 * @param boolean $isTicket
	 * @param string $attachment
	 */
	public static function send($id, $isTicket = true, $attachment = null) {
		$isp = Isp::getActiveISP ();
		$placeholders = array();
		$customer_url = "";
		$admin_url = "";
		
		if($isTicket){
			$ticket = self::getAllInfo ( $id, null, true );
			$customer = $ticket [0] ['Customers'];
			$operator = AdminUser::getAllInfo($ticket [0] ['user_id']);
		}else{
			$ticket = TicketsNotes::getAllInfo ( $id );
			$customer = $ticket [0] ['Tickets']['Customers'];
			$operator = AdminUser::getAllInfo($ticket [0] ['Tickets']['user_id']);
		}
		
		if (! empty ( $ticket [0] )) {
			
			if ($isp) {
				$ispmail = explode ( "@", $isp ['email'] );
				
				$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'ticket_message' );
				
				if ($retval) {
					$s = $retval ['subject'];
					$ticketid =  $ticket [0] ['ticket_id'];
					
					$in_reply_to = md5($ticketid);
					$ispmail = "noreply@" . $ispmail [1];
					
					$rec = Fastlinks::findlinks ( $ticketid, $customer ['customer_id'], 'tickets' );
					
					if (! empty ( $rec[0]['code'] )) {
						$customer_url = "http://" . $_SERVER ['HTTP_HOST'] . "/index/link/id/" . $rec[0]['code'];
						$admin_url = "http://" . $_SERVER ['HTTP_HOST'] . "/admin/login/link/id/" . $rec[0]['code'];
					}
					
					// Check the attachments
					if(!empty($attachment) && file_exists(PUBLIC_PATH . $attachment)){
						$attachment = PUBLIC_PATH . $attachment;
					}
					
					if($isTicket){
						$placeholders['subject'] = $ticket [0] ['subject'];
						$placeholders['description'] = $ticket[0] ['description'];
						$placeholders['date_open'] = Shineisp_Commons_Utilities::formatDateOut ( $ticket[0]['date_open'] );
						$placeholders['status'] = $ticket [0] ['Statuses'] ['status'];
					}else{
						$placeholders['subject'] = $ticket [0] ['Tickets']['subject'];
						$placeholders['description'] = $ticket[0] ['note'];
						$placeholders['date_open'] = Shineisp_Commons_Utilities::formatDateOut ( $ticket[0]['Tickets']['date_open'] );
						$placeholders['status'] = $ticket [0] ['Tickets']['Statuses'] ['status'];
					}
					
					$placeholders['customer'] = $customer ['firstname'] . " " . $customer ['lastname'] . " " . $customer ['company'];
					$placeholders['link'] = $customer_url;
					$placeholders['company'] = $isp ['company'];
					$placeholders['issue_number'] = $ticketid;
					$placeholders['operator'] = $operator['lastname'] . " " . $operator['firstname'];
						
					// Send a message to the customer
					Shineisp_Commons_Utilities::sendEmailTemplate(Contacts::getEmails($customer ['customer_id']), 'ticket_message', $placeholders, $in_reply_to, $attachment, null, $isp, $customer['language_id']);
					
					// Update the link for the administrator email 
					$placeholders['link'] = $admin_url . "/keypass/" . Shineisp_Commons_Hasher::hash_string ( $operator['email'] );
					Shineisp_Commons_Utilities::sendEmailTemplate($isp ['email'], 'ticket_message', $placeholders, $in_reply_to, $attachment, null, $isp);
					
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Has the Ticket some attachment?
	 * @return array
	 */
	public static function hasAttachments($ticketid) {
		
		return Files::findbyExternalId($ticketid, "tickets");
		
	}
	
	
	/**
	 * Summary of all the tickets
	 * @return array
	 */
	public static function summary() {
		$chart = "";
		$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
		
		$dq = Doctrine_Query::create ()
					->select ( "t.ticket_id, count(*) as items, s.status as status" )
					->from ( 'Tickets t' )
					->leftJoin ( 't.Statuses s' )
                    ->leftJoin ( 't.Customers c' )
					->where("s.section = 'tickets'")
                    ->addWhere( "c.isp_id = ?", Isp::getCurrentId() )
					->groupBy('s.status');
        
                    
        $records    = $dq->execute(array (), Doctrine_Core::HYDRATE_ARRAY);
		
		// Strip the customer_id field
		if(!empty($records)){
			foreach($records as $key => $value) {
			  	array_shift($value);
			  	$newarray[] = $value;
			  	$chartLabels[] = $value['status'];
			  	$chartValues[] = $value['items'];
			}
			// Chart link
			$chart = "https://chart.googleapis.com/chart?chs=250x100&chd=t:".implode(",", $chartValues)."&cht=p3&chl=".implode("|", $chartLabels);
		}
		
		$dq = Doctrine_Query::create ()
					->select ( "t.ticket_id, count(*) as items" )
					->from ( 'Tickets t' )
                    ->leftJoin ( 't.Customers c' )
                    ->addWhere( "c.isp_id = ?", Isp::getCurrentId() );
        		
        $record_group2      = $dq->execute(array (), Doctrine_Core::HYDRATE_ARRAY);
		
		$records['data'] = $newarray;
		$records['fields'] = array('items' => array('label' => $translator->translate('Items')), 'status' => array('label' => $translator->translate('Status')));
		$records['chart'] = $chart;
		
		return $records;
	}
	
	
 	/**
     * UploadDocument
     * the extensions allowed are JPG, GIF, PNG
     */
    public static function UploadDocument($id, $customerid){
    	try{
    		
	    	$attachment = new Zend_File_Transfer_Adapter_Http();
	    	
			$files = $attachment->getFileInfo();
			
			// Create the directory
			@mkdir ( PUBLIC_PATH . "/documents/", 0777, true);
			@mkdir ( PUBLIC_PATH . "/documents/customers/", 0777, true );
			@mkdir ( PUBLIC_PATH . "/documents/customers/$customerid/", 0777, true );
			@mkdir ( PUBLIC_PATH . "/documents/customers/$customerid/tickets/", 0777, true );
			@mkdir ( PUBLIC_PATH . "/documents/customers/$customerid/tickets/$id/", 0777, true );
			
			if(is_dir(PUBLIC_PATH . "/documents/customers/$customerid/tickets/$id/")){
				// Set the destination directory
				$attachment->setDestination ( PUBLIC_PATH . "/documents/customers/$customerid/tickets/$id/" );
				
				if ($attachment->receive()) {
					return Files::saveit($files['attachments']['name'], "/documents/customers/$customerid/tickets/$id/", 'tickets', $id);
				}	
			}
    	}catch (Exception $e){
			echo $e->getMessage();
			die;	    		
    	}
    }	

    /**
     * Get a tickets by id lists
     * @param array $ids [1,2,3,4,...,n]
     * @param string $fields
     * @return Array
     */
    public static function get_tickets($ids, $fields=null) {
    	$dq = Doctrine_Query::create ()->from ( 'Tickets t' )
    	->leftJoin ( 't.Customers c' )
    	->leftJoin ( 't.TicketsCategories tc' )
    	->leftJoin ( 't.Domains d' )
    	->leftJoin ( 'd.DomainsTlds dt' )
    	->leftJoin ( 'dt.WhoisServers ws' )
    	->leftJoin ( 't.Statuses s' )
    	->leftJoin ( 't.Tickets t2' )
    	->whereIn( "ticket_id", $ids)
    	->addWhere( "c.isp_id = ?", Isp::getCurrentId() );
    	if(!empty($fields)){
    		$dq->select($fields);
    	}
    
    	return $dq->execute ( array (), Doctrine::HYDRATE_ARRAY );
    }

	######################################### CRON METHODS ############################################
	
    /**
     * This batch has been created in order to remind customers
     * that one or more tickets are still open.
     */
    public static function checkTickets() {
    	$isp = Isp::getActiveISP ();
    	$tickets = Tickets::getWaitingReply ();
    
    	// Get the template from the main email template folder
    	$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'ticket_waitreply' );
    
    	foreach ( $tickets as $ticket ) {
    		$customer = $ticket ['Customers'];
    			
    		// Get the fastlink attached
    		$link_exist = Fastlinks::findlinks ( $ticket ['ticket_id'], $customer ['customer_id'], 'tickets' );
    		if (count ( $link_exist ) > 0) {
    			$fastlink = $link_exist [0] ['code'];
    		} else {
    			$fastlink = Fastlinks::CreateFastlink ( 'tickets', 'edit', json_encode ( array ('id' => $ticket ['ticket_id'] ) ), 'tickets', $ticket ['ticket_id'], $customer ['customer_id'] );
    		}
    			
    		$customer_url = "http://" . $_SERVER ['HTTP_HOST'] . "/index/link/id/$fastlink";
    			
    		if ($retval) {
    			$subject = $retval ['subject'];
    			$Template = nl2br ( $retval ['template'] );
    			$subject = str_replace ( "[subject]", $ticket ['subject'], $subject );
    			$Template = str_replace ( "[subject]", $ticket ['subject'], $Template );
    			$Template = str_replace ( "[lastname]", $customer ['lastname'], $Template );
    			$Template = str_replace ( "[issue_number]", $ticket ['ticket_id'], $Template );
    			$Template = str_replace ( "[date_open]", Shineisp_Commons_Utilities::formatDateOut ( $ticket ['date_open'] ), $Template );
    			$Template = str_replace ( "[link]", $customer_url, $Template );
    			$Template = str_replace ( "[signature]", $isp ['company'] . "<br/>" . $isp ['website'], $Template );
    
    			Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], Contacts::getEmails($customer ['customer_id']), null, $subject, $Template, true );
    		}
    	}
    	return true;
    }

    
	######################################### BULK ACTIONS ############################################
	

    /**
     * export the content in a pdf file
     * @param array $items
     */
    public function bulkexport($items) {
    	$isp = Isp::getActiveISP();
    	$pdf = new Shineisp_Commons_PdfList();
    	$translator = Shineisp_Registry::getInstance ()->Zend_Translate;
    
    	$fields = " t.ticket_id,
					t.subject as subject, 
					tc.category as category, 
					s.status as status, 
					DATE_FORMAT(t.date_open, '%d/%m/%Y %H:%i') as creation_date, 
					DATE_FORMAT(t.date_updated, '%d/%m/%Y %H:%i') as updated_at, 
					CONCAT(c.firstname, ' ', c.lastname) as fullname,
					c.company as company";
    	
    	// Get the records from the customer table
    	$tickets = self::get_tickets($items, $fields);
    	
    	// Create the PDF header
    	$grid['headers']['title'] = $translator->translate('Tickets List');
    	$grid['headers']['subtitle'] = $translator->translate('List of the selected tickets');
    	$grid['footer']['text'] = $isp['company'] . " - " . $isp['website'];
    		
    	if(!empty($tickets[0]))
    
    		// Create the columns of the grid
	    	$grid ['columns'] [] = array ("value" => $translator->translate('Subject'), 'size' => 100);
	    	$grid ['columns'] [] = array ("value" => $translator->translate('Category'), 'size' => 80);
	    	$grid ['columns'] [] = array ("value" => $translator->translate('Status'));
	    	$grid ['columns'] [] = array ("value" => $translator->translate('Creation Date'), 'size' => 100);
	    	$grid ['columns'] [] = array ("value" => $translator->translate('Upload Date'), 'size' => 80);
	    	$grid ['columns'] [] = array ("value" => $translator->translate('Fullname'), 'size' => 80);
	    	$grid ['columns'] [] = array ("value" => $translator->translate('Company'), 'size' => 80);
    
    	// Getting the records values and delete the first column the customer_id field.
    	foreach ($tickets as $ticket){
    		$values = array_values($ticket);
    		array_shift($values);
    		$grid ['records'] [] = $values;
    	}
    
    	// Create the PDF
    	die($pdf->create($grid));
    
    	return false;
    }
    
	/**
	 * massdelete
	 * delete the tickets selected 
	 * @param array
	 * @return Boolean
	 */
	public static function bulk_delete($items) {
		if(!empty($items)){
			return self::massdelete($items);
		}
		return false;
	}
	

	/**
	 * Set the status of the records
	 * @param array $items Items selected
	 * @param array $parameters Custom parameters
	 */
	public function bulk_set_status($items, $parameters) {
		if(!empty($parameters['status'])){
			foreach ($items as $item) {
				self::setStatus($item, $parameters['status']);
			}
		}
		return true;
	}	
}