<?php

namespace App;

/**
 * Pagination Helper Class
 * Handles pagination logic for controllers and views
 */
class Pagination
{
    private int $page;
    private int $perPage;
    private int $total;
    private int $totalPages;
    private int $offset;

    /**
     * Constructor
     * 
     * @param int $total Total number of items
     * @param int $perPage Items per page (default: 10)
     * @param int $page Current page (default: 1)
     */
    public function __construct(int $total, int $perPage = 10, int $page = 1)
    {
        $this->total = max(0, $total);
        $this->perPage = max(1, $perPage);
        $this->page = max(1, $page);
        $this->totalPages = (int) ceil($this->total / $this->perPage);
        $this->page = min($this->page, max(1, $this->totalPages));
        $this->offset = ($this->page - 1) * $this->perPage;
    }

    /**
     * Get the current page number
     */
    public function getCurrentPage(): int
    {
        return $this->page;
    }

    /**
     * Get items per page
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get total number of items
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Get total number of pages
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * Get offset for database query
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Get limit for database query
     */
    public function getLimit(): int
    {
        return $this->perPage;
    }

    /**
     * Check if there is a previous page
     */
    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }

    /**
     * Check if there is a next page
     */
    public function hasNextPage(): bool
    {
        return $this->page < $this->totalPages;
    }

    /**
     * Get previous page number
     */
    public function getPreviousPage(): int
    {
        return max(1, $this->page - 1);
    }

    /**
     * Get next page number
     */
    public function getNextPage(): int
    {
        return min($this->totalPages, $this->page + 1);
    }

    /**
     * Get start item number for display
     */
    public function getStartItem(): int
    {
        if ($this->total === 0) {
            return 0;
        }
        return $this->offset + 1;
    }

    /**
     * Get end item number for display
     */
    public function getEndItem(): int
    {
        if ($this->total === 0) {
            return 0;
        }
        return min($this->offset + $this->perPage, $this->total);
    }

    /**
     * Get page range for display (e.g., [3, 4, 5, 6, 7])
     */
    public function getPageRange(int $windowSize = 5): array
    {
        $startPage = max(1, $this->page - floor($windowSize / 2));
        $endPage = min($this->totalPages, $startPage + $windowSize - 1);
        
        // Adjust if at the end
        if ($endPage - $startPage < $windowSize - 1) {
            $startPage = max(1, $endPage - $windowSize + 1);
        }

        return range($startPage, $endPage);
    }

    /**
     * Paginate an array (for in-memory pagination)
     * 
     * @param array $items Items to paginate
     * @return array Paginated items
     */
    public function paginate(array $items): array
    {
        return array_slice($items, $this->offset, $this->perPage);
    }
}
