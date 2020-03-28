<?php

namespace App\DataProvider;

interface SlipEntryRepositoryInterface
{
    /**
     * Calculate the sum of debit and credit for each account about slip entries between the specified date.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $bookId
     *
     * @return array
     */
    public function calculateSum(string $fromDate, string $toDate, string $bookId): array;

    /**
     * Create new slip entry.
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
    public function create(string $slipId, string $debit, string $credit, int $amount, string $client, string $outline): string;

    /**
     * Delete the specified slip entry.
     *
     * @param string $slipEntryId
     *
     * @return void
     */
    public function delete(string $slipEntryId);

    /**
     * Find slip entry.
     *
     * @param string $slipEntryId
     *
     * @return array | null
     */
    public function findById(string $slipEntryId): ?array;

    /**
     * Find the slip entries that belongs to the specified slip.
     *
     * @param string $slipId
     *
     * @return array
     */
    public function findAllBySlipId(string $slipId): array;

    /**
     * Search slip entries between specified date.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $bookId
     *
     * @return array
     */
    public function searchSlipEntries(string $fromDate, string $toDate, string $bookId): array;
}
