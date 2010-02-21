<?php
class TagsController extends TaggingAppController {
	public $name = 'Tags';

	public $components = array('RequestHandler');

	var $helpers = array('Html', 'Form', 'Javascript', 'Tagging.Tagging');
	
	var $paginate = array(
		'Tag' => array(
			'order' => 'Tag.name ASC',
			'limit' => 20,
			'recursive' => -1
		),
		'ModelsTag' => array(
			'fields' => array('ModelsTag.model', 'ModelsTag.model_id'),
			'order' => 'ModelsTag.id DESC',
			'limit' => 10
		)
	);

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
	 * All Tags used at least once
	 * You have to create a view for this action in {your_app}/views/plugins/tagging/tags/index.ctp
	 * 
	 * Available variables in view :
	 * $data : all used tags ordered by name ASC
	 */
	function index()
	{
		$data = $this->Tag->tagCloud();
		
		if(isset($this->params['requested']))
		{
			return $data;
		}
		
		$this->set('data', $data);
	}
	
	/**
	 * View Tag
	 * Checks $this->params['pass'] for slug or id
	 * You have to create a view for this action in {your_app}/views/plugins/tagging/tags/view.ctp
	 * 
	 * Available variables in view :
	 * $tag : Tag data
	 * $data : paginated ressources tagged with this tag
	 */
	function view()
	{
		if(!isset($this->params['pass'][0]))
		{
			$this->cakeError('error404', array(array('url' => $this->action)));
		}
		
		$param = $this->params['pass'][0];

		if(preg_match('/^\d+$/', $param))
		{
			$findMethod = 'findById';
		}
		else
		{
			$findMethod = 'findBySlug';
		}
		
		$this->Tag->recursive = -1;
		
		if(!$tag = $this->Tag->{$findMethod}($param))
		{
			$this->cakeError('error404', array(array('url' => $this->action)));
		}
		
		$tagged = $this->paginate('ModelsTag', array('ModelsTag.tag_id' => $tag['Tag']['id']));
		
		// Build $data with actual Models data
		$data = array();
		
		foreach($tagged as $row)
		{
			$data[] = ClassRegistry::init($row['ModelsTag']['model'])->read(null, $row['ModelsTag']['model_id']);
		}
		
		$this->set(compact('tag', 'data'));
	}
	
	/**
	 * List Tags
	 */
	function admin_index()
	{
		$this->set('data', $this->paginate('Tag'));
	}
	
	/**
	 * Add Tag
	 */
	function admin_add()
	{
		if(!empty($this->data))
		{
			$this->Tag->create();
			
			if($this->Tag->save($this->data))
			{
				$this->Session->setFlash(__d('tagging', 'The Tag has been saved', true));
				$this->redirect(array('action'=>'index'));
			}
			else
			{
				$this->Session->setFlash(__d('tagging', 'The Tag could not be saved. Please, try again.', true));
			}
		}
	}
	
	/**
	 * Edit Tag
	 *
	 * @param int $id Tag id
	 */
	function admin_edit($id = null)
	{
		if(!$id && empty($this->data))
		{
			$this->Session->setFlash(__d('tagging', 'Invalid Tag', true));
			$this->redirect(array('action'=>'index'));
		}
		
		if(!empty($this->data))
		{
			if($this->Tag->save($this->data))
			{
				$this->Session->setFlash(__d('tagging', 'The Tag has been saved', true));
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__d('tagging', 'The Tag could not be saved. Please, try again.', true));
			}
		}
		
		if(empty($this->data))
		{
			$this->data = $this->Tag->read(null, $id);
		}
	}

	/**
	 * Delete Tag
	 *
	 * @param int $id Tag id
	 */
	function admin_delete($id = null)
	{
		if(!$id)
		{
			$this->Session->setFlash(__d('tagging', 'Invalid id for Tag', true));
		}
		
		if($this->Tag->del($id))
		{
			$this->Session->setFlash(__d('tagging', 'Tag deleted', true));
		}
		
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Admin View for Tags same as public view for now
	 */
	public function admin_view()
	{
		if(!isset($this->params['pass'][0]))
		{
			$this->cakeError('error404', array(array('url' => $this->action)));
		}
		
		$param = $this->params['pass'][0];

		if(preg_match('/^\d+$/', $param))
		{
			$findMethod = 'findById';
		}
		else
		{
			$findMethod = 'findBySlug';
		}
		
		$this->Tag->recursive = -1;
		
		if(!$tag = $this->Tag->{$findMethod}($param))
		{
			$this->cakeError('error404', array(array('url' => $this->action)));
		}
		
		$tagged = $this->paginate('ModelsTag', array('ModelsTag.tag_id' => $tag['Tag']['id']));
		
		// Build $data with actual Models data
		$data = array();
		
		foreach($tagged as $row)
		{
			$data[] = ClassRegistry::init($row['ModelsTag']['model'])->read(null, $row['ModelsTag']['model_id']);
		}
		
		$this->set(compact('tag', 'data'));
	}
}
?>
