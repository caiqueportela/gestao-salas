<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SalasRepository")
 */
class Salas implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descricao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Agendamentos", mappedBy="sala", orphanRemoval=true)
     */
    private $agendamentos;

    public function __construct()
    {
        $this->agendamentos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * @return Collection|Agendamentos[]
     */
    public function getAgendamentos(): Collection
    {
        return $this->agendamentos;
    }

    public function addAgendamento(Agendamentos $agendamento): self
    {
        if (!$this->agendamentos->contains($agendamento)) {
            $this->agendamentos[] = $agendamento;
            $agendamento->setSala($this);
        }

        return $this;
    }

    public function removeAgendamento(Agendamentos $agendamento): self
    {
        if ($this->agendamentos->contains($agendamento)) {
            $this->agendamentos->removeElement($agendamento);
            // set the owning side to null (unless already changed)
            if ($agendamento->getSala() === $this) {
                $agendamento->setSala(null);
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            '_links' => [
                [
                    'rel' => 'self',
                    'path' => '/api/salas/' . $this->id
                ],
                [
                    'rel' => 'agendamentos',
                    'path' => '/api/salas/' . $this->id . '/agendamentos'
                ]
            ]
        ];
    }
}
