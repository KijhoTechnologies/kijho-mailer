<?php

namespace Kijho\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\MailerBundle\Form\EmailLayoutType;
use Symfony\Component\HttpFoundation\Request;
use Kijho\MailerBundle\Util\Util;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class LayoutController extends Controller {

    /**
     * Permite visualizar el listado de todos los layouts creados en el sistema
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 06/11/2015
     * @return type
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $layoutStorage = $this->container->getParameter('kijho_mailer.storage')['layout'];

        $layouts = $em->getRepository($layoutStorage)->findAll();

        return $this->render('KijhoMailerBundle:Layout:index.html.twig', array(
                    'layouts' => $layouts,
                    'menu' => 'layouts'
        ));
    }

    /**
     * Permite desplegar el formulario de creacion de layouts
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 10/11/2015
     * @return Response formulario de creacion del layout
     */
    public function newAction() {

        $layoutStorage = $this->container->getParameter('kijho_mailer.storage')['layout'];
        $layout = new $layoutStorage;

        $form = $this->createForm(EmailLayoutType::class, $layout, array(
            'translator' => $this->get('translator')
        ));

        return $this->render('KijhoMailerBundle:Layout:new.html.twig', array(
                    'layout' => $layout,
                    'form' => $form->createView(),
                    'menu' => 'layouts'
        ));
    }

    /**
     * Permite validar y almacenar la informacion de un layout
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 10/11/2015
     * @param Request $request datos de la solicitud
     * @return Response en caso de exito redirecciona al listado de layouts, 
     * en caso de error despliega nuevamente el formulario de creacion de layouts
     */
    public function saveAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $layoutStorage = $this->container->getParameter('kijho_mailer.storage')['layout'];
        $layout = new $layoutStorage;
        $form = $this->createForm(EmailLayoutType::class, $layout, array(
            'translator' => $this->get('translator')
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $layout->setCreationDate(Util::getCurrentDate());
            $em->persist($layout);
            $em->flush();

            $this->get('session')->getFlashBag()->add('messageSuccessLayout', $this->get('translator')->trans('kijho_mailer.layout.creation_success_message'));
            return $this->redirect($this->generateUrl('kijho_mailer_layout'));
        }

        return $this->render('KijhoMailerBundle:Layout:new.html.twig', array(
                    'layout' => $layout,
                    'form' => $form->createView(),
                    'menu' => 'layouts'
        ));
    }

    /**
     * Permite desplegar el formulario de edicion de layouts
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 10/11/2015
     * @param integer $layoutId identificador del layout a editar
     * @return Response formulario de edicion del layout
     */
    public function editAction($layoutId) {

        $em = $this->getDoctrine()->getManager();

        $layoutStorage = $this->container->getParameter('kijho_mailer.storage')['layout'];

        $layout = $em->getRepository($layoutStorage)->find($layoutId);

        $form = $this->createForm(EmailLayoutType::class, $layout, array(
            'translator' => $this->get('translator')
        ));

        return $this->render('KijhoMailerBundle:Layout:edit.html.twig', array(
                    'layout' => $layout,
                    'form' => $form->createView(),
                    'menu' => 'layouts'
        ));
    }

    /**
     * Permite validar y almacenar los cambios en la informacion de un layout
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 10/11/2015
     * @param Request $request datos de la solicitud
     * @param integer $layoutId identificador del layout a editar
     * @return Response en caso de exito redirecciona al listado de layouts, 
     * en caso de error despliega nuevamente el formulario de creacion de layouts
     */
    public function updateAction(Request $request, $layoutId) {
        $em = $this->getDoctrine()->getManager();

        $layoutStorage = $this->container->getParameter('kijho_mailer.storage')['layout'];
        $layout = $em->getRepository($layoutStorage)->find($layoutId);
        $form = $this->createForm(EmailLayoutType::class, $layout, array(
            'translator' => $this->get('translator')
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($layout);
            $em->flush();

            $this->get('session')->getFlashBag()->add('messageSuccessLayout', $this->get('translator')->trans('kijho_mailer.layout.update_success_message'));
            return $this->redirect($this->generateUrl('kijho_mailer_layout'));
        }

        return $this->render('KijhoMailerBundle:Layout:edit.html.twig', array(
                    'layout' => $layout,
                    'form' => $form->createView(),
                    'menu' => 'layouts'
        ));
    }

    /**
     * Permite desplegar el preview de un layout
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 10/11/2015
     * @param integer $layoutId identificador del layout
     * @return Response pagina con el preview del layout
     */
    public function previewAction($layoutId) {

        $em = $this->getDoctrine()->getManager();

        $layoutStorage = $this->container->getParameter('kijho_mailer.storage')['layout'];

        $layout = $em->getRepository($layoutStorage)->find($layoutId);


        return $this->render('KijhoMailerBundle:Layout:preview.html.twig', array(
                    'layout' => $layout,
                    'menu' => 'layouts'
        ));
    }

    /**
     * Permite eliminar un layout del sistema
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 10/11/2015
     * @param Request $request datos de la solicitud
     * @return Response Json con mensaje de respuesta
     */
    public function deleteAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $layoutId = $request->request->get('layoutId');
        $layoutStorage = $this->container->getParameter('kijho_mailer.storage')['layout'];
        $layout = $em->getRepository($layoutStorage)->find($layoutId);

        $response['result'] = '__OK__';
        $response['msg'] = '';

        try {
            $em->remove($layout);
            $em->flush();
        } catch (\Exception $exc) {
            $response['result'] = '__KO__';
            $response['msg'] = $this->get('translator')->trans('kijho_mailer.global.cant_delete_message');
        }
        return new JsonResponse($response);
    }

}
