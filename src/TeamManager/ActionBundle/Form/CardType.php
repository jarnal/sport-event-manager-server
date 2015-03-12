<?php

namespace TeamManager\ActionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use TeamManager\ActionBundle\Entity\Card;

class CardType extends ActionType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'choices' => array(Card::YELLOW_CARD => 'card.yellow.label', Card::RED_CARD => 'card.red.label'),
                'preferred_choices' => array('yellow'),
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
            'data_class' => 'TeamManager\ActionBundle\Entity\Card',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'card';
    }
}
