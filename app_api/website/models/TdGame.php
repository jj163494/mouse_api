<?php

/**
 * This is the model class for table "td_game".
 *
 * The followings are the available columns in table 'td_game':
 * @property integer $id
 * @property string $game_type
 * @property string $game_name
 * @property string $game_nameEN
 * @property string $game_nameZHT
 * @property string $game_icon
 * @property integer $sort
 */
class TdGame extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_game';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('game_type', 'length', 'max'=>16),
			array('game_name, game_nameZHT', 'length', 'max'=>32),
			array('game_nameEN', 'length', 'max'=>64),
			array('game_icon', 'length', 'max'=>255),
			array('sort', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, game_type, game_name, game_nameEN, game_nameZHT, game_icon, sort', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'game_type' => 'Game Type',
			'game_name' => 'Game Name',
			'game_nameEN' => 'Game Name EN',
			'game_nameZHT' => 'Game Name ZHT',
			'game_icon' => 'Game Icon',
			'sort' => 'sort',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('game_type',$this->game_type,true);
		$criteria->compare('game_name',$this->game_name,true);
		$criteria->compare('game_nameEN',$this->game_nameEN,true);
		$criteria->compare('game_nameZHT',$this->game_nameZHT,true);
		$criteria->compare('game_icon',$this->game_icon,true);
		$criteria->compare('sort',$this->sort,true);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdGame the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取游戏列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getGameList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'id DESC', $group = ''){
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
	 * 获取游戏明细
	 * @param string $condition
	 * @param string $field
	 * @param string $order
	 * @return array|mixed|null
	 */
	public function getGameInfo($condition = "", $field = "*", $order = "id DESC"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'order' => $order,
		);
		return $this->find($criteria);
	}

}
