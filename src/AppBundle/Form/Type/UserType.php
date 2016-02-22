<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Repository\ReferralRepository;
use AppBundle\Repository\CampusRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;


use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('fullName','text',array(
            'constraints' => array(
                new NotBlank(),

            ),
//            'error_bubbling'=>true
        ));

        $builder->add('username','text',array(
//            'validation_groups' => false
            'constraints' => array(
                new NotBlank(),

            ),
//            'error_bubbling'=>true
        ));


        $builder->add('email','email',array(
            'constraints' => array(
                new NotBlank(),
                new Email(),
            ),
//            'error_bubbling'=>true
        ));




        $builder->add('referral', 'entity', array(
            'class' => "AppBundle:Referral",
//            'empty_value' => 'Choose an option',
//            'query_builder' => function(ReferralRepository $er) {
//                    return $er->createQueryBuilder('u')
//                        ->orderBy('u.id', 'ASC');
//                },
            'property' => 'referralName',
            'constraints' => array(
                new NotBlank(),

            ),
//            'error_bubbling'=>true

        ));
//
        $builder->add('campus', 'entity', array(
            'class' => "AppBundle:Campus",
//            'empty_value' => 'Choose University Campus',
//            'query_builder' => function(CampusRepository $er) {
//                    return $er->getCampus();
//                },
            'property' => 'campusName',
            'constraints' => array(
                new NotBlank(),

            ),
//            'error_bubbling'=>true


        ));

//        $builder->add('save', 'button', array(
//            'attr' => array('class' => 'save'),
//        ));
    }


    public function getName()
    {
        return 'app_created_user_update';
    }




    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_protection' => false,
//            'validation_groups' => false,
            'allow_extra_fields' => true,
//            'error_mapping' => array(
//                'usernameAlreadyExist' => 'username',
//            ),

        ));
    }

}