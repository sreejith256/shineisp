<?php
/**
 * 
 * @author Shine Software
 *
 */
class Shineisp_Api_Panels_Base {
	
	protected $isLive;
	protected $name;
	protected $path;
	protected $session;
	protected $actions = array();

						
	/**
	 * @return the $session
	 */
	public function getSession() {
		return $this->session;
	}

	/**
	 * @param field_type $session
	 */
	public function setSession($session) {
		$this->session = $session;
	}

	/**
	 * @return the $path
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param field_type $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return the $isLive
	 */
	public function getIsLive() {
		return $this->isLive;
	}

	/**
	 * @param field_type $isLive
	 */
	public function setIsLive($isLive) {
		$this->isLive = $isLive;
	}
	/**
	 * @return the $actions
	 */
	public function getActions() {
		return $this->actions;
	}

	/**
	 * @param field_type $actions
	 */
	public function setActions($actions) {
		$this->actions = $actions;
	}

	/**
	 * 
	 * Send the email profile to the user
	 */
	public function sendMail($task){
		$isp = Isp::getActiveISP();
		
		$retval = Shineisp_Commons_Utilities::getEmailTemplate ( 'new_hosting' );
		if ($retval) {
			$subject = $retval ['subject'];
			$template = $retval ['template'];
			
			// Get the service details
			$service = OrdersItems::getAllInfo($task['orderitem_id']);
			
			// If the setup has been written by the task action then ...
			if(!empty($service['setup'])){
				$setup = json_decode($service['setup'], true);
					
				// Get the service/product name
				$productname = !empty($service['Products']['ProductsData'][0]['name']) ? $service['Products']['ProductsData'][0]['name'] : "";

				// Check if the customer is present in the service
				if(!empty($service['Orders']['Customers'])){
					
					// Getting the customer
					$customer = $service['Orders']['Customers'];
					
					// Creating the subject of the email
					$subject = str_replace ( "[hostingplan]", $productname, $subject );

					$template = str_replace ( "[fullname]", $customer ['lastname'] . " " . $customer ['firstname'], $template );
					$template = str_replace ( "[hostingplan]", $productname, $template );
					$template = str_replace ( "[controlpanel]", $isp ['website'] . ":8080", $template );
					$template = str_replace ( "[signature]", $isp ['company'], $template );
					
					$strSetup = "";
					foreach ($setup as $section => $details) {
						$strSetup .= strtoupper($section) . "\n===============\n";
						foreach ($details as $label => $detail){
							$strSetup .= "$label: " . $detail . "\n"; 
						}
						$strSetup .= "\n";
					}
					
					$template = str_replace ( "[setup]", $strSetup, $template );
					
					// Send the email
					Shineisp_Commons_Utilities::SendEmail ( $isp ['email'], $isp ['email'], null, $subject, $template );
				}
			}
		}	
	}
	
}