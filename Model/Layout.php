<?php

namespace Kijho\MailerBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Layout implements LayoutInterface {

    /**
     * @var string
     * @ORM\Column(name="layo_name", type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="layo_header", type="text")
     * @Assert\NotBlank()
     */
    protected $header;

    /**
     * @var string
     * @ORM\Column(name="layo_footer", type="text")
     * @Assert\NotBlank()
     */
    protected $footer;

    /**
     * @var \DateTime
     * @ORM\Column(name="layo_creation_date", type="datetime", nullable=true)
     */
    protected $creationDate;

    /**
     * @var boolean
     * @ORM\Column(name="layo_is_default", type="boolean", nullable=true)
     */
    protected $isDefault;

    /**
     * {@inheritDoc}
     */
    public function getFooter() {
        return $this->footer;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * {@inheritDoc}
     */
    public function getName() {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    function getIsDefault() {
        return $this->isDefault;
    }

    /**
     * @param boolean $isDefault
     */
    function setIsDefault($isDefault) {
        $this->isDefault = $isDefault;
    }

    /**
     * 
     * @param string $header
     */
    function setHeader($header) {
        $this->header = $header;
    }

    /**
     * 
     * @param string $footer
     */
    function setFooter($footer) {
        $this->footer = $footer;
    }

    /**
     * @param string $name
     */
    function setName($name) {
        $this->name = $name;
    }

    /**
     * 
     * @param \DateTime $creationDate
     */
    function setCreationDate(\DateTime $creationDate) {
        $this->creationDate = $creationDate;
    }

    public function __toString() {
        return $this->getName();
    }

}
