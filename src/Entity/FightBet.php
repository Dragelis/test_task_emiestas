<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Fight;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FightBetRepository")
 */
class FightBet
{
    public const OPTION_PARTICIPANT = 0;
    public const OPTION_OPPONENT = 1;
    
    public const OPTION_CHOICES = [
        self::OPTION_PARTICIPANT,
        self::OPTION_OPPONENT
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="`option`", type="integer")
     * @Assert\Choice(choices=FightBet::OPTION_CHOICES, message="Choose a valid option.")
     */
    private $option;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Fight", inversedBy="bets")
     */
    private $fight;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bets")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOption(): int
    {
        return $this->option;
    }

    public function setOption(int $option): self
    {
        if (!in_array($option, self::OPTION_CHOICES)) {
            throw new \InvalidArgumentException('Invalid option');
        }

        $this->option = $option;

        return $this;
    }

    public function getFight(): ?Fight
    {
        return $this->fight;
    }

    public function setFight(?Fight $fight): self
    {
        $this->fight = $fight;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
