<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
// CrudCode
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $dataProvider CActiveDataProvider */

?>


<div id=itable>
	<table cellspacing="1" align="center">
		<tr class="itable_title">
			<td width="60"><input type="checkbox" id="select_all" /></td>
		<?php
		foreach($this->tableSchema->columns as $column)
		{
			if($column->autoIncrement)
				continue;
		?>
			<td width="80"><?php echo $column->comment; ?></td>
			<?php }?>
			<td width="70">操作</td>
		</tr>
	<?php echo "<?php"; ?>
	$i=0;
	foreach ( $items as $item ) {
	$edit_url = Yii::app()->createUrl('<?php echo $this->controller;?>/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
	?>
	<tr class="tr3" id="<?php echo "<?php"; ?> echo $item->id;?>">
			<td><input type="checkbox" id="option<?php echo "<?php"; ?> echo $i+1?>"
				name="option[]" value="<?php echo "<?php"; ?> echo $item->id;?>"></td>
			<?php
		foreach($this->tableSchema->columns as $column)
		{
			if($column->autoIncrement)
				continue;
		?>
			<td><?php echo "<?php"; ?> echo $item-><?php echo $column->name;?> ?></td>
			<?php }?>
			<td>
				<a href="<?php echo "<?php"; ?> echo $edit_url?>" class="edit" title="编辑"><img src="<?php echo "<?php"; ?> echo imgUrl('admin/btn_edit.png');?>" border="0"></a> 
				<span class="del" title="删除" name="<?php echo "<?php"; ?> echo $item->id;?>">
				<img src="<?php echo "<?php"; ?> echo imgUrl('admin/btn_delete.png');?>" border="0"></span>
			</td>
		</tr>
<?php echo "<?php"; ?> $i++;}?>
<tr class="btools">
			<td><input type="button" value="批量删除" id="del_btn"
				style="width: 60px; height: 28px; line-height: 28px; float: left; display: inline; overflow: hidden;">
			</td>
			<td colspan=13>
				<div class="page">
				<div style="float:left;margin-left:-30px"><?php echo "<?php"; ?> if($_GET['page']) $p=$_GET['page']; else $p=1; echo "当前第".$p."页"?></div>
				<?php echo "<?php"; ?>   $this->widget('CLinkPager',array('header'=>'','cssFile'=>'false','firstPageLabel'=>'[首页]', 'prevPageLabel'=>'[上页]','nextPageLabel'=>'[下页]','lastPageLabel'=>'[末页]', 'pages'=>$pages));?>
				</div>
			</td>
		</tr>
	</table>
</div>
<input type="hidden" id="relation" value="">
<input type="hidden" id="db_table" value="<?php echo $this->getModelClass();?>">
<input type="hidden" id="page" value=<?php echo "<?php"; ?> echo $_GET['page']?>>
