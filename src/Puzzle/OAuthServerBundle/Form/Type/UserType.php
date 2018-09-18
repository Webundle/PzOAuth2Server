<?php

namespace Puzzle\OAuthServerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Puzzle\OAuthServerBundle\Entity\User;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.firstName',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('lastName', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.lastName',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
            ->add('email', EmailType::class, [
                'translation_domain' => 'messages',
                'label' => 'user.property.user.email',
                'label_attr' => [
                    'class' => 'uk-form-label'
                ],
                'attr' => [
                    'class' => 'md-input'
                ],
            ])
	        ->add('username', TextType::class, [
	            'translation_domain' => 'messages',
	            'label' => 'user.property.user.username',
	            'label_attr' => [
	                'class' => 'uk-form-label'
	            ],
	            'attr' => [
	                'class' => 'md-input'
	            ],
	        ])
            ->add('plainPassword', RepeatedType::class, array(
        		'type' => PasswordType::class,
        		'invalid_message' => 'Les mots de passe doivent correspondre',
        		'options' => ['required' => false],
                'first_options'  => [
                    'translation_domain' => 'messages',
                    'label' => 'user.property.user.password',
                    'label_attr' => [
                        'class' => 'uk-form-label'
                    ],
                    'attr' => [
                        'class' => 'md-input'
                    ]
                ],
                'second_options'  => [
                    'translation_domain' => 'messages',
                    'label' => 'user.property.user.password_repeated',
                    'label_attr' => [
                        'class' => 'uk-form-label'
                    ],
                    'attr' => [
                        'class' => 'md-input'
                    ]
                ],
                'required' => false
            ))
            ->add('register', SubmitType::class, [
                'translation_domain' => 'messages',
                'label' => 'button.save',
                'attr' => [
                    'class' => 'button hide pull-right'
                ],
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    	$resolver->setDefaults(array(
    		'data_class' => User::class
    	));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }
}
