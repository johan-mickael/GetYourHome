<?php

/**
 * Auteur : Johan Mickaël
 */

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=180, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="json")
	 */
	private $roles = [];

	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @ORM\OneToOne(targetEntity=Clients::class, mappedBy="user", cascade={"persist", "remove"})
	 */
	private $clients;

	/**
	 * @ORM\OneToOne(targetEntity=Administrateurs::class, mappedBy="user", cascade={"persist", "remove"})
	 */
	private $administrateurs;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string
	{
		return (string) $this->email;
	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
	{
		$roles = $this->roles;
		// guarantee every user at least has ROLE_USER
		$roles[] = 'ROLE_USER';

		return array_unique($roles);
	}

	public function setRoles(array $roles): self
	{
		$this->roles = $roles;

		return $this;
	}

	/**
	 * @see PasswordAuthenticatedUserInterface
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials()
	{
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}

	public function getClients(): ?Clients
	{
		return $this->clients;
	}

	public function setClients(Clients $clients): self
	{
		// set the owning side of the relation if necessary
		if ($clients->getUser() !== $this) {
			$clients->setUser($this);
		}

		$this->clients = $clients;

		return $this;
	}

	public function getAdministrateurs(): ?Administrateurs
	{
		return $this->administrateurs;
	}

	public function setAdministrateurs(?Administrateurs $administrateurs): self
	{
		// unset the owning side of the relation if necessary
		if ($administrateurs === null && $this->administrateurs !== null) {
			$this->administrateurs->setUser(null);
		}

		// set the owning side of the relation if necessary
		if ($administrateurs !== null && $administrateurs->getUser() !== $this) {
			$administrateurs->setUser($this);
		}

		$this->administrateurs = $administrateurs;

		return $this;
	}

	// Verifie si l'utilisateur est un administrateur
	public function isAdmin(): bool
	{
		foreach ($this->roles as $role)
			if (strcmp($role, 'ROLE_ADMIN') == 0) return true;
		return false;
	}

	// Verifie si l'utilisateur est un employé
	public function isEmployee(): bool
	{
		foreach ($this->roles as $role)
			// L'admin est aussi considéré comme étant un employé
			if (strcmp($role, 'ROLE_EMPLOYEE') == 0 || strcmp($role, 'ROLE_ADMIN') == 0) return true;
		return false;
	}
}
