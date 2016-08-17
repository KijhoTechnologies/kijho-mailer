<?php

namespace Kijho\MailerBundle\Model;

/**
 */
interface SettingsInterface {

    /**
     * @return integer
     */
    public function getSendMode();
    
    /**
     * @return integer
     */
    public function getLimitEmailAmount();
    
    /**
     * @return integer
     */
    public function getIntervalToSend();
    
    /**
     * @return string
     */
    public function getSendModeDescription($sendMode = null);
}
