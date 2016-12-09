<?php

namespace OCAX\Common\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                'label' => 'Username',
                'attr' => array(
                    'class' => 'required',
                )
            ))
            ->add('plainpassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'required' => $options['require_password'],
                'first_options' => array(
                    'label' => 'Password',
                ),
                'second_options' => array(
                    'label' => 'Repeat password',
                )
            ))
            ->add('fullname')
            ->add('email')
            ->add('active')
            ->add('joined')
            ->add('disabled')
            ->add('member')
            ->add('descriptioneditor')
            ->add('manager')
            ->add('admin')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OCAX\Common\Entity\User',
            'require_password' => true,
        ));
    }
}
