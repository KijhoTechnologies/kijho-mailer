<?php
namespace Kijho\MailerBundle\Model;

/**
 */
interface TemplateGroupInterface
{
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @return string
     */
    public function getSlug();
    
    /**
     * @return \DateTime
     */
    public function getCreationDate();
    
    /**
     * @return TemplateGroup
     */
    public function getTemplateGroup();

}
