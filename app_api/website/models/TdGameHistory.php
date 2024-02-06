<?php

/**
 * This is the model class for table "td_game_history".
 *
 * The followings are the available columns in table 'td_game_history':
 * @property integer $gh_id
 * @property integer $pc_id
 * @property integer $gm_id
 * @property integer $time_long
 * @property integer $created_time
 * @property integer $update_time
 * @property integer $is_delete
 */
class TdGameHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_game_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pc_id, gm_id, created_time', 'required'),
			array('pc_id, gm_id, time_long, created_time, update_time, is_delete', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('gh_id, pc_id, gm_id, time_long, created_time, update_time, is_delete', 'safe', 'on'=>'search'),
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
			'gh_id' => '游戏历史id',
			'pc_id' => '主机id',
			'gm_id' => '游戏模式id',
			'time_long' => '游戏时长(秒)',
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

		$criteria->compare('gh_id',$this->gh_id);
		$criteria->compare('pc_id',$this->pc_id);
		$criteria->compare('gm_id',$this->gm_id);
		$criteria->compare('time_long',$this->time_long);
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
	 * @return TdGameHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取游戏记录历史列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getGameHistoryList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'gh_id desc', $group = ''){
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



}
