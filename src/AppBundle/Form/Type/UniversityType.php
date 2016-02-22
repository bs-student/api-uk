<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Repository\ReferralRepository;
use AppBundle\Repository\CampusRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\Form\Type\CampusType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class UniversityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('universityName','text',array(
            'constraints' => array(
                new NotBlank(),
            ),
//            'error_bubbling'=>true
        ));

        $builder->add('universityUrl','text',array(

//            'error_bubbling'=>true
        ));




        $builder->add('referral', 'entity', array(
            'class' => "AppBundle:Referral",
            'property' => 'referralName',
            'constraints' => array(
                new NotBlank(),

            ),
//            'error_bubbling'=>true

        ));
        $builder->add('universityStatus','text',array(
            'constraints' => array(
                new NotBlank(),
            ),
//            'error_bubbling'=>true
        ));
        $builder->add('campuses', 'collection', array(
            'type'         => new CampusType(),
            'allow_add'    => true,

        ))
        ;

    }


    public function getName()
    {
        return 'app_university_update';
    }




    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\University',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ));
    }

}