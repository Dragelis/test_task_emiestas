<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FightRepository")
 */
class Fight
{
    public const STATUS_CREATED = 'created';
    public const STATUS_STARTED = 'started';
    public const STATUS_ENDED = 'ended';

    public const STATUS_CHOICES = [
        self::STATUS_CREATED,
        self::STATUS_STARTED,
        self::STATUS_ENDED
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $participant;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $opponent;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\GreaterThan("+1 hours", message="You can only add fight if there is at least one hour left before the fight begins!")
     */
    private $startTime;

    /**
     * @ORM\Column(type="string", options={"default": Fight::STATUS_CREATED})
     * @Assert\Choice(choices=Fight::STATUS_CHOICES, message="Choose a valid status.")
     */
    private $status;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": null})
     */
    private $winner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FightBet", mappedBy="fight")
     */
    private $bets;

    public function __construct()
    {
        $this->startTime = new \DateTime('+1 hour');
        $this->bets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipant(): string
    {
        return (string) $this->participant;
    }

    public function setParticipant(string $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    public function getOpponent(): string
    {
        return (string) $this->opponent;
    }

    public function setOpponent(string $opponent): self
    {
        $this->opponent = $opponent;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getStatus(): string
    {
        return (string) $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::STATUS_CHOICES, true)) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $this->status = $status;

        return $this;
    }

    public function getWinner(): ?int
    {
        return $this->winner;
    }

    public function setWinner(?int $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    /**
     * @return Collection|FightBet[]
     */
    public function getBets(): Collection
    {
        return $this->bets;
    }
}
