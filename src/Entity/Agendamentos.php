<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AgendamentosRepository")
 */
class Agendamentos implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dataInicio;

    /**
     * @ORM\Column(type="date")
     */
    private $dataFim;

    /**
     * @ORM\Column(type="time")
     */
    private $horaInicio;

    /**
     * @ORM\Column(type="time")
     */
    private $horaFim;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observacao;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Salas", inversedBy="agendamentos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sala;

    public static $HORA_FORMATO = "H:i";

    public static $DATA_FORMATO = "d/m/Y";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataInicio(): \DateTime
    {
        return $this->dataInicio;
    }

    public function setDataInicio($dataInicio): self
    {
        $this->dataInicio = $dataInicio;

        return $this;
    }

    public function getDataFim(): \DateTime
    {
        return $this->dataFim;
    }

    public function setDataFim($dataFim): self
    {
        $this->dataFim = $dataFim;

        return $this;
    }

    public function getHoraInicio(): \DateTime
    {
        return $this->horaInicio;
    }

    public function setHoraInicio($horaInicio): self
    {
        $this->horaInicio = $horaInicio;

        return $this;
    }

    public function getHoraFim(): \DateTime
    {
        return $this->horaFim;
    }

    public function setHoraFim($horaFim): self
    {
        $this->horaFim = $horaFim;

        return $this;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getSala(): ?Salas
    {
        return $this->sala;
    }

    public function setSala(?Salas $sala): self
    {
        $this->sala = $sala;

        return $this;
    }

    public function getDataInicioFormatado(): string
    {
        return $this->dataInicio->format(self::$DATA_FORMATO);
    }

    public function getDataFimFormatado(): string
    {
        return $this->dataFim->format(self::$DATA_FORMATO);
    }

    public function getHoraInicioFormatado(): string
    {
        return $this->horaInicio->format(self::$HORA_FORMATO);
    }

    public function getHoraFimFormatado(): string
    {
        return $this->horaFim->format(self::$HORA_FORMATO);
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'dataInicio' => $this->getDataInicioFormatado(),
            'dataFim' => $this->getDataFimFormatado(),
            'horaInicio' => $this->getHoraInicioFormatado(),
            'horaFim' => $this->getHoraFimFormatado(),
            'observacao' => $this->observacao,
            '_links' => [
                [
                    'rel' => 'self',
                    'path' => '/api/agendamentos/' . $this->id
                ],
                [
                    'rel' => 'sala',
                    'path' => 'api/salas/' . $this->sala->getId()
                ]
            ]
        ];
    }
}
