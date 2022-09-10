<?php

declare(strict_types=1);

namespace Warface\Interfaces\Methods;

interface UserInterface
{
    /**
     * This method returns player statistics.
     *
     * @param string $name
     * @param int $format
     * @return array
     */
    public function stat(string $name, int $format = 0): array;

    /**
     * This method returns data about the player's achievements.
     *
     * @param string $name
     * @return array
     */
    public function achievements(string $name): array;
}
