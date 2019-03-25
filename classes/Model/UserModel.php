<?php
/* Copyright (c) 1998-2011 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace QU\LERQ\Model;

/**
 * Class UserModel
 * @package QU\LERQ\Model
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class UserModel
{
	/** @var int */
	private $usr_id;
	/** @var string */
	private $login;
	/** @var string */
	private $firstname;
	/** @var string */
	private $lastname;
	/** @var string */
	private $title;
	/** @var string */
	private $gender;
	/** @var string */
	private $email;
	/** @var string */
	private $institution;
	/** @var string */
	private $street;
	/** @var string */
	private $city;
	/** @var string */
	private $country;
	/** @var string */
	private $phone_office;
	/** @var string */
	private $hobby;
	/** @var string */
	private $department;
	/** @var string */
	private $phone_home;
	/** @var string */
	private $phone_mobile;
	/** @var string */
	private $fax;
	/** @var string */
	private $referral_comment;
	/** @var string */
	private $matriculation;
	/** @var int */
	private $active;
	/** @var string */
	private $approval_date;
	/** @var string */
	private $agree_date;
	/** @var string */
	private $auth_mode;
	/** @var string */
	private $ext_account;
	/** @var string */
	private $birthday;
	/** @var array */
	private $udf_data;

	/**
	 * @return int
	 */
	public function getUsrId(): int
	{
		return $this->usr_id;
	}

	/**
	 * @param int $usr_id
	 * @return UserModel
	 */
	public function setUsrId(int $usr_id): UserModel
	{
		$this->usr_id = $usr_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogin(): string
	{
		return $this->login;
	}

	/**
	 * @param string $login
	 * @return UserModel
	 */
	public function setLogin(string $login): UserModel
	{
		$this->login = $login;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFirstname(): string
	{
		return $this->firstname;
	}

	/**
	 * @param string $firstname
	 * @return UserModel
	 */
	public function setFirstname(string $firstname): UserModel
	{
		$this->firstname = $firstname;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastname(): string
	{
		return $this->lastname;
	}

	/**
	 * @param string $lastname
	 * @return UserModel
	 */
	public function setLastname(string $lastname): UserModel
	{
		$this->lastname = $lastname;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return UserModel
	 */
	public function setTitle(string $title): UserModel
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGender(): string
	{
		return $this->gender;
	}

	/**
	 * @param string $gender
	 * @return UserModel
	 */
	public function setGender(string $gender): UserModel
	{
		$this->gender = $gender;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return UserModel
	 */
	public function setEmail(string $email): UserModel
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getInstitution(): string
	{
		return $this->institution;
	}

	/**
	 * @param string $institution
	 * @return UserModel
	 */
	public function setInstitution(string $institution): UserModel
	{
		$this->institution = $institution;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getStreet(): string
	{
		return $this->street;
	}

	/**
	 * @param string $street
	 * @return UserModel
	 */
	public function setStreet(string $street): UserModel
	{
		$this->street = $street;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCity(): string
	{
		return $this->city;
	}

	/**
	 * @param string $city
	 * @return UserModel
	 */
	public function setCity(string $city): UserModel
	{
		$this->city = $city;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCountry(): string
	{
		return $this->country;
	}

	/**
	 * @param string $country
	 * @return UserModel
	 */
	public function setCountry(string $country): UserModel
	{
		$this->country = $country;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPhoneOffice(): string
	{
		return $this->phone_office;
	}

	/**
	 * @param string $phone_office
	 * @return UserModel
	 */
	public function setPhoneOffice(string $phone_office): UserModel
	{
		$this->phone_office = $phone_office;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getHobby(): string
	{
		return $this->hobby;
	}

	/**
	 * @param string $hobby
	 * @return UserModel
	 */
	public function setHobby(string $hobby): UserModel
	{
		$this->hobby = $hobby;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDepartment(): string
	{
		return $this->department;
	}

	/**
	 * @param string $department
	 * @return UserModel
	 */
	public function setDepartment(string $department): UserModel
	{
		$this->department = $department;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPhoneHome(): string
	{
		return $this->phone_home;
	}

	/**
	 * @param string $phone_home
	 * @return UserModel
	 */
	public function setPhoneHome(string $phone_home): UserModel
	{
		$this->phone_home = $phone_home;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPhoneMobile(): string
	{
		return $this->phone_mobile;
	}

	/**
	 * @param string $phone_mobile
	 * @return UserModel
	 */
	public function setPhoneMobile(string $phone_mobile): UserModel
	{
		$this->phone_mobile = $phone_mobile;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFax(): string
	{
		return $this->fax;
	}

	/**
	 * @param string $fax
	 * @return UserModel
	 */
	public function setFax(string $fax): UserModel
	{
		$this->fax = $fax;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReferralComment(): string
	{
		return $this->referral_comment;
	}

	/**
	 * @param string $referral_comment
	 * @return UserModel
	 */
	public function setReferralComment(string $referral_comment): UserModel
	{
		$this->referral_comment = $referral_comment;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMatriculation(): string
	{
		return $this->matriculation;
	}

	/**
	 * @param string $matriculation
	 * @return UserModel
	 */
	public function setMatriculation(string $matriculation): UserModel
	{
		$this->matriculation = $matriculation;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getActive(): int
	{
		return $this->active;
	}

	/**
	 * @param int $active
	 * @return UserModel
	 */
	public function setActive(int $active): UserModel
	{
		$this->active = $active;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getApprovalDate(): string
	{
		return $this->approval_date;
	}

	/**
	 * @param string $approval_date
	 * @return UserModel
	 */
	public function setApprovalDate(string $approval_date): UserModel
	{
		$this->approval_date = $approval_date;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAgreeDate(): string
	{
		return $this->agree_date;
	}

	/**
	 * @param string $agree_date
	 * @return UserModel
	 */
	public function setAgreeDate(string $agree_date): UserModel
	{
		$this->agree_date = $agree_date;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAuthMode(): string
	{
		return $this->auth_mode;
	}

	/**
	 * @param string $auth_mode
	 * @return UserModel
	 */
	public function setAuthMode(string $auth_mode): UserModel
	{
		$this->auth_mode = $auth_mode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getExtAccount(): string
	{
		return $this->ext_account;
	}

	/**
	 * @param string $ext_account
	 * @return UserModel
	 */
	public function setExtAccount(string $ext_account): UserModel
	{
		$this->ext_account = $ext_account;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBirthday(): string
	{
		return $this->birthday;
	}

	/**
	 * @param string $birthday
	 * @return UserModel
	 */
	public function setBirthday(string $birthday): UserModel
	{
		$this->birthday = $birthday;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getUdfData(): array
	{
		return $this->udf_data;
	}

	/**
	 * @param array $udf_data
	 * @return UserModel
	 */
	public function setUdfData(array $udf_data): UserModel
	{
		$this->udf_data = $udf_data;
		return $this;
	}

	/**
	 * @return false|string
	 */
	public function __toString()
	{
		return json_encode([
			'usr_id' => $this->getUsrId(),
			'username' => $this->getLogin(),
			'firstname' => $this->getFirstname(),
			'lastname' => $this->getLastname(),
			'title' => $this->getTitle(),
			'gender' => $this->getGender(),
			'email' => $this->getEmail(),
			'institution' => $this->getInstitution(),
			'street' => $this->getStreet(),
			'city' => $this->getCity(),
			'country' => $this->getCountry(),
			'phone_office' => $this->getPhoneOffice(),
			'hobby' => $this->getHobby(),
			'department' => $this->getDepartment(),
			'phone_home' => $this->getPhoneHome(),
			'phone_mobile' => $this->getPhoneMobile(),
			'phone_fax' => $this->getFax(),
			'referral_comment' => $this->getReferralComment(),
			'matriculation' => $this->getMatriculation(),
			'active' => ($this->getActive() == 1),
			'approval_date' => $this->getApprovalDate(),
			'agree_date' => $this->getAgreeDate(),
			'auth_mode' => $this->getAuthMode(),
			'ext_account' => $this->getExtAccount(),
			'birthday' => $this->getBirthday(),
			'udf_data' => json_encode($this->getUdfData()),
		]);
	}
}