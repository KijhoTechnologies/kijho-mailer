<?php

namespace Kijho\MailerBundle\Entity;

use Kijho\MailerBundle\Model\Email as BaseEmail;
use Doctrine\ORM\Mapping as ORM;

/**
 * Email
 * @ORM\Table(name="email")
 * @ORM\Entity
 */
class Email extends BaseEmail {

    /**
     * @ORM\Id
     * @ORM\Column(name="emai_id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * Template al que esta asociado el correo
     * @ORM\ManyToOne(targetEntity="Kijho\MailerBundle\Entity\EmailTemplate")
     * @ORM\JoinColumn(name="emai_template", referencedColumnName="temp_id")
     */
    protected $template;
    
    function getId() {
        return $this->id;
    }
    
    function getTemplate() {
        return $this->template;
    }

    function setTemplate($template = null) {
        $this->template = $template;
    }
}
