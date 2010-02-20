<?php
class ModelsTag extends TaggingAppModel {
	public $belongsTo = array(
		'Tag' => array(
			'className' => 'Tagging.Tag',
			'counterCache' => 'count'
		)
	);

	/**
	 * Finds tags related to a record
	 *
	 * @param string $model Model name
	 * @param int $modelId Related model primary key
	 * @return mixed Found related tags
	 */
	public function findTags($model, $modelId) {
		$conditions = array(
			$this->alias . '.model' => $model,
			$this->alias . '.model_id' => $modelId
		);

		$fields = array('Tag.id', 'Tag.name', 'Tag.slug', 'Tag.created');
		$order = array('Tag.name' => 'asc');
		$recursive = 0;

		return $this->find('all', compact('fields', 'conditions', 'order', 'recursive'));
	}

	/**
	 * Find tag cloud for a model
	 *
	 * @param string $model Model name
	 * @param array $options Options (same as classic find options)
	 * Two new keys available :
	 * - min_count : minimum number of times a tag is used
	 * - max_count : maximum number of times a tag is used
	 * @return array
	 */
	public function tagCloud($model, $options = array()) {
		$conditions = array($this->alias . '.model' => $model);

		$options = Set::merge(compact('conditions'), $options);
		$options['fields'] = array('Tag.id', 'Tag.name', 'Tag.slug', 'Tag.created', 'COUNT(Tag.id) as count');
		$having = '';
		$countBounds = array();

		if (isset($options['min_count'])) {
			$countBounds[] = 'count >= ' . $options['min_count'];
			unset($options['min_count']);
		}

		if (isset($options['max_count'])) {
			$countBounds[] = 'count <= ' . $options['max_count'];
			unset($options['max_count']);
		}

		if (!empty($countBounds)) {
			$having = ' HAVING ' . join(' AND ', $countBounds);
		}

		$options['group'] = array('Tag.id' . $having);
		if (empty($options['order'])) {
			$options['order'] = array('Tag.name' => 'asc');
		}
		$options['recursive'] = 0;

		$results = $this->find('all', $options);
		if (!empty($results)) {
			foreach($results as $k => $row) {
				$results[$k]['Tag']['count'] = $row[0]['count'];
				unset($results[$k][0]);
			}
		}

		return $results;
	}

	/**
	 * Find records tagged with $tagIds, excluding record's tagged ids
	 *
	 * @param string $model Model name
	 * @param mixed $tagIds Tag id(s)
	 * @param int $excludeIds Tagged ids to exclude
	 * @return array
	 */
	public function taggedWith($model = null, $tagIds = null, $excludeIds = null, $limit = null) {
		$conditions = array($this->alias . '.tag_id' => $tagIds);

		if ($model) {
			$conditions[$this->alias . '.model'] = $model;
		}

		if ($excludeIds) {
			$conditions['NOT'] = array($this->alias . '.id' => $excludeIds);
		}

		$fields    = array($this->alias . '.model', $this->alias . '.model_id', 'COUNT(*) as count');
		$group     = array($this->alias . '.model', $this->alias . '.model_id');
		$order     = array($this->alias . '.count' => 'desc');
		$recursive = -1;

		return $this->find('all', compact('fields', 'conditions', 'group', 'order', 'limit', 'recursive'));
	}
}
?>
