<?php
class TaggedFixture extends CakeTestFixture {
    var $name = 'Tagged';

    var $table = 'tagged';

    var $fields = array(
        'id' => array(
            'type' => 'integer',
            'key' => 'primary'),
        'tag_id' => array(
            'type' => 'integer',
            'length' => 10,
            'null' => false),
        'model' => array(
            'type' => 'string',
            'length' => 255,
            'null' => false),
        'model_id' => array(
            'type' => 'integer',
            'length' => 10,
            'null' => false));

    var $records = array();
}