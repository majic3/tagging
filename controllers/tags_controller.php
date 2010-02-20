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
	 * JSON format tag suggestions based on first letters of tag name
	 */
	public function admin_suggest() {
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
		$this->Tag->recursive = 1;
		$tags = $this->Tag->find('all');
		$this->set(compact('tags'));
	}

	/**
	 * Admin Index for Tags
	 */
	public function admin_view($id, $tag) {
		//debug(am($id, $tag)); die();
		$this->Tag->id = $id;
		
		$tag = $this->Tag->findById($id);
		$this->set(compact('tag')); 
	}
}
?>
