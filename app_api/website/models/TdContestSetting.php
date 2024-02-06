<?php

/**
 * This is the model class for table "td_contest_setting".
 *
 * The followings are the available columns in table 'td_contest_setting':
 * @property integer $cs_id
 * @property integer $member_id
 * @property integer $cm_id
 * @property string $assist_url
 * @property string $assist_tool
 * @property integer $created_time
 */
class TdContestSetting extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'td_contest_setting';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_id, cm_id', 'required'),
			array('member_id, cm_id, created_time', 'numerical', 'integerOnly'=>true),
			array('assist_url, assist_tool', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cs_id, member_id, cm_id, assist_url, assist_tool, created_time', 'safe', 'on'=>'search'),
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
			'cs_id' => '主键id',
			'member_id' => '用户id',
			'cm_id' => '竞技模式id',
			'assist_url' => '辅助链接',
			'assist_tool' => '辅助工具',
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

		$criteria->compare('cs_id',$this->cs_id);
		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('cm_id',$this->cm_id);
		$criteria->compare('assist_url',$this->assist_url,true);
		$criteria->compare('assist_tool',$this->assist_tool,true);
		$criteria->compare('created_time',$this->created_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdContestSetting the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取竞技模式设置明细
	 * @param string $condition
	 * @param string $field
	 * @return array|mixed|null
	 */
	public function getContestSettingInfo($condition = "", $field = "*"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
		);
		return $this->find($criteria);
	}
}
