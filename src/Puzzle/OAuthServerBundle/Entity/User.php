<?php

namespace Puzzle\OAuthServerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
                                
  /**
  * User
  *
  * @ORM\Table(name="user")
  * @ORM\Entity(repositoryClass="Puzzle\OAuthServerBundle\Repository\UserRepository")
  * @JMS\ExclusionPolicy("all")
  * @JMS\XmlRoot("user")
  * @Hateoas\Relation(
  * 		name = "self", 
  * 		href = @Hateoas\Route(
  * 			"get_user", 
  * 			parameters = {"id" = "expr(object.getId())"},
  * 			absolute = true,
  * ))
  */
  class User implements AdvancedUserInterface, \Serializable, EquatableInterface
  {
    
     const ROLE_DEFAULT = 'ROLE_USER';
     
     /**
      * @var string
      *
      * @ORM\Column(name="id", type="string")
      * @ORM\Id
      * @ORM\GeneratedValue(strategy="CUSTOM")
      * @ORM\CustomIdGenerator(class="Puzzle\OAuthServerBundle\Service\IdGenerator")
      * @JMS\Expose
  	  * @JMS\Type("string")
  	  * @JMS\XmlAttribute
      */
     protected $id;
     
      /**
       * @ORM\Column(name="first_name", type="string", length=25)
       * @JMS\Expose
	   * @JMS\Type("string")
       */
      private $firstName;
      
      /**
       * @ORM\Column(name="last_name", type="string", length=25)
       * @JMS\Expose
	   * @JMS\Type("string")
       */
      private $lastName;
      
      /**
       * @ORM\Column(name="gender", type="string", length=25, nullable=true)
       * @JMS\Expose
	   * @JMS\Type("string") 
       */
      private $gender;
      
      /**
       * @ORM\Column(name="phone", type="string", length=255, nullable=true)
       * @JMS\Expose
	   * @JMS\Type("string")
       */
      private $phone;

      /**
      * @ORM\Column(type="string", length=255, unique=true)
      * @JMS\Expose
	  * @JMS\Type("string")
      */
      private $username;

      /**
      * @ORM\Column(type="string", length=255, unique=true)
      * @JMS\Expose
	  * @JMS\Type("string")
      */
      private $email;
      
      /**
      * @ORM\Column(type="string", length=255, nullable=true)
      */
      private $salt;

      /**
      * @ORM\Column(type="string", length=255, nullable=true)
      */
      private $password;
      
      /**
       * @Assert\Length(min=8, max=4096, minMessage="user.password.short", maxMessage="user.password.long", groups={"Create", "Update", "ChangePassword", "ResetPassword"})
       * @var string $plainPassword
       */
      protected $plainPassword;
     
      /**
       * @ORM\Column(type="boolean")
       * @var boolean $enabled
       * @JMS\Expose
	   * @JMS\Type("boolean")
       */
      protected $enabled;
      
      /**
       * @ORM\Column(type="boolean")
       * @var boolean $locked
       * @JMS\Expose
	   * @JMS\Type("boolean")
       */
      protected $locked;
      
      /**
       * @ORM\Column(name="account_expires_at", type="datetime", nullable=true)
       * @var \DateTime $accountExpiresAt
       * @JMS\Expose
	   * @JMS\Type("DateTime<'Y-m-d H:i'>")
       */
      protected $accountExpiresAt;
      
      /**
       * @ORM\Column(name="credentials_expires_at", type="datetime", nullable=true)
       * @var \DateTime $credentialsExpiresAt
       * @JMS\Expose
	   * @JMS\Type("DateTime<'Y-m-d H:i'>")
       */
      protected $credentialsExpiresAt;
      
      /**
       * @ORM\Column(name="confirmation_token", type="string", nullable=true)
       * @var string $confirmationToken
       * @JMS\Expose
	   * @JMS\Type("string")
       */
      protected $confirmationToken;
      
      /**
       * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
       * @var \DateTime $passwordRequestedAt
       * @JMS\Expose
	   * @JMS\Type("DateTime<'Y-m-d H:i'>")
       */
      protected $passwordRequestedAt;
      
      /**
       * @ORM\Column(name="password_changed", type="boolean")
       * @var boolean $passwordChanged
       * @JMS\Expose
	   * @JMS\Type("boolean")
       */
      protected $passwordChanged;
      
      /**
       * @ORM\Column(name="roles", type="array")
       * @var array
       * @JMS\Expose
	   * @JMS\Type("array")
       */
      private $roles = array();
      
      public function __construct() {
          $this->roles = [self::ROLE_DEFAULT];
          $this->enabled = true;
          $this->locked = false;
          $this->passwordChanged = false;
      }
      
      public function getId() {
          return $this->id;
      }
      
      /**
      * @inheritDoc
      */
      public function getUsername() {
          return $this->username;
      }

      /**
      * @inheritDoc
      */
      public function setUsername($username) {
          $this->username = $username;
          return $this;
      }

      /**
      * @inheritDoc
      */
      public function getSalt() {
          return $this->salt;
      }

      public function setSalt($salt) {
          $this->salt = $salt;
          return $this;
      }

      
      public function hasRole(string $role) :bool {
          return in_array(strtoupper($role), $this->roles, true);
      }
      
      public function addRole(string $role) :self {
          $role = strtoupper($role);
          
          if (false === in_array($role, $this->roles, true)) {
              $this->roles[] = $role;
          }
          
          return $this;
      }
      
      public function setRoles(array $roles) :self {
          foreach ($roles as $role) {
              $this->addRole($role);
          }
          return $this;
      }
      
      public function removeRole(string $role) :self {
          $role = strtoupper($role);
          
          if ($role !== static::ROLE_DEFAULT) {
              if (false !== ($key = array_search($role, $this->roles, true))) {
                  unset($this->roles[$key]);
                  $this->roles = array_values($this->roles);
              }
          }
          
          return $this;
      }
      
      /**
      * @inheritDoc
      */
      public function getRoles() {
          return $this->roles;
      }

      /**
      * @inheritDoc
      */
      public function eraseCredentials() {}

      /**
      * @see \Serializable::serialize()
      */
      public function serialize() {
          return serialize([$this->id, $this->username, $this->password,]);
      }

      /**
      * @see \Serializable::unserialize()
      */
      public function unserialize($serialized) {
          list ($this->id, $this->username, $this->password,) = unserialize($serialized);
      }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
        return $this;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setGender($gender) {
        $this->gender = $gender;
        return $this;
    }

    public function getGender() {
        return $this->gender;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone() {
        return $this->phone;
    }
    
    public function getPlainPassword() {
        return $this->plainPassword;
    }
    
    public function setPlainPassword(string $plainPassword) :self {
        $this->plainPassword = $plainPassword;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }
    
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    public function getAccountExpiresAt() :?\DateTime {
        return $this->accountExpiresAt;
    }
    
    public function setAccountExpiresAt(\DateTime $expiresAt = null) :self {
        $this->accountExpiresAt = $expiresAt;
        return $this;
    }
    
    public function isAccountNonExpired() {
        return $this->accountExpiresAt instanceof \DateTime ?
        $this->accountExpiresAt->getTimestamp() >= time () : true;
    }
    
    public function getCredentialsExpiresAt() :?\DateTime {
        return $this->credentialsExpiresAt;
    }
    
    public function setCredentialsExpiresAt(\DateTime $expiresAt = null) :self {
        $this->credentialsExpiresAt = $expiresAt;
        return $this;
    }
    
    public function isCredentialsNonExpired() {
        return $this->credentialsExpiresAt instanceof \DateTime ?
        $this->credentialsExpiresAt->getTimestamp() >= time () : true;
    }
    
    public function setEnabled(bool $enabled) :self {
        $this->enabled = $enabled;
        return $this;
    }
    
    public function isEnabled() {
        return $this->enabled;
    }
    
    public function setLocked(bool $locked) :self {
        $this->locked = $locked;
        return $this;
    }
    
    public function isLocked() {
        return $this->locked;
    }
    
    public function isAccountNonLocked() {
        return !$this->locked;
    }
    
    public function getConfirmationToken() :?string {
        return $this->confirmationToken;
    }
    
    public function setConfirmationToken(string $confirmationToken = null) :?self {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }
    
    public function getPasswordRequestedAt() :?\DateTime {
        return $this->passwordRequestedAt;
    }
    
    public function setPasswordRequestedAt(\DateTime $passwordRequestedAt = null) :self {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }
    
    public function isPasswordRequestNonExpired(int $ttl) :bool {
        return $this->passwordRequestedAt instanceof \DateTime &&
        $this->passwordRequestedAt->getTimestamp() + $ttl > time();
    }
    
    public function setPasswordChanged(bool $passwordChanged) :self {
        $this->passwordChanged = $passwordChanged;
        return $this;
    }
    
    public function isPasswordChanged() {
        return $this->passwordChanged;
    }
    
    /**
     * @Assert\IsTrue(message="user.password.equal_username", groups={"Create", "Update", "ChangePassword", "ResetPassword"})
     * @return boolean
     */
    public function isPasswordEqualUsername() {
        if ($this->username === null) {
            return true;
        }
        
        return strtolower($this->username) !== strtolower($this->plainPassword);
    }
    
    /**
     * @Assert\IsTrue(message="user.password.equal_email", groups={"Create", "Update", "ChangePassword", "ResetPassword"})
     * @return boolean
     */
    public function isPasswordEqualEmail() {
        return strtolower($this->email) !== strtolower($this->plainPassword);
    }
    
    public function isEqualTo(UserInterface $user)
    {
        if ($this->password !== $user->getPassword()) {
            return false;
        }
        
        if ($this->salt !== $user->getSalt()) {
            return false;
        }
        
        if ($this->username !== $user->getUsername()) {
            return false;
        }
        
        return true;
    }
    
    public function getFullName(int $width = null) :?string {
        $fullName = $this->firstName ?: '';
        $fullName .= $this->lastName && $this->firstName ? ' '.$this->lastName : ($this->lastName ?: '');
        
        return $width && $fullName ? mb_strimwidth($fullName, 0, $width, '...') : $fullName;
    }
    
    public function __toString() {
        return $this->getFullName() ?: $this->username;
    }
}
