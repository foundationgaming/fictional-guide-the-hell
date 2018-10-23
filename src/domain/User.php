<?php
namespace quotemaker\domain;

class User
{
    protected $id;
    protected $username;
    protected $emailAddress;
    protected $realName;
    protected $password;
    protected $roles;
    protected $encoder;

    public function __construct($encoder=NULL) {
        $this->encoder = $encoder;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getEmailAddress()
    {
      return $this->emailAddress;
    }

    public function setEmailAddress($value)
    {
      $this->emailAddress = $value;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getRealName()
    {
        return $this->realName;
    }

    public function setRealName($realName)
    {
        $this->realName = $realName;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRoles()
    {
        return $this->roles ?: "ROLE_USER";
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getEncryptedPassword()
    {
        $encoded = NULL;
        if ( !empty($this->getPassword()) ) {
            $salt = base64_encode(random_bytes(40));
            $encoded = $this->encoder->encodePassword($this->getPassword(), $salt);
        }
        return $encoded;
    }
}
