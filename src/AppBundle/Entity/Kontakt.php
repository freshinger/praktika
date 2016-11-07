<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kontakt
 */
class Kontakt
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Teilnehmer
     */
    private $teilnehmer;

    /**
     * @var \Ansprechpartner
     */
    private $ansprechpartner;

    /**
     * @var \Korrespondenz
     */
    private $korrespondenz;


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
     * @return Kontakt
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

    /**
     * Set ansprechpartner
     *
     * @param \Ansprechpartner $ansprechpartner
     * @return Kontakt
     */
    public function setAnsprechpartner(\Ansprechpartner $ansprechpartner = null)
    {
        $this->ansprechpartner = $ansprechpartner;

        return $this;
    }

    /**
     * Get ansprechpartner
     *
     * @return \Ansprechpartner 
     */
    public function getAnsprechpartner()
    {
        return $this->ansprechpartner;
    }

    /**
     * Set korrespondenz
     *
     * @param \Korrespondenz $korrespondenz
     * @return Kontakt
     */
    public function setKorrespondenz(\Korrespondenz $korrespondenz = null)
    {
        $this->korrespondenz = $korrespondenz;

        return $this;
    }

    /**
     * Get korrespondenz
     *
     * @return \Korrespondenz 
     */
    public function getKorrespondenz()
    {
        return $this->korrespondenz;
    }
}
