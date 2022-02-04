<?php

/**
 * Auteur : Johan Mickaël
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
	private $targetDirectory;
	private $slugger;

	public function __construct($targetDirectory, SluggerInterface $slugger)
	{
		$this->targetDirectory = $targetDirectory;
		$this->slugger = $slugger;
	}

	// Service pour transformer le nom des fichiers clients et le mettre dans le dossier souhaité
	public function upload(UploadedFile $file, array $options)
	{
		// cela est nécessaire pour inclure en toute sécurité le nom du fichier dans l'URL
		$safeFilename = $this->slugger->slug($options['filename']);
		$newFileName = $safeFilename . '.' . $file->guessExtension();

		// Déplace le fichier dans le répertoire où sont stockées les documents
		try {
			$file->move(
				$this->getTargetDirectory() . $options['projet']->getDocumentsPath(),
				$newFileName
			);
		} catch (FileException $e) {
			throw $e;
		}
		return $newFileName;
	}

	public function getTargetDirectory()
	{
		return $this->targetDirectory;
	}
}