<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\SlipRepositoryInterface;

class SlipRepository implements SlipRepositoryInterface
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
    public function create(string $bookId, string $outline, string $date, $memo, bool $isDraft): string
    {
        $slip = new Slip();
        $slip->book_id = $bookId;
        $slip->slip_outline = $outline;
        $slip->slip_memo = $memo;
        $slip->date = $date;
        $slip->is_draft = $isDraft;
        $slip->save();

        return $slip->slip_id;
    }

    /**
     * Delete the specified slip.
     *
     * @param string $slipId
     *
     * @return void
     */
    public function delete(string $slipId)
    {
        $slip = Slip::find($slipId);
        if (!is_null($slip)) {
            $slip->delete();
        }
    }

    /**
     * Find the draft slips that belongs to the specified book.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function findAllDraftByBookId(string $bookId): array
    {
        $list = Slip::select('slip_id', 'date', 'slip_outline', 'slip_memo')
            ->where('book_id', $bookId)
            ->where('is_draft', true)
            ->get()->toArray();

        return $list;
    }

    /**
     * Find a slip.
     *
     * @param string $slipId
     *
     * @return array|null
     */
    public function findById(string $slipId): ?array
    {
        $slip = Slip::select('book_id', 'slip_id', 'date', 'slip_outline', 'slip_memo')
            ->where('slip_id', $slipId)
            ->where('is_draft', false)
            ->first();

        return is_null($slip) ? null : $slip->toArray();
    }

    /**
     * Update the specified slip.
     *
     * @param string $slipId
     * @param array  $newData
     *
     * @return void
     */
    public function update(string $slipId, array $newData)
    {
        $slip = Slip::find($slipId);
        if (!is_null($slip)) {
            if (array_key_exists('outline', $newData)) {
                $slip->slip_outline = $newData['outline'];
            }
            if (array_key_exists('memo', $newData)) {
                $slip->slip_memo = $newData['memo'];
            }
            if (array_key_exists('date', $newData)) {
                $slip->date = $newData['date'];
            }
            $slip->save();
        }
    }

    /**
     * Update the flag which indicates that the slip is draft.
     *
     * @param string $slipId
     * @param bool   $isDraft
     */
    public function updateIsDraft(string $slipId, bool $isDraft)
    {
        $slip = Slip::find($slipId);
        $slip->is_draft = $isDraft;
        $slip->save();
    }
}
