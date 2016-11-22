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
     * @var \User
     */
    private $user;

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
     * Set user
     *
     * @param \User $user
     * @return Kontakt
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
     * Set ansprechpartner
     *
     * @param \Ansprechpartner $ansprechpartner
     * @return Kontakt
     */
    public function setAnsprechpartner(\AppBundle\Entity\Ansprechpartner $ansprechpartner = null)
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
    public function setKorrespondenz(\AppBundle\Entity\Korrespondenz $korrespondenz = null)
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
