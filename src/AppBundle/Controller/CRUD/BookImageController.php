<?php

namespace AppBundle\Controller\CRUD;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\BookImage;
use AppBundle\Form\BookImageType;

/**
 * BookImage controller.
 *
 */
class BookImageController extends Controller
{

    /**
     * Lists all BookImage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:BookImage')->findAll();

        return $this->render('AppBundle:BookImage:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new BookImage entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BookImage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bookimage_show', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:BookImage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BookImage entity.
     *
     * @param BookImage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BookImage $entity)
    {
        $form = $this->createForm(new BookImageType(), $entity, array(
            'action' => $this->generateUrl('bookimage_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BookImage entity.
     *
     */
    public function newAction()
    {
        $entity = new BookImage();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:BookImage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BookImage entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:BookImage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BookImage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:BookImage:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BookImage entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:BookImage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BookImage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:BookImage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BookImage entity.
    *
    * @param BookImage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BookImage $entity)
    {
        $form = $this->createForm(new BookImageType(), $entity, array(
            'action' => $this->generateUrl('bookimage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BookImage entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:BookImage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BookImage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('bookimage_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:BookImage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BookImage entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:BookImage')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BookImage entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('bookimage'));
    }

    /**
     * Creates a form to delete a BookImage entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bookimage_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
