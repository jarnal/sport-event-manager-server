<?php

namespace TeamManager\TeamBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use TeamManager\PlayerBundle\Entity\Player;

class TeamType extends AbstractType
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
            ->add('image_url')
            ->add('default_location')
            ->add('manager', 'hidden_entity', array(
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
            'data_class' => 'TeamManager\TeamBundle\Entity\Team',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'team';
    }
}
