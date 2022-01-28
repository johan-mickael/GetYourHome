<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Projets
 *
 * @ORM\Table(name="projets")
 * @ORM\Entity
 */
class Projets
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nom", type="string", length=255, nullable=false)
	 */
	private $nom;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_debut", type="date", nullable=false)
	 */
	private $dateDebut;

	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(name="date_fin", type="date", nullable=true)
	 */
	private $dateFin;


	/**
	 * @ORM\ManyToOne(targetEntity=Clients::class, inversedBy="projets")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $client;

	/**
	 * @ORM\ManyToOne(targetEntity=ProjetsEtat::class, inversedBy="projets")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $etat;

	/**
	 * @ORM\ManyToMany(targetEntity=Etapes::class, inversedBy="projets")
	 */
	private $etapes;

	/**
	 * @ORM\ManyToMany(targetEntity=Documents::class, inversedBy="projets")
	 */
	private $documents;


	public function __construct()
	{
		$this->etapes = new ArrayCollection();
		$this->documents = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getNom(): ?string
	{
		return $this->nom;
	}

	public function setNom(string $nom): self
	{
		$this->nom = $nom;
		return $this;
	}

	public function getDateDebut(): ?\DateTimeInterface
	{
		return $this->dateDebut;
	}

	public function setDateDebut(\DateTimeInterface $dateDebut): self
	{
		$this->dateDebut = $dateDebut;
		return $this;
	}

	public function getDateFin(): ?\DateTimeInterface
	{
		return $this->dateFin;
	}

	public function setDateFin(?\DateTimeInterface $dateFin): self
	{
		$this->dateFin = $dateFin;
		return $this;
	}


	public function getClient(): ?Clients
	{
		return $this->client;
	}

	public function setClient(?Clients $client): self
	{
		$this->client = $client;
		return $this;
	}

	public function getEtat(): ?ProjetsEtat
	{
		return $this->etat;
	}

	public function setEtat(?ProjetsEtat $etat): self
	{
		$this->etat = $etat;
		return $this;
	}

	/**
	 * @return Collection|Etapes[]
	 */
	public function getEtapes(): Collection
	{
		return $this->etapes;
	}

	public function addEtape(Etapes $etape): self
	{
		if (!$this->etapes->contains($etape)) {
			$this->etapes[] = $etape;
		}
		return $this;
	}

	public function removeEtape(Etapes $etape): self
	{
		$this->etapes->removeElement($etape);
		return $this;
	}

	/**
	 * @return Collection|Documents[]
	 */
	public function getDocuments(): Collection
	{
		return $this->documents;
	}

	public function addDocument(Documents $document): self
	{
		if (!$this->documents->contains($document)) {
			$this->documents[] = $document;
		}
		return $this;
	}

	public function removeDocument(Documents $document): self
	{
		$this->documents->removeElement($document);
		return $this;
	}

	public function getDocumentsPath(): string
	{
		return '/' . $this->client->getUser()->getEmail() . '/' . $this->getNom();
	}

	public function allDocumentsAreSubmitted($allDocuments): bool
	{
		$size = 0;
		foreach ($allDocuments as $document) {
			$size++;
		}
		return $size === $this->documents->count();
	}

	public function getNotSubmittedDocuments($allDocuments)
	{
		$res = array();
		foreach ($allDocuments as $doc) {
			if (!in_array($doc, $this->documents->toArray())) {
				$res[] = $doc;
			}
		}
		return $res;
	}
}
