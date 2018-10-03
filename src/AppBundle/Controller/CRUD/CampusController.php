<?php

namespace AppBundle\Controller\CRUD;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Campus;
use AppBundle\Form\CampusType;

/**
 * Campus controller.
 *
 */
class CampusController extends Controller
{

    /**
     * Lists all Campus entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Campus')->findAll();

        return $this->render('AppBundle:Campus:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Campus entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Campus();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('campus_show', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:Campus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Campus entity.
     *
     * @param Campus $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Campus $entity)
    {
        $form = $this->createForm(new CampusType(), $entity, array(
            'action' => $this->generateUrl('campus_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Campus entity.
     *
     */
    public function newAction()
    {
        $entity = new Campus();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:Campus:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Campus entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Campus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Campus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Campus:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Campus entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Campus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Campus entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Campus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Campus entity.
    *
    * @param Campus $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Campus $entity)
    {
        $form = $this->createForm(new CampusType(), $entity, array(
            'action' => $this->generateUrl('campus_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Campus entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Campus')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Campus entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('campus_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:Campus:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Campus entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Campus')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Campus entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('campus'));
    }

    /**
     * Creates a form to delete a Campus entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('campus_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
