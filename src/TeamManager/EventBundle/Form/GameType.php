<?php

namespace TeamManager\EventBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use TeamManager\EventBundle\Entity\Event;

class GameType extends EventType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('type', 'choice', array(
                'choices' => array(Event::GAME => 'event.type.game', Event::GAME_FRIENDLY => 'event.type.game_friendly'),
                'preferred_choices' => array('game'),
            ))
            ->add('team', 'hidden_entity', array(
                "class" => "TeamManager\\TeamBundle\\Entity\\Team"
            ));
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TeamManager\EventBundle\Entity\Game',
            'csrf_protection' => false/*,
            'validation_groups' => array($this->getName())*/
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'game';
    }
}
