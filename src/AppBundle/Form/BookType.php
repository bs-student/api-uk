<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bookTitle')
            ->add('bookDirectorAuthorArtist')
            ->add('bookEdition')
            ->add('bookIsbn10')
            ->add('bookIsbn13')
            ->add('bookPublisher')
            ->add('bookPublishDate')
            ->add('bookBinding')
            ->add('bookPage')
            ->add('bookLanguage')
            ->add('bookDescription')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Book'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_book';
    }
}
