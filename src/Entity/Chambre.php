<?php

namespace App\Entity;

use App\Repository\ChambreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChambreRepository::class)
 */
class Chambre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $numero_batimat;

   
    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNumeroBatimat(): ?string
    {
        return $this->numero_batimat;
    }

    public function setNumeroBatimat(string $numero_batimat): self
    {
        $this->numero_batimat = $numero_batimat;

        return $this;
    }

    // public function getEtudiant(): ?Etudiant
    // {
    //     return $this->etudiant;
    // }

    // public function setEtudiant(?Etudiant $etudiant): self
    // {
    //     $this->etudiant = $etudiant;

    //     return $this;
    // }

    // /**
    //  * @return Collection|Etudiant[]
    //  */
    // public function getEtudiants(): Collection
    // {
    //     return $this->etudiants;
    // }

    // public function addEtudiant(Etudiant $etudiant): self
    // {
    //     if (!$this->etudiants->contains($etudiant)) {
    //         $this->etudiants[] = $etudiant;
    //         $etudiant->setChambre($this);
    //     }

    //     return $this;
    // }

    // public function removeEtudiant(Etudiant $etudiant): self
    // {
    //     if ($this->etudiants->contains($etudiant)) {
    //         $this->etudiants->removeElement($etudiant);
    //         // set the owning side to null (unless already changed)
    //         if ($etudiant->getChambre() === $this) {
    //             $etudiant->setChambre(null);
    //         }
    //     }

    //     return $this;
    // }
}
