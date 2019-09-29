<?php

namespace App\Service;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;

class SlipService
{
    /**
     * Slip repository instance.
     *
     * @var \App\DataProvider\SlipRepositoryInterface
     */
    private $slip;

    /**
     * Slip repository instance.
     *
     * @var \App\DataProvider\SlipEntryRepositoryInterface
     */
    private $slipEntry;

    /**
     * Create a new SlipService instance.
     *
     * @param \App\DataProvider\SlipRepositoryInterface      $slip
     * @param \App\DataProvider\SlipEntryRepositoryInterface $slipEntry
     */
    public function __construct(SlipRepositoryInterface $slip, SlipEntryRepositoryInterface $slipEntry)
    {
        $this->slip = $slip;
        $this->slipEntry = $slipEntry;
    }

    /**
     * Create new Slip as draft.
     *
     * @param string $bookId
     * @param string $outline
     * @param string $date
     * @param array  $entries
     * @param string $memo
     *
     * @return string $slipId
     */
    public function createSlipAsDraft(string $bookId, string $outline, string $date, array $entries, string $memo = null) : string
    {
        $slipId = $this->slip->create($bookId, $outline, $date, $memo, true);
        foreach ($entries as &$entry) {
            $this->slipEntry->create($slipId, $entry['debit'], $entry['credit'], $entry['amount'], $entry['client'], $entry['outline']);
        }

        return $slipId;
    }

    /**
     * Submit the slip.
     *
     * @param string $slipId
     */
    public function submitSlip(string $slipId)
    {
        $this->slip->updateIsDraft($slipId, false);
    }
}