<?php

namespace App\Form;

use App\Entity\Projets;
use App\Entity\Clients;
use App\Entity\ProjetsEtat;
use App\Entity\Etapes;
use App\Entity\Documents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ProjetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('client', EntityType::class, [
                'label' => 'Client',
                'class' => Clients::class,
                'choice_label' => function ($client) {
                    return $client->getDisplayName();
                }
            ])
            ->add('etat', EntityType::class, [
                'class' => ProjetsEtat::class,
                'choice_label' => 'libelle'
            ])
			->add('documents', EntityType::class, [
                'class' => Documents::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('etapes', EntityType::class, [
                'class' => Etapes::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projets::class,
        ]);
    }
}
