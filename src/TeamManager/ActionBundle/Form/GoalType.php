<?php

namespace TeamManager\ActionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use TeamManager\ActionBundle\Entity\Goal;

class GoalType extends ActionType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'choices' => array(
                    Goal::SPECIAL => 'goal.special.label',
                    Goal::NORMAL => 'goal.normal.label',
                    Goal::AUTOGOAL => 'goal.autogoal.label',
                    Goal::HEADER => 'goal.header.label',
                    Goal::VOLLEY => 'goal.volley.label'
                ),
                'preferred_choices' => array(Goal::NORMAL),
            ))
            ->add('time', 'datetime', array(
                'required'=>false
            ))
        ;
        parent::buildForm($builder, $options);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TeamManager\ActionBundle\Entity\Goal',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'goal';
    }
}
