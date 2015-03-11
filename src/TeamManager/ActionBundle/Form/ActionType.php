<?php

namespace TeamManager\ActionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('player', 'hidden_entity', array(
                "class" => "TeamManager\\PlayerBundle\\Entity\\Player"
            ))
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
        return 'action';
    }
}
