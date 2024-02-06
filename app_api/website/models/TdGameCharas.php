<?php

/**
 * This is the model class for table "td_game_charas".
 *
 * The followings are the available columns in table 'td_game_charas':
 * @property integer $chara_id
 * @property integer $game_id
 * @property string $game_name
 * @property string $game_nameEN
 * @property string $game_nameZHT
 * @property string $chara_name
 * @property string $chara_nameEN
 * @property string $chara_nameZHT
 * @property string $chara_desc
 * @property string $chara_descEN
 * @property string $chara_descZHT
 * @property string $chara_image1
 * @property string $chara_image2
 * @property integer $download
 * @property integer $macro_count
 * @property integer $created_time
 * @property integer $update_time
 * @property integer $is_delete
 */
class TdGameCharas extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_game_charas';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('game_name, chara_name', 'required'),
			array('game_id, download, macro_count, created_time, update_time, is_delete', 'numerical', 'integerOnly'=>true),
			array('game_name, chara_name, game_nameZHT, chara_nameZHT', 'length', 'max'=>32),
			array('game_nameEN, chara_nameEN', 'length', 'max'=>64),
			array('chara_desc, chara_descZHT', 'length', 'max'=>100),
			array('chara_descEN, chara_image1, chara_image2', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('chara_id, game_id, game_name, game_nameEN, game_nameZHT, chara_name, chara_nameEN, chara_nameZHT, chara_desc, chara_descEN, chara_descZHT, chara_image1, chara_image2, download, macro_count, created_time, update_time, is_delete', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'chara_id' => '主键id',
			'game_id' => '游戏id',
			'game_name' => '游戏名称',
			'game_nameEN' => '游戏名称(英文)',
			'game_nameZHT' => '游戏名称(繁体中文)',
			'chara_name' => '角色名称',
			'chara_nameEN' => '角色名称(英文)',
			'chara_nameZHT' => '角色名称(繁体中文)',
			'chara_desc' => '角色简介',
			'chara_descEN' => '角色简介(英文)',
			'chara_descZHT' => '角色简介(繁体中文)',
			'chara_image1' => '角色图像(大图)',
			'chara_image2' => '角色图像(小图)',
			'download' => '下载量',
			'macro_count' => '宏数量',
			'created_time' => '创建时间',
			'update_time' => '修改时间',
			'is_delete' => '0未删除,1已删除',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('chara_id',$this->chara_id);
		$criteria->compare('game_id',$this->game_id,true);
		$criteria->compare('game_name',$this->game_name,true);
		$criteria->compare('game_nameEN',$this->game_nameEN,true);
		$criteria->compare('game_nameZHT',$this->game_nameZHT,true);
		$criteria->compare('chara_name',$this->chara_name,true);
		$criteria->compare('chara_nameEN',$this->chara_nameEN,true);
		$criteria->compare('chara_nameZHT',$this->chara_nameZHT,true);
		$criteria->compare('chara_desc',$this->chara_desc,true);
		$criteria->compare('chara_descEN',$this->chara_descEN,true);
		$criteria->compare('chara_descZHT',$this->chara_descZHT,true);
		$criteria->compare('chara_image1',$this->chara_image1,true);
		$criteria->compare('chara_image2',$this->chara_image2,true);
		$criteria->compare('download',$this->download);
		$criteria->compare('macro_count',$this->macro_count);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('is_delete',$this->is_delete);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdGameCharas the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取游戏角色明细
	 * @param string $condition
	 * @param string $field
	 * @param string $order
	 * @return array|mixed|null
	 */
	public function getGameCharaInfo($condition = "", $field = "*", $order = "chara_id DESC"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'order' => $order,
		);
		return $this->find($criteria);
	}


	/**
	 * 根据id获取游戏角色信息
	 * @param $chara_id
	 * @param string $field
	 * @return CActiveRecord
	 */
	public function getGameCharaById($chara_id, $field = "*"){
		$criteria = array(
			'select' => $field,
			'condition' => "chara_id = {$chara_id} AND is_delete = 0",
		);
		return $this->find($criteria);
	}


	/**
	 * 获取游戏角色列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getGameCharaList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'chara_id DESC', $group = ''){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'limit' => $page_size,
			'offset' => $offset,
			'order' => $order,
			'group' => $group
		);
		return $this->findAll($criteria);
	}

	public function getGameList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'chara_id DESC', $group = ''){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'limit' => $page_size,
			'offset' => $offset,
			'order' => $order,
			'group' => $group
		);
		return $this->findAll($criteria);
	}
}
