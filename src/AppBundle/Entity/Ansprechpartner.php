<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ansprechpartner
 */
class Ansprechpartner
{
    /**
     * @var string
     */
    private $prename;
    
    /**
     * @var string
     */
    private $surname;
    /**
     * @var string
     */
    private $position;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Firma
     */
    private $firma;


    /**
     * Set Prename
     *
     * @param string $name
     * @return Ansprechpartner
     */
    public function setPrename($name)
    {
        $this->prename = $name;

        return $this;
    }

    /**
     * Get Prename
     *
     * @return string 
     */
    public function getPrename()
    {
        return $this->prename;
    }
    
    /**
     * Set Surname
     *
     * @param string $name
     * @return Ansprechpartner
     */
    public function setSurname($name)
    {
        $this->surname = $name;

        return $this;
    }

    /**
     * Get Surname
     *
     * @return string 
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return Ansprechpartner
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Ansprechpartner
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Ansprechpartner
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

    
    public function setFirma(\Appbundle\Entity\Firma $firma)
    {
        $this->firma = $firma;

        return $this;
    }

    
    public function getFirma()
    {
        return $this->firma;
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
}