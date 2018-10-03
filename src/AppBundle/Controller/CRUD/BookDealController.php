<?php

namespace AppBundle\Controller\CRUD;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\BookDeal;
use AppBundle\Form\BookDealType;

/**
 * BookDeal controller.
 *
 */
class BookDealController extends Controller
{

    /**
     * Lists all BookDeal entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:BookDeal')->findAll();

        return $this->render('AppBundle:BookDeal:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new BookDeal entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BookDeal();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bookdeal_show', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:BookDeal:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BookDeal entity.
     *
     * @param BookDeal $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BookDeal $entity)
    {
        $form = $this->createForm(new BookDealType(), $entity, array(
            'action' => $this->generateUrl('bookdeal_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BookDeal entity.
     *
     */
    public function newAction()
    {
        $entity = new BookDeal();
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:BookDeal:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BookDeal entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:BookDeal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BookDeal entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:BookDeal:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BookDeal entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:BookDeal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BookDeal entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:BookDeal:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BookDeal entity.
    *
    * @param BookDeal $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BookDeal $entity)
    {
        $form = $this->createForm(new BookDealType(), $entity, array(
            'action' => $this->generateUrl('bookdeal_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BookDeal entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:BookDeal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BookDeal entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('bookdeal_edit', array('id' => $id)));
        }

        return $this->render('AppBundle:BookDeal:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BookDeal entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:BookDeal')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BookDeal entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('bookdeal'));
    }

    /**
     * Creates a form to delete a BookDeal entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bookdeal_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
