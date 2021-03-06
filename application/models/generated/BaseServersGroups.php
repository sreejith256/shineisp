<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('ServersGroups', 'doctrine');

/**
 * BaseServersGroups
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $group_id
 * @property string $name
 * @property integer $fill_type
 * @property boolean $active
 * @property Doctrine_Collection $Products
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseServersGroups extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('servers_groups');
        $this->hasColumn('group_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('name', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('fill_type', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('active', 'boolean', 25, array(
             'type' => 'boolean',
             'default' => 1,
             'length' => '25',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Products', array(
             'local' => 'group_id',
             'foreign' => 'server_group_id'));
    }
}