<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookDealType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bookPriceSell')
            ->add('bookCondition')
            ->add('bookIsHighlighted')
            ->add('bookHasNotes')
            ->add('bookComment')
            ->add('bookContactMethod')
            ->add('bookContactHomeNumber')
            ->add('bookContactCellNumber')
            ->add('bookContactEmail')
            ->add('bookIsAvailablePublic')
            ->add('bookPaymentMethodCaShOnExchange')
            ->add('bookPaymentMethodCheque')
            ->add('bookAvailableDate')
            ->add('bookSellingStatus')
            ->add('bookStatus')
            ->add('book')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\BookDeal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_bookdeal';
    }
}
