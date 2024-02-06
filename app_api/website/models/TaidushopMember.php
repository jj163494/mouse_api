<?php

/**
 * This is the model class for table "taidushop_member".
 *
 * The followings are the available columns in table 'taidushop_member':
 * @property integer $member_id
 * @property string $member_name
 * @property string $member_truename
 * @property string $member_avatar
 * @property integer $member_sex
 * @property string $member_birthday
 * @property string $member_passwd
 * @property string $member_paypwd
 * @property string $member_email
 * @property integer $member_email_bind
 * @property string $member_mobile
 * @property integer $member_mobile_bind
 * @property string $member_qq
 * @property string $member_ww
 * @property integer $member_login_num
 * @property string $member_time
 * @property string $member_login_time
 * @property string $member_old_login_time
 * @property string $member_login_ip
 * @property string $member_old_login_ip
 * @property string $member_qqopenid
 * @property string $member_qqinfo
 * @property string $member_sinaopenid
 * @property string $member_sinainfo
 * @property string $weixin_unionid
 * @property string $weixin_info
 * @property integer $member_points
 * @property string $available_predeposit
 * @property string $freeze_predeposit
 * @property string $available_rc_balance
 * @property string $freeze_rc_balance
 * @property integer $inform_allow
 * @property integer $is_buy
 * @property integer $is_allowtalk
 * @property integer $member_state
 * @property integer $member_snsvisitnum
 * @property integer $member_areaid
 * @property integer $member_cityid
 * @property integer $member_provinceid
 * @property string $member_areainfo
 * @property string $member_privacy
 * @property integer $member_exppoints
 * @property string $invite_one
 * @property string $invite_two
 * @property string $invite_three
 * @property string $openid
 * @property integer $weight
 * @property integer $height
 * @property integer $profession_id
 * @property integer $married_type
 * @property string $interest
 * @property string $vision
 * @property string $address
 * @property string $usertype
 * @property integer $game
 */
class TaidushopMember extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'taidushop_member';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('member_name', 'required'),
			array('member_sex, member_email_bind, member_mobile_bind, member_login_num, member_points, inform_allow, is_buy, is_allowtalk, member_state, member_snsvisitnum, member_areaid, member_cityid, member_provinceid, member_exppoints, weight, height, profession_id, married_type, game', 'numerical', 'integerOnly'=>true),
			array('member_name, member_avatar, weixin_unionid, invite_one, invite_two, invite_three', 'length', 'max'=>50),
			array('member_truename, member_login_ip, member_old_login_ip', 'length', 'max'=>20),
			array('member_passwd, member_paypwd', 'length', 'max'=>32),
			array('member_email, member_qq, member_ww, member_qqopenid, member_sinaopenid', 'length', 'max'=>100),
			array('member_mobile', 'length', 'max'=>11),
			array('member_time, member_login_time, member_old_login_time, available_predeposit, freeze_predeposit, available_rc_balance, freeze_rc_balance', 'length', 'max'=>10),
			array('weixin_info, member_areainfo', 'length', 'max'=>255),
			array('openid', 'length', 'max'=>200),
			array('interest', 'length', 'max'=>128),
			array('vision', 'length', 'max'=>32),
			array('address', 'length', 'max'=>450),
			array('usertype', 'length', 'max'=>1),
			array('member_birthday, member_qqinfo, member_sinainfo, member_privacy', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('member_id, member_name, member_truename, member_avatar, member_sex, member_birthday, member_passwd, member_paypwd, member_email, member_email_bind, member_mobile, member_mobile_bind, member_qq, member_ww, member_login_num, member_time, member_login_time, member_old_login_time, member_login_ip, member_old_login_ip, member_qqopenid, member_qqinfo, member_sinaopenid, member_sinainfo, weixin_unionid, weixin_info, member_points, available_predeposit, freeze_predeposit, available_rc_balance, freeze_rc_balance, inform_allow, is_buy, is_allowtalk, member_state, member_snsvisitnum, member_areaid, member_cityid, member_provinceid, member_areainfo, member_privacy, member_exppoints, invite_one, invite_two, invite_three, openid, weight, height, profession_id, married_type, interest, vision, address, usertype, game', 'safe', 'on'=>'search'),
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
			'member_id' => 'Member',
			'member_name' => 'Member Name',
			'member_truename' => 'Member Truename',
			'member_avatar' => 'Member Avatar',
			'member_sex' => 'Member Sex',
			'member_birthday' => 'Member Birthday',
			'member_passwd' => 'Member Passwd',
			'member_paypwd' => 'Member Paypwd',
			'member_email' => 'Member Email',
			'member_email_bind' => 'Member Email Bind',
			'member_mobile' => 'Member Mobile',
			'member_mobile_bind' => 'Member Mobile Bind',
			'member_qq' => 'Member Qq',
			'member_ww' => 'Member Ww',
			'member_login_num' => 'Member Login Num',
			'member_time' => 'Member Time',
			'member_login_time' => 'Member Login Time',
			'member_old_login_time' => 'Member Old Login Time',
			'member_login_ip' => 'Member Login Ip',
			'member_old_login_ip' => 'Member Old Login Ip',
			'member_qqopenid' => 'Member Qqopenid',
			'member_qqinfo' => 'Member Qqinfo',
			'member_sinaopenid' => 'Member Sinaopenid',
			'member_sinainfo' => 'Member Sinainfo',
			'weixin_unionid' => 'Weixin Unionid',
			'weixin_info' => 'Weixin Info',
			'member_points' => 'Member Points',
			'available_predeposit' => 'Available Predeposit',
			'freeze_predeposit' => 'Freeze Predeposit',
			'available_rc_balance' => 'Available Rc Balance',
			'freeze_rc_balance' => 'Freeze Rc Balance',
			'inform_allow' => 'Inform Allow',
			'is_buy' => 'Is Buy',
			'is_allowtalk' => 'Is Allowtalk',
			'member_state' => 'Member State',
			'member_snsvisitnum' => 'Member Snsvisitnum',
			'member_areaid' => 'Member Areaid',
			'member_cityid' => 'Member Cityid',
			'member_provinceid' => 'Member Provinceid',
			'member_areainfo' => 'Member Areainfo',
			'member_privacy' => 'Member Privacy',
			'member_exppoints' => 'Member Exppoints',
			'invite_one' => 'Invite One',
			'invite_two' => 'Invite Two',
			'invite_three' => 'Invite Three',
			'openid' => 'Openid',
			'weight' => 'Weight',
			'height' => 'Height',
			'profession_id' => 'Profession',
			'married_type' => 'Married Type',
			'interest' => 'Interest',
			'vision' => 'Vision',
			'address' => 'Address',
			'usertype' => 'Usertype',
			'game' => 'Game',
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

		$criteria->compare('member_id',$this->member_id);
		$criteria->compare('member_name',$this->member_name,true);
		$criteria->compare('member_truename',$this->member_truename,true);
		$criteria->compare('member_avatar',$this->member_avatar,true);
		$criteria->compare('member_sex',$this->member_sex);
		$criteria->compare('member_birthday',$this->member_birthday,true);
		$criteria->compare('member_passwd',$this->member_passwd,true);
		$criteria->compare('member_paypwd',$this->member_paypwd,true);
		$criteria->compare('member_email',$this->member_email,true);
		$criteria->compare('member_email_bind',$this->member_email_bind);
		$criteria->compare('member_mobile',$this->member_mobile,true);
		$criteria->compare('member_mobile_bind',$this->member_mobile_bind);
		$criteria->compare('member_qq',$this->member_qq,true);
		$criteria->compare('member_ww',$this->member_ww,true);
		$criteria->compare('member_login_num',$this->member_login_num);
		$criteria->compare('member_time',$this->member_time,true);
		$criteria->compare('member_login_time',$this->member_login_time,true);
		$criteria->compare('member_old_login_time',$this->member_old_login_time,true);
		$criteria->compare('member_login_ip',$this->member_login_ip,true);
		$criteria->compare('member_old_login_ip',$this->member_old_login_ip,true);
		$criteria->compare('member_qqopenid',$this->member_qqopenid,true);
		$criteria->compare('member_qqinfo',$this->member_qqinfo,true);
		$criteria->compare('member_sinaopenid',$this->member_sinaopenid,true);
		$criteria->compare('member_sinainfo',$this->member_sinainfo,true);
		$criteria->compare('weixin_unionid',$this->weixin_unionid,true);
		$criteria->compare('weixin_info',$this->weixin_info,true);
		$criteria->compare('member_points',$this->member_points);
		$criteria->compare('available_predeposit',$this->available_predeposit,true);
		$criteria->compare('freeze_predeposit',$this->freeze_predeposit,true);
		$criteria->compare('available_rc_balance',$this->available_rc_balance,true);
		$criteria->compare('freeze_rc_balance',$this->freeze_rc_balance,true);
		$criteria->compare('inform_allow',$this->inform_allow);
		$criteria->compare('is_buy',$this->is_buy);
		$criteria->compare('is_allowtalk',$this->is_allowtalk);
		$criteria->compare('member_state',$this->member_state);
		$criteria->compare('member_snsvisitnum',$this->member_snsvisitnum);
		$criteria->compare('member_areaid',$this->member_areaid);
		$criteria->compare('member_cityid',$this->member_cityid);
		$criteria->compare('member_provinceid',$this->member_provinceid);
		$criteria->compare('member_areainfo',$this->member_areainfo,true);
		$criteria->compare('member_privacy',$this->member_privacy,true);
		$criteria->compare('member_exppoints',$this->member_exppoints);
		$criteria->compare('invite_one',$this->invite_one,true);
		$criteria->compare('invite_two',$this->invite_two,true);
		$criteria->compare('invite_three',$this->invite_three,true);
		$criteria->compare('openid',$this->openid,true);
		$criteria->compare('weight',$this->weight);
		$criteria->compare('height',$this->height);
		$criteria->compare('profession_id',$this->profession_id);
		$criteria->compare('married_type',$this->married_type);
		$criteria->compare('interest',$this->interest,true);
		$criteria->compare('vision',$this->vision,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('usertype',$this->usertype,true);
		$criteria->compare('game',$this->game);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TaidushopMember the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	/**
	 * 获取用户信息
	 * @param string $condition
	 * @param string $field
	 * @return array|mixed|null
	 */
	public function getMemberInfo($condition = "", $field = "*"){
		$criteria = array(
			'select' => $field,
			'condition' => $condition,
		);
		return $this->find($criteria);
	}


	/**
	 * 获取用户列表
	 * @param string $condition
	 * @param string $field
	 * @param int $page_size
	 * @param int $offset
	 * @param string $order
	 * @param string $group
	 * @return array|mixed|null
	 */
	public function getMemberList($condition = "", $field = '*', $page_size = 1000, $offset = 0, $order = 'member_id desc', $group = ''){
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
