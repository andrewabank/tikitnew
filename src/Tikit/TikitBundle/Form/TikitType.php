<?php

namespace Tikit\TikitBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TikitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tikit_name', 'text');
        $builder->add('tikit_url', 'url');
        $builder->add('captcha', 'captcha');
    }

    public function getName()
    {
        return 'tikit';
    }
}
