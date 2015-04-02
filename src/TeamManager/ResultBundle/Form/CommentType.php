<?php

namespace TeamManager\ResultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content')
            ->add('event', 'hidden_entity', array(
                "class" => "TeamManager\\EventBundle\\Entity\\Event"
            ))
            ->add('player_receiver', 'hidden_entity', array(
                "class" => "TeamManager\\PlayerBundle\\Entity\\Player"
            ))
            ->add('player_sender', 'hidden_entity', array(
                "class" => "TeamManager\\PlayerBundle\\Entity\\Player"
            ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TeamManager\ResultBundle\Entity\Comment',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'comment';
    }
}
