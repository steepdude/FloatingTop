<?php

namespace Tops\page;

use pocketmine\Server;

class PageExecutor
{
    private const DEFAULT_PAGE_INDEX = 0;

    private PageFactory $factory;
    private int $page = self::DEFAULT_PAGE_INDEX;

    public function __construct(PageFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Moves to the next page, wrapping around to the first page if at the end.
     *
     * @return void
     */
    public function nextPage(): void
    {
        $pageCount = count($this->factory->getPages());

        if ($this->page >= $pageCount - 1) {
            $this->page = self::DEFAULT_PAGE_INDEX; // Set to the first page
        } else {
            $this->page++;
        }
    }

    public function previousPage(): void
    {
        $pageCount = count($this->factory->getPages());

        if ($this->page <= self::DEFAULT_PAGE_INDEX) {
            $this->page = max(0, $pageCount - 1);
        } else {
            $this->page--;
        }
    }

    /**
     * Sets the page number to a specific value.
     *
     * @param int $page The page number to set.
     * @return void
     */
    public function setPage(int $page): void
    {
        $pageCount = count($this->factory->getPages());
        if ($page < 0 || $page >= $pageCount) {
            return;
        }

        $this->page = $page;
    }

    /**
     * Gets the current page number.
     *
     * @return int The current page number.
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Gets the PageFactory instance associated with this executor.
     *
     * @return PageFactory The PageFactory instance.
     */
    public function getFactory(): PageFactory
    {
        return $this->factory;
    }
}