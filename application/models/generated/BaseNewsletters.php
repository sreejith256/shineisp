<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Newsletters', 'doctrine');

/**
 * BaseNewsletters
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $news_id
 * @property string $subject
 * @property string $message
 * @property timestamp $sendat
 * @property timestamp $sent
 * @property Doctrine_Collection $NewslettersHistory
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseNewsletters extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('newsletters');
        $this->hasColumn('news_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('subject', 'string', 200, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '200',
             ));
        $this->hasColumn('message', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '',
             ));
        $this->hasColumn('sendat', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => '25',
             ));
        $this->hasColumn('sent', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => '25',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('NewslettersHistory', array(
             'local' => 'news_id',
             'foreign' => 'news_id'));
    }
}