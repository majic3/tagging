
<div class="tags">
<h2 class="section"><?php __d('tagging', 'Tag'); echo ' ' . $tag['Tag']['name'], ' count(', $tag['Tag']['count'], ') ', $time->niceShort($tag['Tag']['modified']);?></h2>

<table class="data">
<thead>
<tr>
	<th>Model</th>
	<th>Title/Name</th>
	<th>Tags</th>
	<th>Misc</th>
	<th>Created</th>
	<th>Updated</th>
	<th>Actions</th>
</tr>
</thead>
<tbody>
<?php

$tagTr = <<<TAGTR
<tr class="{TYPE}">
	<th class="type">{TYPE}</th>
	<td>{TITLE}</td>
	<td>{TAGS}</td>
	<td>{MISC}</td>
	<td class="date">{CREATED}</td>
	<td class="date">{UPDATED}</td>
	<td class="actions">{ACTIONS}</td>
</tr>
TAGTR;
$tagSettings = array(
	'class' => 'tags',
	'itemTemplate' => '<li%attr%><a href="/admin/tagging/tags/view%url%"><span>%name%</span></a></li>',
	'activeItemTemplate' => '<li%attr%><a href="/admin/tagging/tags/view%url%"><span>%name%</span></a></li>'
);

	foreach($data as $related)	{
		$ItemTags = $title = '';
		$args = array();
		if(isset($related['Post']))	{
			$ItemTags = explode(',', str_replace(' ', '', $related['Post']['tags']));
			$title = $html->link($related['Post']['title'], array('plugin' => null, 'action' => 'admin_edit', $related['Post']['id']), array('title' => __('Edit this post.', true)));
			$title.= '<br />' . $html->link('View', '/' . Configure::read('Wildflower.postsParent') . '/' . $related['Post']['slug'], array('class' => '', 'rel' => 'permalink', 'title' => __('View this post.', true)));

			$args[] = 'post';
			$args[] = $title;
			$args[] = $navigation->create($ItemTags, $tagSettings);
			$args[] = '';
			$args[] = $time->niceShort($related['Post']['created']);
			$args[] = $time->niceShort($related['Post']['updated']);
			$args[] = '';
		} else if(isset($related['Page']))	{
			$ItemTags = explode(',', str_replace(' ', '', $related['Page']['tags']));
			$title = $html->link($related['Page']['title'], array('plugin' => null, 'action' => 'edit', $related['Page']['id']), array('title' => 'Edit this page.'));
			$title.= '<br />' . $html->link('View', $related['Page']['url'], array('class' => '', 'rel' => 'permalink', 'title' => 'View this page.'));

			$args[] = 'page';
			$args[] = $title;
			$args[] = $navigation->create($ItemTags, $tagSettings);
			$args[] = '';
			$args[] = $time->niceShort($related['Page']['created']);
			$args[] = $time->niceShort($related['Page']['updated']);
			$args[] = '';
		} else if(isset($related['Asset']))	{
			$ItemTags = explode(',', str_replace(' ', '', $related['Asset']['tags']));
			$title = $html->link($related['Asset']['name'], array('plugin' => null, 'action' => 'edit', $related['Asset']['id']), array('title' => 'Edit this asset.'));
			$title.= ' <br />THUMBNAIL';

			$args[] = 'asset';
			$args[] = $title;
			$args[] = $navigation->create($ItemTags, $tagSettings);
			$args[] = '';
			$args[] = $time->niceShort($related['Asset']['created']);
			$args[] = $time->niceShort($related['Asset']['updated']);
			$args[] = '';
		} else {
			$title = '';
			$modelType = 'Other';

			$args[] = strtolower($modelType);
			$args[] = $title;
			$args[] = $navigation->create($ItemTags, $tagSettings);
			$args[] = '';
			$args[] = (true == false) ? $time->niceShort($related[$modelType]['created']) : 'n/a';
			$args[] = (true == false) ? $time->niceShort($related[$modelType]['updated']) : 'n/a';
			$args[] = '';
		}
		echo str_replace(
			array(
				'{TYPE}', 
				'{TITLE}', 
				'{TAGS}', 
				'{MISC}', 
				'{CREATED}', 
				'{UPDATED}', 
				'{ACTIONS}'
			), 
			$args, 
			$tagTr
		);
	}
?>
</tbody>
</table>
</div>


<?php $partialLayout->blockStart('sidebar'); ?>
    <li>
        <?php echo $html->link(__d('tagging', 'List Tags', true), array('action'=>'index')); ?>
    </li>
    <li>
        <?php echo $html->link(__d('tagging', 'New Tag', true), array('action'=>'add')); ?>
    </li>
	<li>
		<?php echo $this->element('admin_tag_cloud'); ?>
	</li>
<?php $partialLayout->blockEnd(); ?>