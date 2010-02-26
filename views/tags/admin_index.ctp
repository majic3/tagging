<div class="tags">
<h2 class="section"><?php __d('tagging', 'Tags');?></h2>
<table class="data" cellspacing="1">
<caption></caption>
<thead>
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('name');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th class="actions"><?php __d('tagging', 'Actions');?></th>
</tr>
</thead>
<tbody>
<?php
$i = 0;
foreach ($data as $row):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<th class="tag">
			<?php echo $row['Tag']['id']; ?>
		</th>
		<td>
			<?php echo $html->link(
				$row['Tag']['name'],
				array(
					'action' => 'view',
					$row['Tag']['slug'],
					$row['Tag']['id']
				)
			); ?>
		</td>
		<td class="date">
			<?php echo $time->niceShort($row['Tag']['created']); ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__d('tagging', 'Edit', true), array('action'=>'edit', $row['Tag']['id'])); ?>
			<?php echo $html->link(__d('tagging', 'Delete', true), array('action'=>'delete', $row['Tag']['id']), null, sprintf(__d('tagging', 'Are you sure you want to delete # %s?', true), $row['Tag']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
	<?php
		$paginator->options(array('url' => $this->passedArgs));
		echo $this->element('admin_pagination');
	?>
</div>


<?php $partialLayout->blockStart('sidebar'); ?>
    <li>
        <?php echo $html->link(__d('tagging', 'New Tag', true), array('action'=>'add')); ?>
    </li>
	<li>
		<?php echo $this->element('admin_tag_cloud'); ?>
	</li>
<?php $partialLayout->blockEnd(); ?>