<?php

namespace OCAX\OCM\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Collections\ArrayCollection;

class EnquiryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creationdate', DateTimeType::class, array(
                'required' => false,
            ))
            ->add('modificationdate', DateTimeType::class, array(
                'required' => false,
            ))
            ->add('assigndate', DateTimeType::class, array(
                'required' => false,
            ))
            ->add('submissiondate', DateTimeType::class, array(
                'required' => false,
            ))
            ->add('registrynumber')
            ->add('budgetary', ChoiceType::class, array(
                'multiple' => false,
                'choices' => array(
                    '0' => 'generic',
                    '1' => 'budgetary'
                )
            ))
            ->add('addressedto', ChoiceType::class, array(
                'multiple' => false,
                'choices' => array(
                    '0' => 'ADMINISTRATION',
                    '1' => 'Observatory'
                )
            ))
            ->add('subject')
            ->add('body', TextareaType::class, array(
                'attr' => array(
                    'class' => 'tinymce',
                )
            ))
            ->add('parent', EntityType::class, array(
                'class' => 'OCMBundle:Enquiry',
                'required' => false
            ))
            ->add('user', EntityType::class, array(
                'class' => 'CommonBundle:User'
            ))
            ->add('state', EntityType::class, array(
                'class' => 'OCAX\OCM\Entity\EnquiryState'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OCAX\OCM\Entity\Enquiry'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ocax_ocm_enquiry';
    }
}
