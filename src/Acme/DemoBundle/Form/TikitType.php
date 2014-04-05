<?php

namespace Acme\DemoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TikitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tikit_name', 'text');
        $builder->add('tikit_url', 'url');
    }

    public function getName()
    {
        return 'tikit';
    }
}
