<?php
namespace Kijho\MailerBundle\Model;

/**
 */
interface TemplateInterface
{
    /**
     * @return string
     */
    public function getName();
    
    /**
     * @return integer
     */
    public function getStatus();
    
    /**
     * @return string
     */
    public function getStatusDescription($status = null);

    /**
     * @return Layout
     */
    public function getLayout();
    
    /**
     * @return TemplateGroup
     */
    public function getGroup();

    /**
     * @return string
     */
    public function getRecipientName();
    
    /**
     * @return string
     */
    public function getFromName();
    
    /**
     * @return string
     */
    public function getFromMail();
    
    /**
     * @return string
     */
    public function getCopyTo();
    
    /**
     * @return string
     */
    public function getSubject();
    
    /**
     * @return string
     */
    public function getContentMessage();
    
    /**
     * @return \DateTime
     */
    public function getCreationDate();
    
    
    /**
     * @return string
     */
    public function getMailerSettings();
    
    /**
     * @return string
     */
    public function getEntityName();
    
    /**
     * @return string
     */
    public function getLanguageCode();
    
    /**
     * @return string
     */
    public function getSlug();

}
