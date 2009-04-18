<?php
class TagFixture extends CakeTestFixture {
    var $name = 'Tag';

    var $fields = array(
        'id' => array(
            'type' => 'integer',
            'key' => 'primary'),
        'name' => array(
            'type' => 'string',
            'length' => '255',
            'null' => false),
        'slug' => array(
            'type' => 'string',
            'length' => 255,
            'null' => false),
        'count' => array(
            'type' => 'integer',
            'length' => 10,
            'null' => false),
        'created' => array(
            'type' => 'datetime',
            'null' => false));

    var $records = array();
}