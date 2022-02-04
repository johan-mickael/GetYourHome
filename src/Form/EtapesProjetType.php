<?php

/**
 * Auteur : Johan Mickaël
 */

namespace App\Form;

use App\Entity\Etapes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EtapesProjetType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('etapes', EntityType::class, [
				'class' => Etapes::class,
				'choice_label' => 'libelle',
				'multiple' => true,
				'expanded' => true,
				'disabled' => true
			]);
	}
}
