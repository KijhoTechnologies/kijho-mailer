<?php

namespace Kijho\MailerBundle\Entity;

use Kijho\MailerBundle\Model\Template as BaseTemplate;
use Doctrine\ORM\Mapping as ORM;

/**
 * Email Template
 * @ORM\Table(name="email_template")
 * @ORM\Entity
 */
class EmailTemplate extends BaseTemplate {

    /**
     * @ORM\Id
     * @ORM\Column(name="temp_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * Layout al que esta asociado el template
     * @ORM\ManyToOne(targetEntity="Kijho\MailerBundle\Entity\EmailLayout")
     * @ORM\JoinColumn(name="temp_layout", referencedColumnName="layo_id", nullable=true)
     */
    protected $layout;
    
    /**
     * Grupo al que esta asociado el template
     * @ORM\ManyToOne(targetEntity="Kijho\MailerBundle\Entity\EmailTemplateGroup")
     * @ORM\JoinColumn(name="temp_group", referencedColumnName="tgro_id", nullable=true)
     */
    protected $group;
    
    function getId() {
        return $this->id;
    }
    
    function getLayout() {
        return $this->layout;
    }

    function setLayout($layout = null) {
        $this->layout = $layout;
    }
    
    function getGroup() {
        return $this->group;
    }

    function setGroup($group = null) {
        $this->group = $group;
    }



}
