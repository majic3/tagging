<?php
class TagsController extends TaggingAppController {
	public $name = 'Tags';
	public $components = array('RequestHandler');
	public $helpers = array('Javascript');

	/**
	 * JSON format tag suggestions based on first letters of tag name
	 */
	public function suggest() {
		if($this->RequestHandler->isAjax() && $this->RequestHandler->isPost()) {
			App::import('Core', 'Sanitize');
			$matches = $this->Tag->suggest(Sanitize::clean($this->params['form']['tag']));
			if (empty($matches)) {
				$matches = array();
			}
			$this->set(compact('matches'));
		}
	}

	/**
	 * Public Index for Tags
	 */
	public function index() {
	}

	/**
	 * Admin Index for Tags
	 */
	public function admin_index() {
		$tags = $this->Tag->find('all');
		$this->set(compact('tags'));
	}
}
?>
