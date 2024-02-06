<?php

/**
 * This is the model class for table "td_contest_mode".
 *
 * The followings are the available columns in table 'td_contest_mode':
 * @property integer $cm_id
 * @property integer $member_id
 * @property integer $recommend_cm_id
 * @property string $game_name
 * @property string $game_nameEN
 * @property string $game_nameZHT
 * @property integer $game_type
 * @property integer $created_time
 * @property integer $is_delete
 */
class TdContestMode extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_contest_mode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created_time', 'required'),
			array('member_id, recommend_cm_id, game_type, created_time, is_delete', 'numerical', 'integerOnly'=>true),
			array('game_name, game_nameZHT', 'length', 'max'=>32),
			array('game_nameEN', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cm_id, member_id, recommend_cm_id, game_name, game_nameEN, game_nameZHT, game_type, created_time, is_delete', 'safe', 'on'=>'search'),
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
			'cm_id' => '竞技模式id',
			'member_id' => '用户id',
			'recommend_cm_id' => '推荐竞技模式id',
			'game_name' => '游戏名称',
			'game_nameEN' => '游戏名称(英文)',
			'game_nameZHT' => '游戏名称(繁体中文)',
			'game_type' => '游戏类型 1RTS 2MOBA 3FPS 4其他',
			'created_time' => '创建时间',
			'is_delete' => '是否删除 0未删除,1已删除',
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

		$criteria->compare('cm_id',$this->cm_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('recommend_cm_id',$this->recommend_cm_id);
		$criteria->compare('game_name',$this->game_name,true);
		$criteria->compare('game_nameEN',$this->game_nameEN,true);
		$criteria->compare('game_nameZHT',$this->game_nameZHT,true);
		$criteria->compare('game_type',$this->game_type);
		$criteria->compare('created_time',$this->created_time);
		$criteria->compare('is_delete',$this->is_delete);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdContestMode the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取竞技模式列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getContestModeList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'cm_id DESC', $group = ''){
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
	 * 获取竞技模式明细
	 * @param string $condition
	 * @param string $field
	 * @param string $order
	 * @return array|mixed|null
	 */
	public function getContestModeInfo($condition = "", $field = "*", $order = "cm_id DESC"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
			'order' => $order,
		);
		return $this->find($criteria);
	}

}
