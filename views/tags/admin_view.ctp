
<div class="tags">
<h2 class="section"><?php __d('tagging', 'Tags');?></h2>

<?php

	debug($tag);

	debug($data);
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