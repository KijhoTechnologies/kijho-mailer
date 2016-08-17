<?php

namespace Kijho\MailerBundle\Util;

/**
 * Description of Util
 *
 * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 10/11/2015
 */
class Util {

    public static function getCurrentDate($zone = 'America/Bogota') {
        $timezone = new \DateTimeZone($zone);
        $datetime = new \DateTime('now');
        $datetime->setTimezone($timezone);
        return $datetime;
    }
    
    /**
     * Permite encontrar el trozo de texto que esta entre dos palabras o caracteres
     * indicados
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 17/11/2015
     * @param string $content texto a analizar
     * @param string $start deliminador de inicio
     * @param string $end delimitador final
     * @return string texto encontrado entre los dos delimitadores
     */
    public static function getBetween($content, $start, $end) {
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);
            return $r[0];
        }
        return '';
    }
}

