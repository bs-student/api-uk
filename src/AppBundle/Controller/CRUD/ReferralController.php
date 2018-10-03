<?php

namespace AppBundle\Controller\CRUD;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Referral;
use AppBundle\Form\ReferralType;

/**
 * Referral controller.
 *
 */
class ReferralController extends Controller
{

    /**
     * Lists all Referral entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Referral')->findAll();

        return $this->render('AppBundle:Referral:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Referral entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Referral();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('referral_show', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:Referral:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Referral entity.
     *
     * @param Referral $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Referral $entity)
    {
        $form = $this->createForm(new ReferralType(), $entity, array(
            'action' => $this->generateUrl('referral_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Referral entity.
     *
     */
    public function newAction()
    {
        $entity = new Referral();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:Referral:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Referral entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Referral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Referral entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Referral:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Referral entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Referral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Referral entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Referral:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Referral entity.
    *
    * @param Referral $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Referral $entity)
    {
        $form = $this->createForm(new ReferralType(), $entity, array(
            'action' => $this->generateUrl('referral_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Referral entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Referral')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Referral entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('referral_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:Referral:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Referral entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Referral')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Referral entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('referral'));
    }

    /**
     * Creates a form to delete a Referral entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('referral_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
