<?php

namespace TeamManager\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameFriendlyType extends EventType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('team', 'hidden_entity', array(
                "class" => "TeamManager\\TeamBundle\\Entity\\Team"
            ));
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'game';
    }
}
