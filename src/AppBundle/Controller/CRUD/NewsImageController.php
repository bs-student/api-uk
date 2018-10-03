<?php

namespace AppBundle\Controller\CRUD;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\NewsImage;
use AppBundle\Form\NewsImageType;

/**
 * NewsImage controller.
 *
 */
class NewsImageController extends Controller
{

    /**
     * Lists all NewsImage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:NewsImage')->findAll();

        return $this->render('AppBundle:NewsImage:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new NewsImage entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new NewsImage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('newsimage_show', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:NewsImage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a NewsImage entity.
     *
     * @param NewsImage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(NewsImage $entity)
    {
        $form = $this->createForm(new NewsImageType(), $entity, array(
            'action' => $this->generateUrl('newsimage_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new NewsImage entity.
     *
     */
    public function newAction()
    {
        $entity = new NewsImage();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:NewsImage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a NewsImage entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:NewsImage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsImage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:NewsImage:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NewsImage entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:NewsImage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsImage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:NewsImage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a NewsImage entity.
    *
    * @param NewsImage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(NewsImage $entity)
    {
        $form = $this->createForm(new NewsImageType(), $entity, array(
            'action' => $this->generateUrl('newsimage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing NewsImage entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:NewsImage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsImage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('newsimage_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:NewsImage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a NewsImage entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:NewsImage')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find NewsImage entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('newsimage'));
    }

    /**
     * Creates a form to delete a NewsImage entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('newsimage_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
