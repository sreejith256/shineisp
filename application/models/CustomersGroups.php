<?php

/**
 * CustomersGroups
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class CustomersGroups extends BaseCustomersGroups {
	
	/**
	 * grid
	 * create the configuration of the grid
	 */
	public static function grid($rowNum = 10) {
		
		$translator = Zend_Registry::getInstance ()->Zend_Translate;
		
		$config ['datagrid'] ['columns'] [] = array ('label' => null, 'field' => 'cg.group_id', 'alias' => 'group_id', 'type' => 'selectall' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'ID' ), 'field' => 'cg.group_id', 'alias' => 'group_id', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		$config ['datagrid'] ['columns'] [] = array ('label' => $translator->translate ( 'Name' ), 'field' => 'cg.name', 'alias' => 'name', 'sortable' => true, 'searchable' => true, 'type' => 'string' );
		
		$config ['datagrid'] ['fields'] = "group_id, name";
		$config ['datagrid'] ['dqrecordset'] = Doctrine_Query::create ()->select ( $config ['datagrid'] ['fields'] )->from ( 'CustomersGroups cg' );
		
		$config ['datagrid'] ['rownum'] = $rowNum;
		
		$config ['datagrid'] ['basepath'] = "/admin/customersgroups/";
		$config ['datagrid'] ['index'] = "group_id";
		$config ['datagrid'] ['rowlist'] = array ('10', '50', '100', '1000' );
		
		$config ['datagrid'] ['buttons'] ['edit'] ['label'] = $translator->translate ( 'Edit' );
		$config ['datagrid'] ['buttons'] ['edit'] ['cssicon'] = "edit";
		$config ['datagrid'] ['buttons'] ['edit'] ['action'] = "/admin/customersgroups/edit/id/%d";
		
		$config ['datagrid'] ['buttons'] ['delete'] ['label'] = $translator->translate ( 'Delete' );
		$config ['datagrid'] ['buttons'] ['delete'] ['cssicon'] = "delete";
		$config ['datagrid'] ['buttons'] ['delete'] ['action'] = "/admin/customersgroups/delete/id/%d";
		return $config;
	}
	
	/**
	 * find
	 * Get a record by ID
	 * 
	 * @param
	 *       	 $id
	 * @return Doctrine Record
	 */
	public static function find($id, $fields = "*", $retarray = false) {
		$dq = Doctrine_Query::create ()->select ( $fields )->from ( 'Customersgroups cg' )->where ( "cg.group_id = ?", $id )->limit ( 1 );
		
		$retarray = $retarray ? Doctrine_Core::HYDRATE_ARRAY : null;
		$record = $dq->execute ( array (), $retarray );
		return $record;
	}
	
	/**
	 * Save all the data in the database
	 * @param array $data
	 * @param integer $id
	 */
	public static function saveAll(array $data, $id) {
	
		if(!empty($data) && is_array($data)){
			if(is_numeric($id)){
				$group = Doctrine::getTable ( 'Customersgroups' )->find($id);
			}else{
				$group = new Customersgroups();
			}
			
			$group->name = $data ['name'];
			$group->save ();
			return $group['group_id'];
		}
		
		return false;
	}
	
	/**
	 * delete
	 * Delete a record by ID
	 * 
	 * @param
	 *       	 $id
	 */
	public static function deleteItem($id) {
		Doctrine::getTable ( 'Customersgroups' )->findOneBy ( 'group_id', $id )->delete ();
	}
	
	/**
	 * getList
	 * Get a list ready for the html select object
	 * 
	 * @return array
	 */
	public static function getList($empty = false) {
		$items = array ();
		$arrTypes = Doctrine::getTable ( 'Customersgroups' )->findAll ();
		if ($empty) {
			$items [] = "";
		}
		foreach ( $arrTypes->getData () as $c ) {
			$items [$c ['group_id']] = $c ['name'];
		}
		
		return $items;
	}
}