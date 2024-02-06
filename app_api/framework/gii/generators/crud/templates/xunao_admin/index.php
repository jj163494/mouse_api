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

		<div class="main_right_content">
		<div class="main_right_content_title">
			<div class="main_right_content_title_font">管理</div>
			<div class="main_right_content_content_block_action_add">
				<a class="btn btn-success" href="<?php echo '<?php' ?> echo Yii::app()->createUrl("<?php echo $this->controller.'/create'; ?>");?>">增加</a>
			</div>
		</div>
		<div class="main_right_content_content">
			<table cellspacing=1 border="0" class="table table-hover">
				<thead>
					<tr class="main_right_content_content_title">
						<?php
						foreach($this->tableSchema->columns as $column)
						{
							if($column->autoIncrement)
								continue;
							echo '<td width="15%">'.$column->comment.'</td>';
						}
						?>
						<td width="20%">操作</td>
					</tr>
				</thead>
				<tbody>
					<?php echo "<?php"; ?>
					$i=0;
					foreach ( $items as $item ) {
					$edit_url = Yii::app()->createUrl('<?php echo $this->controller;?>/update',array('id'=>$item->id,'page'=>$_REQUEST['page']));
					?>
						<tr class="main_right_content_content_body">
						<?php 
						foreach($this->tableSchema->columns as $column)
						{
							if($column->autoIncrement)
								continue;
						?>
					<td><?php echo "<?php"; ?> echo $item-><?php echo $column->name;?> ?></td>
					<?php }?>
					<td>
						<a class="btn btn-primary btn-sm" href="<?php echo "<?php"; ?> echo $edit_url?>">编辑</a>
						<a class="btn btn-danger btn-sm action_del" data="<?php echo "<?php"; ?> echo $item->id;?>">删除</a>
					</td>
				</tr>
				<?php echo '<?php } ?>' ?>
				</tbody>
			</table>

		</div>
	</div>
	
	<input id="del_baseurl" type="hidden" value="<?php echo "<?php"; ?> echo Yii::app()->createUrl("<?php echo $this->controller ."/delete"?>",array('page'=>$pages->currentPage +1));?>" />
	<div class="main_footer <?php echo "<?php"; ?> if($pages->pageCount <= 1) echo 'main_footer_hidden';?>">
		<div class="main_footer_page">
			<?php echo "<?php"; ?>
			$this->widget ( 'CLinkPager', array (
					'header' => '',
					'firstPageLabel' => '«首页',
					'lastPageLabel' => '尾页»',
					'prevPageLabel' => '«',
					'nextPageLabel' => '»',
					'maxButtonCount' => 6,
					'pages' => $pages
			) );
			?>
		</div>
	</div>
</div>
	
