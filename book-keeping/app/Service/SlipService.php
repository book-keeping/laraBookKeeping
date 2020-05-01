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
    public function createSlipAsDraft(string $bookId, string $outline, string $date, array $entries, string $memo = null): string
    {
        $slipId = $this->slip->create($bookId, $outline, $date, $memo, true);
        foreach ($entries as &$entry) {
            $this->slipEntry->create($slipId, $entry['debit'], $entry['credit'], $entry['amount'], $entry['client'], $entry['outline']);
        }

        return $slipId;
    }

    /**
     * Create a new slip entry.
     *
     * @param string $slipId
     * @param string $debit
     * @param string $credit
     * @param int    $amount
     * @param string $client
     * @param string $outline
     *
     * @return string $slipEntryId
     */
    public function createSlipEntry(string $slipId, string $debit, string $credit, int $amount, string $client, string $outline): string
    {
        return $this->slipEntry->create($slipId, $debit, $credit, $amount, $client, $outline);
    }

    /**
     * Delete the specified slip.
     *
     * @param string $slipId
     *
     * @return void
     */
    public function deleteSlip(string $slipId)
    {
        $this->slip->delete($slipId);
    }

    /**
     * Delete the specified slip entry.
     *
     * @param string $slipEntryId
     *
     * @return void
     */
    public function deleteSlipEntry(string $slipEntryId)
    {
        $this->slipEntry->delete($slipEntryId);
    }

    /**
     * Retrieve the amount flow of each account between specified data.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveAmountFlows(string $fromDate, string $toDate, string $bookId): array
    {
        return $this->slipEntry->calculateSum($fromDate, $toDate, $bookId);
    }

    /**
     * Retrieve draft slips.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveDraftSlips(string $bookId): array
    {
        return $this->slip->findAllDraftByBookId($bookId);
    }

    /**
     * Retrieve a slip.
     *
     * @param string $slipId
     *
     * @return array|null
     */
    public function retrieveSlip(string $slipId): ?array
    {
        return $this->slip->findById($slipId);
    }

    /**
     * Retrieve slip entries between specified date.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param array  $condition
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveSlipEntries(string $fromDate, string $toDate, array $condition, string $bookId): array
    {
        return $this->slipEntry->searchSlipEntries($fromDate, $toDate, $condition, $bookId);
    }

    /**
     * Retrieve slip entries bound to specify slip.
     *
     * @param string $slipId
     *
     * @return array
     */
    public function retrieveSlipEntriesBoundTo(string $slipId): array
    {
        return $this->slipEntry->findAllBySlipId($slipId);
    }

    /**
     * Retrieve slip that bound specify slip entry.
     *
     * @param string $slipEntryId
     *
     * @return string | null
     */
    public function retrieveSlipThatBound(string $slipEntryId)
    {
        $slipEntry = $this->slipEntry->findById($slipEntryId);

        return is_null($slipEntry) ? null : $slipEntry['slip_id'];
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

    /**
     * Update the slip date.
     *
     * @param string $slipId
     * @param string $date
     */
    public function updateDate(string $slipId, string $date)
    {
        $this->slip->update($slipId, ['date' => $date]);
    }
}
