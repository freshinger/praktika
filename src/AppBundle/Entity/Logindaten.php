<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email wird bereits verwendet")
 */
class Logindaten
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max = 4096)
     */
    private $password;

    /**
     * @var boolean
     */
    private $adminrights = "false";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Set password
     *
     * @param string $password
     * @return Logindaten
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
     * Set adminrights
     *
     * @param boolean $adminrights
     * @return Logindaten
     */
    public function setAdminrights($adminrights)
    {
        $this->adminrights = $adminrights;

        return $this;
    }

    /**
     * Get adminrights
     *
     * @return boolean 
     */
    public function getAdminrights()
    {
        return $this->adminrights;
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
     * Set teilnehmer
     *
     * @param \Teilnehmer $teilnehmer
     * @return Logindaten
     */
    public function setTeilnehmer(\Teilnehmer $teilnehmer = null)
    {
        $this->teilnehmer = $teilnehmer;

        return $this;
    }

    /**
     * Get teilnehmer
     *
     * @return \Teilnehmer 
     */
    public function getTeilnehmer()
    {
        return $this->teilnehmer;
    }
}
