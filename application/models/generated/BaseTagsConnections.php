<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('TagsConnections', 'doctrine');

/**
 * BaseTagsConnections
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $tagconnection_id
 * @property integer $tag_id
 * @property integer $customer_id
 * @property integer $domain_id
 * @property Customers $Customers
 * @property Domains $Domains
 * @property Tags $Tags
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTagsConnections extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('tags_connections');
        $this->hasColumn('tagconnection_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('tag_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('customer_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('domain_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Customers', array(
             'local' => 'customer_id',
             'foreign' => 'customer_id'));

        $this->hasOne('Domains', array(
             'local' => 'domain_id',
             'foreign' => 'domain_id'));

        $this->hasOne('Tags', array(
             'local' => 'tag_id',
             'foreign' => 'tag_id'));
    }
}