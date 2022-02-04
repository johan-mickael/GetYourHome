<?php

/**
 * Auteur : Johan Mickaël
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Clients
 *
 * @ORM\Table(name="clients")
 * @ORM\Entity
 */
class Clients
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
	 * @ORM\Column(name="nom", type="string", length=20, nullable=false)
	 */
	private $nom;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="prenom", type="string", length=20, nullable=false)
	 */
	private $prenom;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_naissance", type="date", nullable=false)
	 */
	private $dateNaissance;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="adresse", type="string", length=100, nullable=false)
	 */
	private $adresse;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="code_postale", type="integer", nullable=false)
	 */
	private $codePostale;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ville", type="string", length=50, nullable=false)
	 */
	private $ville;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="telephone", type="string", length=15, nullable=false)
	 */
	private $telephone;

	/**
	 * @ORM\OneToMany(targetEntity=Projets::class, mappedBy="client")
	 */
	private $projets;

	/**
	 * @ORM\OneToOne(targetEntity=User::class, inversedBy="clients", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;

	public function __construct()
	{
		$this->projets = new ArrayCollection();

		// Initialisation du propriété user pour la création automatique d'un compte pour le client
		$this->setUser(new User);
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

	public function getPrenom(): ?string
	{
		return $this->prenom;
	}

	public function setPrenom(string $prenom): self
	{
		$this->prenom = $prenom;

		return $this;
	}

	public function getDateNaissance(): ?\DateTimeInterface
	{
		return $this->dateNaissance;
	}

	public function setDateNaissance(\DateTimeInterface $dateNaissance): self
	{
		$this->dateNaissance = $dateNaissance;

		return $this;
	}

	public function getAdresse(): ?string
	{
		return $this->adresse;
	}

	public function setAdresse(string $adresse): self
	{
		$this->adresse = $adresse;

		return $this;
	}

	public function getCodePostale(): ?int
	{
		return $this->codePostale;
	}

	public function setCodePostale(int $codePostale): self
	{
		$this->codePostale = $codePostale;

		return $this;
	}

	public function getVille(): ?string
	{
		return $this->ville;
	}

	public function setVille(string $ville): self
	{
		$this->ville = $ville;

		return $this;
	}

	public function getTelephone(): ?string
	{
		return $this->telephone;
	}

	public function setTelephone(string $telephone): self
	{
		$this->telephone = $telephone;

		return $this;
	}

	/**
	 * @return Collection|Projets[]
	 */
	public function getProjets(): Collection
	{
		return $this->projets;
	}

	public function addProjet(Projets $projet): self
	{
		if (!$this->projets->contains($projet)) {
			$this->projets[] = $projet;
			$projet->setClient($this);
		}

		return $this;
	}

	public function removeProjet(Projets $projet): self
	{
		if ($this->projets->removeElement($projet)) {
			// set the owning side to null (unless already changed)
			if ($projet->getClient() === $this) {
				$projet->setClient(null);
			}
		}

		return $this;
	}

	public function getDisplayName(): string
	{
		return $this->nom . ' ' . $this->prenom;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(User $user): self
	{
		$this->user = $user;
		return $this;
	}

	// Création de l'utilisateur associé au client
	public function setUserOnCreateClient(string $email, string $password): self
	{
		// Ajout du role
		$this->user->setRoles(['ROLE_CLIENT']);

		// Assignement des identifiants
		$this->user->setEmail($email);
		$this->user->setPassword($password);

		return $this;
	}
}
