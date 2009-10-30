<?php
class TaggableBehavior extends ModelBehavior {
	/**
	 * Tag model
	 *
	 * @var object
	 */
	public $Tag = null;

	/**
	 * ModelsTag model
	 *
	 * @var object
	 */
	public $ModelsTag = null;

	/**
	 * Initializes Tag and Tagged models
	 */
	public function setup() {
		$this->Tag = ClassRegistry::init('Tagging.Tag');
		$this->ModelsTag = ClassRegistry::init('Tagging.ModelsTag');
	}

	/**
	 * Save tag and tagged models
	 *
	 * @param object $model
	 */
	public function afterSave($model) {
		if (!isset($model->data[$model->alias]['tags'])) {
			return;
		}

		$taggedConditions = array('model' => $model->alias, 'model_id' => $model->id);
		$this->ModelsTag->deleteAll($taggedConditions, false, true);

		$tags = Set::normalize($model->data[$model->alias]['tags'], false);
		$tags = array_unique($tags);

		foreach($tags as $tag) {
			$this->Tag->saveTag($tag, $taggedConditions);
		}
	}

	/**
	 * Delete tag relations with current Model Id
	 *
	 * @param object $model
	 */
	public function beforeDelete($model) {
		if (empty($model->id)) {
			return false;
		}

		$conditions = array('model' => $model->alias, 'model_id' => $model->id);
		return $this->ModelsTag->deleteAll($conditions, false, true);
	}

	/**
	 * Populates results array with a new field 'tags' with comma separated tag names
	 * Only for 1 row results sets (find('first') or read())
	 *
	 * @param object $model
	 * @param array $results
	 * @param array $primary
	 * @return array
	 */
	public function afterFind($model, $results, $primary = false) {
		if (count($results) == 1 && isset($results[0][$model->alias][$model->primaryKey])) {
			$tags = $this->ModelsTag->findTags($model->alias, $results[0][$model->alias][$model->primaryKey]);
			$results[0][$model->alias]['tags'] = join(', ', Set::extract('/Tag/name', $tags));
		}

		return $results;
	}

	/**
	 * Finds tags related to a record
	 *
	 * @param object $model
	 * @param int $id Related model primary key
	 * @return mixed Found related tags
	 */
	public function findTags($model, $id = null) {
		if (empty($id) && empty($model->id)) {
			return null;
		}

		return $this->ModelsTag->findTags($model->alias, !empty($id) ? $id : $model->id);
	}

	/**
	 * Find used tags, model specific
	 *
	 * @param array $options Options (same as classic find options)
	 * Two new keys available :
	 * - min_count : minimum number of times a tag is used
	 * - max_count : maximum number of times a tag is used
	 * @return array
	 */
	public function tagCloud($model, $options = array()) {
		return $this->ModelsTag->tagCloud($model->alias, $options);
	}

	/**
	 * Returns records that share the most tags with record of id $id
	 *
	 * @param object $model
	 * @param int $id Record Id
	 * @param bool $restrictToModel If true, returns related records of the same model, if false return all related records
	 * @param int limit Limit the number of records
	 * @return array Related records
	 */
	public function findRelated($model, $id = null, $restrictToModel = true, $limit = null) {
		if (is_bool($id)) {
			$limit = $restrictToModel;
			$restrictToModel = $id;
			$id = null;
		}

		if (empty($id) && empty($model->id)) {
			return false;
		}

		if (empty($id)) {
			$id = $model->id;
		}

		$tags = $this->ModelsTag->findTags($model->alias, $id);
		if (empty($tags)) {
			return false;
		}

		$tagIds = Set::extract('/Tag/id', $tags);
		$taggedWithModel = null;
		if ($restrictToModel) {
			$taggedWithModel = $model->alias;
		}

		$excludeIds = array_values($this->ModelsTag->find('list', array(
			'fields'     => $this->ModelsTag->alias . '.' . $this->ModelsTag->primaryKey,
			'conditions' => array('model' => $model->alias, 'model_id' => $id),
			'recursive'  => -1
		)));

		$related = $this->ModelsTag->taggedWith($taggedWithModel, $tagIds, $excludeIds, $limit);
		if (empty($related)) {
			return false;
		}

		if ($restrictToModel) {
			$modelIds = Set::extract('/' . $this->ModelsTag->alias . '/model_id', $related);
			$pk = $model->escapeField($model->primaryKey);
			$conditions = array($pk => $modelIds);
			$order = "FIELD({$pk}, " . join(', ', $modelIds) . ")";
			$results = $model->find('all', compact('conditions', 'order'));
		} else {
			$results = array();
			foreach($related as $row) {
				if ($assocModel = ClassRegistry::init($row[$this->ModelsTag->alias]['model'])) {
					$results[] = $assocModel->find('first', array(
						'conditions' => array($assocModel->alias . '.' . $assocModel->primaryKey => $row[$this->ModelsTag->alias]['model_id']),
						'recursive' => -1
					));
				}
			}
		}

		return $results;
	}
}
?>
