<?php

namespace Yoda\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Serializable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="yoda_user")
 * @ORM\Entity(repositoryClass="Yoda\UserBundle\Repository\UserRepository")
 * @UniqueEntity(fields="username", message="That username is taken!")
 * @UniqueEntity(fields="email", message="That email is taken!")
 */
class User implements AdvancedUserInterface, Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     * @Assert\NotBlank(message="Put in a username you rebel scum :P")
     * @Assert\Length(min=3, minMessage="Give us at least 3 characters!")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @Assert\NotBlank
     * @Assert\Regex(
     *      pattern="/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/",
     *      message="Use 1 upper case letter, 1 lower case letter, and 1 number"
     * )
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="Yoda\EventBundle\Entity\Event", mappedBy="owner")
     */
    protected $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get plain password
     *
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set plain password
     *
     * @param $plainPassword
     * @return $this
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        //$this->setPassword(null);
        return $this;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        // allows for chaining
        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Get salt
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     *
     */
    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }



    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->getIsActive();
        //return true;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized);
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }
}
