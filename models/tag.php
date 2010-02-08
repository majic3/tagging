<?php
App::import('Core', 'Multibyte');

class Tag extends TaggingAppModel {
	public $displayField = 'name';
	public $hasMany = array('Tagging.ModelsTag' => array('dependent' => true));
	public $actsAs = array(
		'Slug' => array('separator' => '-', 'overwrite' => true, 'label' => 'name'),
	);
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'notEmpty'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'on' => 'create',
				'message' => 'isUnique'
			)
		)
	);

	/**
	 * Returns tags matching first letters
	 *
	 * @param string $firstLetters
	 * @return array Matching tag names as a simple associative array
	 */
	public function suggest($firstLetters = '') {
		if (empty($firstLetters)) {
			return;
		}

		$firstLetters = trim($firstLetters);
		if (Multibyte::strlen($firstLetters) <= 2) {
			return;
		}

		$fields     = array('name');
		$conditions = array('name LIKE' => "{$firstLetters}%");
		$order      = 'name ASC';
		$limit      = 10;
		$recursive  = -1;

		return array_values($this->find('list', compact(
			'fields', 'conditions', 'order', 'limit', 'recursive'
		)));
	}

	/**
	 * Save a tag and the association with the tagged model
	 *
	 * @param string $tag Tag name
	 * @param array $tagged Tagged model parameters array : tagged model name and tagged model primary key
	 */
	public function saveTag($tag = '', $tagged = array()) {
		if (empty($tag) || empty($tagged)) {
			return;
		}

		$currentTag = $this->find('first', array('conditions'=>array($this->alias . '.name' => $tag), 'recursive'=>-1));
		if (!empty($currentTag)) {
			$tag = $currentTag;
		} else {
			$tag = array('Tag' => array('name' => $tag));
			$this->create();
		}

		$result = false;
		if ($this->save($tag)) {
			// Go around cake bug with naming of plugin based binding instances
			$className = 'Tagging.ModelsTag';
			if (empty($this->ModelsTag) && is_object($this->$className)) {
				list($plugin, $alias) = explode('.', $className);
				$this->ModelsTag = $this->$className;
				$this->ModelsTag->alias = $alias;
			}

			$tagged = array($this->ModelsTag->alias => $tagged);
			$tagged[$this->ModelsTag->alias]['tag_id'] = $this->id;

			$this->ModelsTag->create();
			$result = $this->ModelsTag->save($tagged) !== false;
		}

		return $result;
	}

	/**
	 * Find used tags, all models
	 *
	 * @param array $options Options (same as classic find options)
	 * Two new keys available :
	 * - minCount : minimum number of times a tag is used
	 * - maxCount : maximum number of times a tag is used
	 * @return array
	 */
	public function tagCloud($options = array()) {
		$defaults = array('minCount' => 0, 'maxCount' => null);
		$options = array_merge($defaults, $options);
		$conditions = array();

		$conditions['Tag.count >='] = $options['minCount'];
		if (!empty($options['maxCount'])) {
			$conditions['Tag.count <='] = $options['maxCount'];
		}

		$recursive = -1;
		$order = array($this->alias . '.name' => 'asc');
		$options = Set::merge(compact('conditions', 'order', 'recursive'), array_diff_key($options, $defaults));
		return $this->find('all', $options);
	}
}
?>
