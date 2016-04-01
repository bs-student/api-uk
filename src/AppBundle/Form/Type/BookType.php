<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
class BookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bookTitle', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookDirectorAuthorArtist', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookEdition', 'text')
            ->add('bookIsbn10', 'text')
            ->add('bookIsbn13', 'text')

            ->add('bookPublisher', 'text')
            ->add('bookPublishDate','text', array(
                'constraints' => array(
                    new Date(),

                ),))
            ->add('bookBinding', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookPage','text')
            ->add('bookPriceSell', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookLanguage','text')
            ->add('bookDescription','text')
            ->add('bookCondition', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookIsHighlighted', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookHasNotes', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookComment', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookContactMethod', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookContactHomeNumber','text')
            ->add('bookContactCellNumber','text')
            ->add('bookContactEmail','text')
            ->add('bookIsAvailablePublic', 'text', array(
                'constraints' => array(
                    new NotBlank(),

                ),))
            ->add('bookPaymentMethodCaShOnExchange')
            ->add('bookPaymentMethodCheque')
            ->add('bookAvailableDate', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Date()
                ),))
            ->add('bookBuyer','entity',array(
                'class' => "AppBundle:User",
                ))
            ->add('bookSeller','entity',array(
                'class' => "AppBundle:User",
                'constraints' => array(
                    new NotBlank(),

                )))
        ->add('bookImages', 'collection', array(
                'type'         => new BookImageType(),
                'allow_add'    => true,
//                'allow_delete'    => true,
                'by_reference' =>false

            ));

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
    }


//    function onPreSetData(FormEvent $event) {
//        $data = $event->getData();
//        $data->setBookBinding('BOOKBINDING');
////        var_dump($data);
//        return $event->setData($data);
//    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_book';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Book',
            'csrf_protection' => false,
//            'validation_groups' => false,
            'allow_extra_fields' => true,
//            'error_mapping' => array(
//                'usernameAlreadyExist' => 'username',
//            ),
//            'cascade_validation'=>true

        ));
    }
}
