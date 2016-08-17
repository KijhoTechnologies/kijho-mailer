<?php

namespace Kijho\MailerBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class TemplateGroup implements TemplateGroupInterface {

    /**
     * @var string
     * @ORM\Column(name="tgro_name", type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="tgro_slug", type="string", nullable=true)
     */
    protected $slug;

    /**
     * @var \DateTime
     * @ORM\Column(name="tgro_creation_date", type="datetime", nullable=true)
     */
    protected $creationDate;

    /**
     * @var boolean
     * @ORM\Column(name="tgro_is_default", type="boolean", nullable=true)
     */
    protected $isDefault;

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

    public function getSlug() {
        return $this->slug;
    }

    function setSlug($slug) {
        $this->slug = $slug;
    }

    public function getTemplateGroup() {
        
    }

}
