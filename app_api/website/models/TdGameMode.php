<?php

/**
 * This is the model class for table "td_game_mode".
 *
 * The followings are the available columns in table 'td_game_mode':
 * @property integer $gm_id
 * @property integer $pc_id
 * @property string $gm_name
 * @property integer $is_delete
 */
class TdGameMode extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_game_mode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pc_id, gm_name', 'required'),
			array('pc_id, is_delete', 'numerical', 'integerOnly'=>true),
			array('gm_name', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('gm_id, pc_id, gm_name, is_delete', 'safe', 'on'=>'search'),
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
			'gm_id' => '游戏模式id',
			'pc_id' => '主机id',
			'gm_name' => '游戏模式名称',
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

		$criteria->compare('gm_id',$this->gm_id);
		$criteria->compare('pc_id',$this->pc_id);
		$criteria->compare('gm_name',$this->gm_name,true);
		$criteria->compare('is_delete',$this->is_delete);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdGameMode the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取游戏模式列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getGameModeList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'gm_id DESC', $group = ''){
		$condition .= " AND is_delete = 0";

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


	/**
	 * 获取游戏模式名称列表
	 * @param string $condition
	 * @return CActiveRecord[]
	 */
	public function getGameModeName($condition = ""){
		$criteria = array(
			'select' => "gm_id, gm_name",
			'condition' => $condition
		);
		return $this->findAll($criteria);
	}


	/**
	 * 获取游戏模式明细
	 * @param string $condition
	 * @param string $field
	 * @param string $order
	 * @return array|mixed|null
	 */
	public function getGameModeInfo($condition = "", $field = "*", $order = "gm_id DESC"){
		$condition .= " AND is_delete = 0";

		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'order' => $order,
		);
		return $this->find($criteria);
	}



}
