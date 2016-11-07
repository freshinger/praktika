<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Firma
 */
class Firma
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $city;

    /**
     * @var integer
     */
    private $postcode;

    /**
     * @var string
     */
    private $website;

    /**
     * @var string
     */
    private $description;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ansprechpartner;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ansprechpartner = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Firma
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Firma
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Firma
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postcode
     *
     * @param integer $postcode
     * @return Firma
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return integer 
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Firma
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Firma
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
     * Add ansprechpartner
     *
     * @param \Ansprechpartner $ansprechpartner
     * @return Firma
     */
    public function addAnsprechpartner(\Ansprechpartner $ansprechpartner)
    {
        $this->ansprechpartner[] = $ansprechpartner;

        return $this;
    }

    /**
     * Remove ansprechpartner
     *
     * @param \Ansprechpartner $ansprechpartner
     */
    public function removeAnsprechpartner(\Ansprechpartner $ansprechpartner)
    {
        $this->ansprechpartner->removeElement($ansprechpartner);
    }

    /**
     * Get ansprechpartner
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnsprechpartner()
    {
        return $this->ansprechpartner;
    }
}
