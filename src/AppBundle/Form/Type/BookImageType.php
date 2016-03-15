<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Repository\ReferralRepository;
use AppBundle\Repository\CampusRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;


use Symfony\Component\OptionsResolver\OptionsResolver;

class BookImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('imageName','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));
        $builder->add('imageUrl','text',array(
            'constraints' => array(
                new NotBlank(),

            )
        ));
        $builder->add('titleImage','checkbox');


    }


    public function getName()
    {
        return 'appbundle_bookimage';
    }




    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\BookImage',
            'csrf_protection' => false,
//            'validation_groups' => false,
            'allow_extra_fields' => true,
//            'error_mapping' => array(
//                'usernameAlreadyExist' => 'username',
//            ),

        ));
    }

}