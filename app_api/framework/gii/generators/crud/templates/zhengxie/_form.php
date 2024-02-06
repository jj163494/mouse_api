<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */
/* @var $form CActiveForm */
?>
<?php echo "<?php \$form=\$this->beginWidget('CActiveForm', array(
	'id'=>'".$this->class2id($this->modelClass)."-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>\n"; ?>
<div id=itable>
	<table cellspacing=1>
	<?php
foreach($this->tableSchema->columns as $column)
{
	if($column->autoIncrement)
		continue;
?>

<tr class=tr4>
			<td class=td1 width="15%"><?php echo "<?php echo ".$this->generateActiveLabel($this->modelClass,$column)."; ?>\n"; ?></td>
			<td width="85%">
			<?php echo "<?php echo ".$this->generateActiveField($this->modelClass,$column)."; ?>\n"; ?>
			<!-- <div style="color: red">*</div> -->
			</td>
		</tr>
<?php
}
?>
	<tr class=tr4>
				<td></td>
				<td>
					<button type="submit" class="reset">提交</button>
				</td>	
			</tr>
		</table>
	<?php echo "<?php\n"; ?>
	$this->endWidget ();
	?>
</div>
