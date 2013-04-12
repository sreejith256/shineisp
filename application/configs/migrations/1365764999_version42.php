<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version42 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('servers', 'servers_panel_id_panels_panel_id', array(
             'name' => 'servers_panel_id_panels_panel_id',
             'local' => 'panel_id',
             'foreign' => 'panel_id',
             'foreignTable' => 'panels',
             ));
        $this->addIndex('servers', 'servers_panel_id', array(
             'fields' => 
             array(
              0 => 'panel_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('servers', 'servers_panel_id_panels_panel_id');
        $this->removeIndex('servers', 'servers_panel_id', array(
             'fields' => 
             array(
              0 => 'panel_id',
             ),
             ));
    }
}