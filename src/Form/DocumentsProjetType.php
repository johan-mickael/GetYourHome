<?php

/**
 * Auteur : Johan Mickaël
 */

namespace App\Form;

use App\Entity\Documents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DocumentsProjetType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('documents', EntityType::class, [
				'class' => Documents::class,
				'choice_label' => 'name',
				'multiple' => true,
				'expanded' => true,
				'disabled' => true
			]);
	}
}
