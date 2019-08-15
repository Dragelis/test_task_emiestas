<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Fight;
use App\Entity\FightBet;

use PhpOffice\PhpSpreadsheet\Reader\Csv;

class ImportUploader
{
    private const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function import(UploadedFile $file): int
    {
        $tempFilePath = $file->getRealPath();

        if (!$tempFilePath) {
            return 0;
        }

        $reader = new Csv();

        $spreadsheet = $reader->load($tempFilePath);

        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $importedFightsCount = 0;

        array_shift($sheetData);

        foreach ($sheetData as $row) {
            $fight = new Fight();

            if (empty($row[0])) {
                continue;
            }

            $fight->setParticipant($row[0]);

            if (empty($row[1])) {
                continue;
            }

            $fight->setOpponent($row[1]);

            if (empty($row[2])) {
                continue;
            }

            $startTime = \DateTime::createFromFormat(self::DEFAULT_DATETIME_FORMAT, $row[2]);

            if (!$startTime) {
                continue;
            }

            $fight->setStartTime($startTime);

            $nowTime = new \DateTime('+1 hour');

            if ($startTime > $nowTime) {
                $fight->setStatus(Fight::STATUS_CREATED);
            } else {
                if (array_key_exists(3, $row)) {
                    if ($row[0] === $row[3]) {
                        $fight->setWinner(FightBet::OPTION_PARTICIPANT);
                    } else if ($row[1] === $row[3]) {
                        $fight->setWinner(FightBet::OPTION_OPPONENT);
                    }
                }

                $fight->setStatus(Fight::STATUS_ENDED);
            }

            $this->entityManager->persist($fight);

            $importedFightsCount++;
        }

        $this->entityManager->flush();

        return $importedFightsCount;
    }
}
