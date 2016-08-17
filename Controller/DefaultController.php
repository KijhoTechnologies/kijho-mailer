<?php

namespace Kijho\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    public function homeAction() {
        return $this->render('KijhoMailerBundle:Default:home.html.twig', array('menu' => 'home'));
    }

    /**
     * Esta funcion sirve para redireccionar al idioma por defecto en caso de que no 
     * se establezca el locale en la url introducida por el usuario
     * @return type
     */
    public function defaultIndexAction(Request $request) {
        return $this->redirect($this->generateUrl('kijho_mailer_homepage', array('_locale' => $request->getLocale())));
    }

}
