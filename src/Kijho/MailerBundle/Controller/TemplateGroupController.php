<?php

namespace Kijho\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\MailerBundle\Form\EmailTemplateGroupType;
use Symfony\Component\HttpFoundation\Request;
use Kijho\MailerBundle\Util\Util;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class TemplateGroupController extends Controller {

    /**
     * Permite visualizar el listado de todos los grupos de templates creados en el sistema
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 12/11/2015
     * @return type
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $templateGroupStorage = $this->container->getParameter('kijho_mailer.storage')['template_group'];

        $templateGroups = $em->getRepository($templateGroupStorage)->findAll();

        return $this->render('KijhoMailerBundle:TemplateGroup:index.html.twig', array(
                    'templateGroups' => $templateGroups,
                    'menu' => 'templateGroups'
        ));
    }

    /**
     * Permite desplegar el formulario de creacion de template groups
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 12/11/2015
     * @return Response formulario de creacion del template group
     */
    public function newAction() {

        $templateGroupStorage = $this->container->getParameter('kijho_mailer.storage')['template_group'];
        $templateGroup = new $templateGroupStorage;

        $form = $this->createForm(EmailTemplateGroupType::class, $templateGroup, array(
            'translator' => $this->get('translator')
        ));

        return $this->render('KijhoMailerBundle:TemplateGroup:new.html.twig', array(
                    'templateGroup' => $templateGroup,
                    'form' => $form->createView(),
                    'menu' => 'templateGroups'
        ));
    }

    /**
     * Permite validar y almacenar la informacion de un template group
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 12/11/2015
     * @param Request $request datos de la solicitud
     * @return Response en caso de exito redirecciona al listado de template groups, 
     * en caso de error despliega nuevamente el formulario de creacion de template groups
     */
    public function saveAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $templateGroupStorage = $this->container->getParameter('kijho_mailer.storage')['template_group'];
        $templateGroup = new $templateGroupStorage;
        $form = $this->createForm(EmailTemplateGroupType::class, $templateGroup, array(
            'translator' => $this->get('translator')
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $templateGroup->setCreationDate(Util::getCurrentDate());
            $em->persist($templateGroup);
            $em->flush();

            //Seteamos el slug del grupo
            $slug = trim(strtolower(str_replace(' ', '_', $templateGroup->getName()))) . "_" . $templateGroup->getId();
            $templateGroup->setSlug($slug);
            $em->persist($templateGroup);
            $em->flush();

            $this->get('session')->getFlashBag()->add('messageSuccessTemplateGroup', $this->get('translator')->trans('kijho_mailer.template_group.creation_success_message'));
            return $this->redirect($this->generateUrl('kijho_mailer_template_group'));
        }

        return $this->render('KijhoMailerBundle:TemplateGroup:new.html.twig', array(
                    'templateGroup' => $templateGroup,
                    'form' => $form->createView(),
                    'menu' => 'templateGroups'
        ));
    }

    /**
     * Permite desplegar el formulario de edicion de template groups
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 12/11/2015
     * @param integer $templateGroupId identificador del template group a editar
     * @return Response formulario de edicion del template group
     */
    public function editAction($templateGroupId) {

        $em = $this->getDoctrine()->getManager();

        $templateGroupStorage = $this->container->getParameter('kijho_mailer.storage')['template_group'];

        $templateGroup = $em->getRepository($templateGroupStorage)->find($templateGroupId);

        $form = $this->createForm(EmailTemplateGroupType::class, $templateGroup, array(
            'translator' => $this->get('translator')
        ));

        return $this->render('KijhoMailerBundle:TemplateGroup:edit.html.twig', array(
                    'templateGroup' => $templateGroup,
                    'form' => $form->createView(),
                    'menu' => 'templateGroups'
        ));
    }

    /**
     * Permite validar y almacenar los cambios en la informacion de un template group
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 12/11/2015
     * @param Request $request datos de la solicitud
     * @param integer $templateGroupId identificador del template group a editar
     * @return Response en caso de exito redirecciona al listado de template groups, 
     * en caso de error despliega nuevamente el formulario de creacion de template groups
     */
    public function updateAction(Request $request, $templateGroupId) {
        $em = $this->getDoctrine()->getManager();

        $templateGroupStorage = $this->container->getParameter('kijho_mailer.storage')['template_group'];
        $templateGroup = $em->getRepository($templateGroupStorage)->find($templateGroupId);
        $form = $this->createForm(EmailTemplateGroupType::class, $templateGroup, array(
            'translator' => $this->get('translator')
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($templateGroup);
            $em->flush();

            $this->get('session')->getFlashBag()->add('messageSuccessTemplateGroup', $this->get('translator')->trans('kijho_mailer.template_group.update_success_message'));
            return $this->redirect($this->generateUrl('kijho_mailer_template_group'));
        }

        return $this->render('KijhoMailerBundle:TemplateGroup:edit.html.twig', array(
                    'templateGroup' => $templateGroup,
                    'form' => $form->createView(),
                    'menu' => 'templateGroups'
        ));
    }

    /**
     * Permite eliminar un template group del sistema
     * @author Cesar Giraldo - Kijho Technologies <cnaranjo@kijho.com> 12/11/2015
     * @param Request $request datos de la solicitud
     * @return Response Json con mensaje de respuesta
     */
    public function deleteAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $templateGroupId = $request->request->get('templateGroupId');
        $templateGroupStorage = $this->container->getParameter('kijho_mailer.storage')['template_group'];
        $templateGroup = $em->getRepository($templateGroupStorage)->find($templateGroupId);

        $response['result'] = '__OK__';
        $response['msg'] = '';

        try {
            $em->remove($templateGroup);
            $em->flush();
        } catch (\Exception $exc) {
            $response['result'] = '__KO__';
            $response['msg'] = $this->get('translator')->trans('kijho_mailer.global.cant_delete_message');
        }
        return new JsonResponse($response);
    }

}
