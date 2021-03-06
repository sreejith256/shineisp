<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('CustomAttributesValues', 'doctrine');

/**
 * BaseCustomAttributesValues
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $value_id
 * @property integer $external_id
 * @property integer $attribute_id
 * @property string $value
 * @property CustomAttributes $CustomAttributes
 * @property Doctrine_Collection $Customers
 * @property Doctrine_Collection $Servers
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCustomAttributesValues extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('custom_attributes_values');
        $this->hasColumn('value_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('external_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '4',
             ));
        $this->hasColumn('attribute_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '4',
             ));
        $this->hasColumn('value', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('CustomAttributes', array(
             'local' => 'attribute_id',
             'foreign' => 'attribute_id'));

        $this->hasMany('Customers', array(
             'local' => 'external_id',
             'foreign' => 'customer_id'));

        $this->hasMany('Servers', array(
             'local' => 'external_id',
             'foreign' => 'server_id'));
    }
}