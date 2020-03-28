<?php

namespace App\DataProvider;

interface SlipRepositoryInterface
{
    /**
     * Create new slip.
     *
     * @param string $bookId
     * @param string $outline
     * @param string $date
     * @param string $memo
     * @param bool   $isDraft
     *
     * @return string $slipId
     */
    public function create(string $bookId, string $outline, string $date, $memo, bool $isDraft): string;

    /**
     * Delete the specified slip.
     *
     * @param string $slipId
     *
     * @return void
     */
    public function delete(string $slipId);

    /**
     * Find the draft slips that belongs to the specified book.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function findAllDraftByBookId(string $bookId): array;

    /**
     * Update the specified slip.
     *
     * @param string $slipId
     * @param array  $newData
     *
     * @return void
     */
    public function update(string $slipId, array $newData);

    /**
     * Update the flag which indicates that the slip is draft.
     *
     * @param string $slipId
     * @param bool   $isDraft
     */
    public function updateIsDraft(string $slipId, bool $isDraft);
}
