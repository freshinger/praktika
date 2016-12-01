<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Praktikum
 */
class Praktikum
{
    /**
     * @var \DateTime
     */
    private $startdatum;

    /**
     * @var \DateTime
     */
    private $enddatum;

    /**
     * @var string
     */
    private $beruf;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Teilnehmer
     */
    private $user;

    /**
     * @var \Firma
     */
    private $firma;


    /**
     * Set startdatum
     *
     * @param \DateTime $startdatum
     * @return Praktikum
     */
    public function setStartdatum($startdatum)
    {
        $this->startdatum = $startdatum;

        return $this;
    }

    /**
     * Get startdatum
     *
     * @return \DateTime
     */
    public function getStartdatum()
    {
        return $this->startdatum;
    }

    /**
     * Set enddatum
     *
     * @param \DateTime $enddatum
     * @return Praktikum
     */
    public function setEnddatum($enddatum)
    {
        $this->enddatum = $enddatum;

        return $this;
    }

    /**
     * Get enddatum
     *
     * @return \DateTime
     */
    public function getEnddatum()
    {
        return $this->enddatum;
    }

    /**
     * Set beruf
     *
     * @param string $beruf
     * @return Praktikum
     */
    public function setBeruf($beruf)
    {
        $this->beruf = $beruf;

        return $this;
    }

    /**
     * Get beruf
     *
     * @return string 
     */
    public function getBeruf()
    {
        return $this->beruf;
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
     * @return Praktikum
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set firma
     *
     * @param \Firma $firma
     * @return Praktikum
     */
    public function setFirma(\AppBundle\Entity\Firma $firma = null)
    {
        $this->firma = $firma;

        return $this;
    }

    /**
     * Get firma
     *
     * @return \Firma 
     */
    public function getFirma()
    {
        return $this->firma;
    }
}