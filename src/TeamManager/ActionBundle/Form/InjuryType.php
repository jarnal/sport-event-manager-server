<?php

namespace TeamManager\ActionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use TeamManager\ActionBundle\Entity\Injury;

class InjuryType extends ActionType
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
                    Injury::NORMAL => 'injury.normal.label',
                    Injury::SERIOUS => 'injury.serious.label',
                    Injury::LIGHT => 'injury.light.label'
                ),
                'preferred_choices' => array(Injury::NORMAL),
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
            'data_class' => 'TeamManager\ActionBundle\Entity\Injury',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'injury';
    }
}
