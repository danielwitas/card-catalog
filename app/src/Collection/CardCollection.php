<?php

namespace App\Collection;

class CardCollection
{
    private array $items;
    private int $totalItemCount;
    private int $currentPageNumber;
    private int $itemNumberPerPage;

    /**
     * @param array $items
     * @param int $totalItemCount
     * @param int $currentPageNumber
     * @param int $itemNumberPerPage
     */
    public function __construct(iterable $items, int $totalItemCount, int $currentPageNumber, int $itemNumberPerPage)
    {
        $this->items = $items;
        $this->totalItemCount = $totalItemCount;
        $this->currentPageNumber = $currentPageNumber;
        $this->itemNumberPerPage = $itemNumberPerPage;
    }

    /**
     * @return array
     */
    public function getItems(): iterable
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getTotalItemCount(): int
    {
        return $this->totalItemCount;
    }

    /**
     * @return int
     */
    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    /**
     * @return int
     */
    public function getItemNumberPerPage(): int
    {
        return $this->itemNumberPerPage;
    }





}