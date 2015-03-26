<?php

namespace TeamManager\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('date', 'datetime')
            ->add('subscription_type')
            ->add('player_limit')
            ->add('location')
            ->add('season')
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
        return 'event';
    }
}
