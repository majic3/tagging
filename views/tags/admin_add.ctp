<div class="tags form">
<?php echo $form->create('Tag');?>
	<fieldset>
 		<legend><?php __d('tagging', 'Add Tag');?></legend>
	<?php
		echo $form->input('name', array(
			'label' => __d('tagging', 'Name:', true),
			'error' => array(
				'notEmpty' => __d('tagging', 'Tag name cannot be left blank', true),
				'isUnique' => __d('tagging', 'This Tag already exists.', true),
			)
		));
	?>
	</fieldset>
<?php echo $form->end(__d('tagging', 'Submit', true));?> 
</div>
<?php $partialLayout->blockStart('sidebar'); ?>
    <li>
        <?php echo $html->link(__d('tagging', 'List Tags', true), array('action'=>'index')); ?>
    </li>
	<li>
		<?php echo $this->element('admin_tag_cloud'); ?>
	</li>
<?php $partialLayout->blockEnd(); ?>