<?php

/**
 * UsersController
 * Handling the customers 
 * @version 1.0
 */

class Admin_CustomersController extends Zend_Controller_Action {
	
	protected $customers;
	protected $datagrid;
	protected $session;
	protected $translator;
	
	/**
	 * preDispatch
	 * Starting of the module
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Zend_Controller_Action#preDispatch()
	 */
	
	public function preDispatch() {
		$this->session = new Zend_Session_Namespace ( 'Admin' );
		$this->customers = new Customers ();
		$this->translator = Zend_Registry::getInstance ()->Zend_Translate;
		$this->datagrid = $this->_helper->ajaxgrid;
		$this->datagrid->setModule ( "customers" )->setModel ( $this->customers );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return unknown_type
	 */
	public function indexAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$redirector->gotoUrl ( '/admin/customers/list' );
	}
	
	/**
	 * indexAction
	 * Create the User object and get all the records.
	 * @return datagrid
	 */
	public function listAction() {
		$this->view->title = "Customer list";
		$this->view->description = "Here you can see all the customers.";
		$this->view->buttons = array(array("url" => "/admin/customers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))));
		$this->datagrid->setConfig ( Customers::grid() )->datagrid ();
	}
	
	/**
	 * Load Json Records
	 *
	 * @return string Json records
	 */
	public function loadrecordsAction() {
		$this->_helper->ajaxgrid->setConfig ( Customers::grid() )->loadRecords ($this->getRequest ()->getParams());
	}
	
	/**
	 * searchProcessAction
	 * Search the record 
	 * @return unknown_type
	 */
	public function searchprocessAction() {
		$this->_helper->ajaxgrid->setConfig ( Customers::grid() )->search ();
	}
	
	/*
	 *  bulkAction
	 *  Execute a custom function for each item selected in the list
	 *  this method will be call from a jQuery script 
	 *  @return string
	 */
	public function bulkAction() {
		$this->_helper->ajaxgrid->massActions ();
	}
	
	/**
	 * recordsperpage
	 * Set the number of the records per page
	 * @return unknown_type
	 */
	public function recordsperpageAction() {
		$this->_helper->ajaxgrid->setRowNum ();
	}
	
	/**
	 * newAction
	 * Create the form module in order to create a record
	 * @return unknown_type
	 */
	public function newAction() {
		$this->view->form = $this->getForm ( "/admin/customers/process" );
		$this->view->title = "Customer details";
		$this->view->description = "Here you can edit the customer details.";
		$this->view->buttons = array(array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
							   		 array("url" => "/admin/customers/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))));
		$this->render ( 'applicantform' );
	}
	
	/**
	 * confirmAction
	 * Ask to the user a confirmation before to execute the task
	 * @return null
	 */
	public function confirmAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$controller = Zend_Controller_Front::getInstance ()->getRequest ()->getControllerName ();
		try {
			if (is_numeric ( $id )) {
				$this->view->back = "/admin/$controller/edit/id/$id";
				$this->view->goto = "/admin/$controller/delete/id/$id";
				$this->view->title = $this->translator->translate ( 'WARNING: Are you sure to delete this customer, domains, invoices, orders, tickets?' );
				$this->view->description = $this->translator->translate ( 'If you delete this customer whole information will be no longer available anymore.' );
				
				$record = $this->customers->find ( $id );
				$this->view->recordselected = $record ['firstname'] . " " . $record ['lastname'] . " " . $record ['company'];
			} else {
				$this->_helper->redirector ( 'list', $controller, 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ), 'status' => 'error' ) );
			}
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
	
	/**
	 * Delete the address selected
	 * @return unknown_type
	 */
	public function addressdeleteAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {

			// Get the customer information before to delete the address 
			$customer = Addresses::getCustomer($id);
			if(is_array($customer)){
				try {
					if(Addresses::delete_address($id)){
						$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $customer['customer_id'], 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );	
					}
					
				} catch ( Exception $e ) {
					$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $customer['customer_id'], 'mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
				}
			}
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the customer
	 * @return unknown_type
	 */
	public function deletecontactAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			// Get the customer information before to delete the address
			$customer = Contacts::getCustomer($id);
			if(is_array($customer)){
				try {
					if(Contacts::delete_contact ( $id )){
						$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $customer['customer_id'], 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
					}
				} catch ( Exception $e ) {
					$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $customer['customer_id'], 'mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
				}
			}
		}
	}
	
	/**
	 * Delete the files attached
	 */
	public function deletefileAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		if (is_numeric ( $id )) {
			try {
				if(Files::del( $id )){
					$this->_helper->redirector ( 'list', 'customers', 'admin', array ('mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
				}
			} catch ( Exception $e ) {
				$this->_helper->redirector ( 'list', 'customers', 'admin', array ('mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
			}
		}
	}
	
	/**
	 * deleteAction
	 * Delete a record previously selected by the customer
	 * @return unknown_type
	 */
	public function deleteAction() {
		$files = new Files ();
		$id = $this->getRequest ()->getParam ( 'id' );
		try {
			if (is_numeric ( $id )) {
				Customers::del($id);
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
		}
		
		$this->_helper->redirector ( 'list', 'customers', 'admin', array ('mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
	}
	
	/**
	 * editAction
	 * Get a record and populate the application form 
	 * @return unknown_type
	 */
	public function editAction() {
		$form = $this->getForm ( '/admin/customers/process' );
		$form->getElement ( 'save' )->setLabel ( 'Update' );
		$id = $this->getRequest ()->getParam ( 'id' );

		$this->view->title = "Customer edit";
		$this->view->description = "Here you can edit the customer details.";
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/customers/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'))),
				array("url" => "/admin/customers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		if (! empty ( $id ) && is_numeric ( $id )) {
			
			$rs = $this->customers->getAllInfo ( $id );
			
			if (! empty ( $rs )) {
				
				$rs += CustomAttributes::getElementsValues($id);
				$rs['birthdate'] = Shineisp_Commons_Utilities::formatDateOut($rs['birthdate']);
				
				$this->view->id = $id;
				$form->populate ( $rs );
				
				if(!empty($rs['company'])){
					$this->view->title = $rs['company'] . " - " . $rs['firstname'] . " " . $rs['lastname'];
				}else{
					$this->view->title = $rs['firstname'] . " " . $rs['lastname'];
				}
				
				$this->view->buttons[] = array("url" => "/admin/orders/new", "label" => $this->translator->translate('New Order'), "params" => array('css' => array('button', 'float_right')));
				$this->view->buttons[] = array("url" => "/admin/customers/confirm/id/$id", "label" => $this->translator->translate('Delete'), "params" => array('css' => array('button', 'float_right')));
				$this->view->buttons[] = array("url" => "/default/index/fastlogin/id/" . md5($rs['email']) . "-" . $rs['password'], "label" => $this->translator->translate('Public profile'), "params" => array('css' => array('button', 'float_right')));
				
			}
		}
		
		
		$this->view->mex = $this->getRequest ()->getParam ( 'mex' );
		$this->view->mexstatus = $this->getRequest ()->getParam ( 'status' );
		
		
		
		$this->view->addressesdatagrid = $this->addressesGrid ();
		$this->view->contactsdatagrid = $this->contactsGrid ();
		$this->view->filesdatagrid = $this->filesGrid ();
		$this->view->domainsdatagrid = $this->domainsGrid ();
		$this->view->servicesdatagrid = $this->servicesGrid ();
		$this->view->ordersdatagrid = $this->ordersGrid ();
		$this->view->tickets = $this->ticketsGrid ();
		$this->view->form = $form;
		$this->render ( 'applicantform' );
	}
	
	private function domainsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Domains::findbyUserId ( $request->id, "d.domain_id, CONCAT(d.domain,'.', ws.tld) as domain, DATE_FORMAT(d.creation_date, '%d/%m/%Y') as creation_date,  DATE_FORMAT(d.expiring_date, '%d/%m/%Y') as expiring_date" );
			if (isset ( $rs [0] )) {
				return array ('name' => 'domains', 'records' => $rs, 'edit' => array ('controller' => 'domains', 'action' => 'edit' ), 'pager' => true );
			}
		}
	}
	
	private function servicesGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		try {
			if (isset ( $request->id ) && is_numeric ( $request->id )) {
				// In order to select only the fields interested we have to add an alias to all the fields. If the aliases are not created Doctrine will require an index field for each join created.
				$rs = Products::getAllServicesByCustomerID ( $request->id, 'oi.detail_id as detail_id, pd.name as productname' );
				if ($rs) {
					return array ('name' => 'services', 'records' => $rs, 'edit' => array ('controller' => 'services', 'action' => 'edit' ), 'pager' => true );
				}
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $request->id, 'mex' => $this->translator->translate ( 'Unable to process request at this time.' ) . ": " . $e->getMessage (), 'status' => 'error' ) );
		}
	}
	
	private function addressesGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Addresses::find_by_customerid($request->id);
			if (isset ( $rs )) {
				return array ('name' => 'contacts', 'records' => $rs, 'delete' => array ('controller' => 'customers', 'action' => 'addressdelete' ) );
			}
		}
	}
	
	private function contactsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Contacts::getContacts ( $request->id );
			if (isset ( $rs  )) {
				return array ('name' => 'contacts', 'records' => $rs, 'delete' => array ('controller' => 'customers', 'action' => 'deletecontact' ) );
			}
		}
	}
	
	private function filesGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Files::findbyExternalId ( $request->id, "customers", "file_id, fc.name as category, CONCAT(path, file) as file, DATE_FORMAT(date, '%d/%m/%Y %H:%i:%s') as date" );
			
			if (isset ( $rs  )) {
				return array ('name' => 'files', 'records' => $rs, 'delete' => array ('controller' => 'customers', 'action' => 'deletefile' ) );
			}
		}
	}
	
	private function ordersGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Orders::getOrdersByCustomerID ( $request->id, "o.order_id, o.order_id as order, i.number as invoice, DATE_FORMAT(o.order_date, '%d/%m/%Y') as date, o.grandtotal as total");
			if (isset ( $rs )) {
				return array ('name' => 'orders', 'records' => $rs, 'edit' => array ('controller' => 'orders', 'action' => 'edit' ) );
			}
		}
	}
	
	private function ticketsGrid() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		if (isset ( $request->id ) && is_numeric ( $request->id )) {
			$rs = Tickets::getByCustomerID ( $request->id, "t.subject, s.status, DATE_FORMAT(t.date_open, '%d/%m/%Y') as date_open, c.company");
			return array ('name' => 'tickets', 'records' => $rs, 'edit' => array ('controller' => 'tickets', 'action' => 'edit' ) );
		}
	}
	
	/**
	 * processAction
	 * Update the record previously selected
	 * @return unknown_type
	 */
	public function processAction() {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'redirector' );
		$form = $this->getForm ( "/admin/customers/process" );
		
		// Add the customer custom attributes
		$form = CustomAttributes::getElements($form);
		
		$request = $this->getRequest ();
		
		// Create the buttons in the edit form
		$this->view->buttons = array(
				array("url" => "#", "label" => $this->translator->translate('Save'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/customers/list", "label" => $this->translator->translate('List'), "params" => array('css' => array('button', 'float_right'), 'id' => 'submit')),
				array("url" => "/admin/customers/new/", "label" => $this->translator->translate('New'), "params" => array('css' => array('button', 'float_right'))),
		);
		
		try {
			
			// Check if we have a POST request
			if (! $request->isPost ()) {
				return $this->_helper->redirector ( 'list', 'customers', 'admin' );
			}
			
			if ($form->isValid ( $request->getPost () )) {

				$id = Customers::saveAll($request->getPost (), $request->getParam ( 'customer_id' ));
				CustomAttributes::saveElementsValues($form->getSubForm('attributes')->getValues(), $request->getParam ( 'customer_id' ), "customers");
				
				$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $id, 'mex' => $this->translator->translate ( 'The task requested has been executed successfully.' ), 'status' => 'success' ) );
			} else {
				$this->view->form = $form;
				$this->view->title = "Customer details";
				$this->view->description = "Here you can edit the customer details.";
				return $this->render ( 'applicantform' );
			}
		} catch ( Exception $e ) {
			$this->_helper->redirector ( 'edit', 'customers', 'admin', array ('id' => $id, 'mex' => $e->getMessage (), 'status' => 'error' ) );
		}
	}
	
	/**
	 * getForm
	 * Get the customized application form 
	 * @return unknown_type
	 */
	private function getForm($action) {
		$form = new Admin_Form_CustomersForm ( array ('action' => $action, 'method' => 'post' ) );
		
		// Add the customer custom attributes
		$form = CustomAttributes::getElements($form, "customers");
		
		return $form;
	}
	
	/**
	 * getproductinfo
	 * Get product info
	 * @return Json
	 */
	public function getcustomerinfoAction() {
		$id = $this->getRequest ()->getParam ( 'id' );
		$customer = Customers::find($id, null, true);
		if(!empty($customer)){
			die ( json_encode ( $customer ) );
		}
		die(json_encode(array()));
	}
		
}