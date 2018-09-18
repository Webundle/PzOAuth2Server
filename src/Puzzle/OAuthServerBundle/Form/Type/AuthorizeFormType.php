<?php

namespace Puzzle\OAuthServerBundle\Form\Type;

use FOS\OAuthServerBundle\Form\Type\AuthorizeFormType as BaseAuthorizeFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 */
class AuthorizeFormType extends BaseAuthorizeFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('accepted', SubmitType::class, ['mapped' => false]);
        $builder->add('rejected', SubmitType::class, ['mapped' => false]);
    }

}
