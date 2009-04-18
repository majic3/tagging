<?php
App::import('Model', 'Tagging.Tag');
require_once ROOT.DS.APP_DIR.DS.'plugins'.DS.'tagging'.DS.'tests'.DS.'test_models.php';

class TagCase extends CakeTestCase {

    var $fixtures = array(
        'plugin.tagging.article',
        'plugin.tagging.tag',
        'plugin.tagging.tagged');

    function startTest($method) {
        $this->Article =& ClassRegistry::init('Article');
        $this->Tag =& ClassRegistry::init('Tag');
        $this->Tagged =& ClassRegistry::init('Tagged');
    }

    function test_aTag()
    {
        $this->assertEqual($this->Tag->displayField, 'name',
            "should display its name : %s");

        $result = in_array('Tagged', array_keys($this->Tag->hasMany));
        $this->assertIdentical($result, true,
            "should have many Tagged : %s");

        $result = in_array(
            'Tagging.Sluggable', array_keys($this->Tag->actsAs));
        $this->assertIdentical($result, true,
            "should acts as Sluggable : %s");

        $result = $this->Tag->actsAs;
        $this->assertEqual($result['Tagging.Sluggable']['label'], 'name',
            "should be slugged with its name : %s");
        $this->assertEqual(
            $result['Tagging.Sluggable']['translation'],
            'utf-8',
            "should be slugged with utf-8");
        $this->assertIdentical($result['Tagging.Sluggable']['overwrite'], 1,
            "should have its slug overwritten on save : %s");

        $result = $this->Tag->validate;
        $this->assertEqual(!empty($result['name']['notEmpty']), true,
            "should validate its name is not empty : %s");
        $this->assertEqual(!empty($result['name']['isUnique']), true,
            "should validate its name is unique : %s");
        $this->assertEqual(!empty($result['slug']['rule']['notEmpty']), true,
            "should validate its slug is not empty : %s");
    }

    function test_suggest() {
        $this->Tag->save(array('name' => 'International'));

        $result = $this->Tag->suggest('int');
        $expected = array('International');
        $this->assertIdentical($result, $expected,
            "should return International when given int : %s");

        $result = $this->Tag->suggest('abcd');
        $this->assertIdentical($result, null,
            "should return null if not tag matches : %s");

        $result = $this->Tag->suggest();
        $this->assertIdentical($result, null,
            "should return null if no argument is passed : %s");

        $result = $this->Tag->suggest('ab');
        $this->assertIdentical($result, null,
            "should return null if the string is too short (< 3) : %s");

        $this->Tag->save(array('name' => 'Aéronautique'));
        $result = $this->Tag->suggest('Aé');
        $this->assertIdentical($result, null,
            "should correctly count the number of letters in the argument when given a UTF-8 string : %s");

        $this->Tag->saveAll(array(
            array('name' => 'Politics'),
            array('name' => 'Police')));
        $result = $this->Tag->suggest('poli');
        $expected = array('Police', 'Politics');
        $this->assertIdentical($result, $expected,
            "should return tags ordered by name : %s");
    }

    function test_saveTag() {
        $this->Article->saveAll(array(
            array('id' => 1, 'title' => 'First article'),
            array('id' => 2, 'title' => 'Second article')));
        $this->Tag->saveTag('International', array(
            'model' => 'Article', 'model_id' => 1));

        $result = $this->Tag->find('first', array(
            'conditions' => array('name' => 'International')));
        $this->assertEqual($result['Tag']['name'], 'International',
            "should create a new tag if the name doesn't exist yet : %s");

        $result = $this->Tagged->find('first', array(
            'conditions' => array(
                'tag_id' => 1,
                'model' => 'Article',
                'model_id' => 1)));
        $this->assertIdentical(!empty($result), true,
            "should tag the given record with the new tag : %s");

        $this->Tag->saveTag('International', array(
            'model' => 'Article', 'model_id' => 2));

        $result = $this->Tag->find('all', array(
            'conditions' => array('name' => 'International')));
        $this->assertEqual(count($result), 1,
            "shouldn't create a new tag if the name already exists : %s");

        $result = $this->Tagged->find('first', array(
            'conditions' => array(
                'tag_id' => 1,
                'model' => 'Article',
                'model_id' => 2)));
        $this->assertIdentical(!empty($result), true,
            "should tag the given record with the existing tag : %s");
    }

    function test_tagCloud() {
        $this->Article->saveAll(array(
            array('id' => 1, 'title' => 'First article'),
            array('id' => 2, 'title' => 'Second article'),
            array('id' => 3, 'title' => 'Third article'),
            array('id' => 4, 'title' => 'Fourth article'),
            array('id' => 5, 'title' => 'Fifth article'),
            array('id' => 6, 'title' => 'Sixth article'),
            array('id' => 7, 'title' => 'Seventh article'),
            ));
        $this->Tag->saveTag('International', array(
            'model' => 'Article', 'model_id' => 1));
        $this->Tag->saveTag('International', array(
            'model' => 'Article', 'model_id' => 2));
        $this->Tag->saveTag('Politics', array(
            'model' => 'Article', 'model_id' => 3));
        $this->Tag->saveTag('Environment', array(
            'model' => 'Article', 'model_id' => 4));
        $this->Tag->saveTag('Sports', array(
            'model' => 'Article', 'model_id' => 5));
        $this->Tag->saveTag('Sports', array(
            'model' => 'Article', 'model_id' => 6));
        $this->Tag->saveTag('Sports', array(
            'model' => 'Article', 'model_id' => 7));

        $result = Set::extract(
            '{n}.Tag.name',
            $this->Tag->tagCloud(array('min_count' => 2, 'order' => 'name')));
        $expected = array('International', 'Sports');
        $this->assertEqual($result, $expected,
            "should return only tags having the min_count given : %s");

        $result = Set::extract(
            '{n}.Tag.name',
            $this->Tag->tagCloud(array('max_count' => 1, 'order' => 'name')));
        $expected = array('Environment', 'Politics');
        $this->assertEqual($result, $expected,
            "should return only tags having the max_count given : %s");

        $result = Set::extract(
            '{n}.Tag.name',
            $this->Tag->tagCloud(array(
                'min_count' => 2,
                'max_count' => 2,
                'order' => 'name')));
        $expected = array('International');
        $this->assertEqual($result, $expected,
            "should return only tags having between the min_count and max_count given : %s");
    }
}