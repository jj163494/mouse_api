<?php

/**
 * This is the model class for table "td_user_terminals".
 *
 * The followings are the available columns in table 'td_user_terminals':
 * @property integer $terminal_id
 * @property string $terminal_code
 * @property integer $member_id
 * @property integer $terminal_type
 * @property integer $created_time
 */
class TdUserTerminals extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_user_terminals';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('terminal_code, member_id, created_time', 'required'),
			array('member_id, terminal_type, created_time', 'numerical', 'integerOnly'=>true),
			array('terminal_code', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('terminal_id, terminal_code, member_id, terminal_type, created_time', 'safe', 'on'=>'search'),
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
			'terminal_id' => '主键id',
			'terminal_code' => '终端标识',
			'member_id' => '用户id',
			'terminal_type' => '终端类型(1PC,2安卓,3IOS)',
			'created_time' => '创建时间',
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

		$criteria->compare('terminal_id',$this->terminal_id);
		$criteria->compare('terminal_code',$this->terminal_code,true);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('terminal_type',$this->terminal_type);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdUserTerminals the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取用户终端信息
	 * @param string $condition
	 * @param string $field
	 * @return array|mixed|null
	 */
	public function getUserTerminalInfo($condition = "", $field = "*"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
		);
		return $this->find($criteria);
	}


	/**
	 * 获取用户终端信息列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getUserTerminalList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'terminal_id DESC', $group = ''){
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
