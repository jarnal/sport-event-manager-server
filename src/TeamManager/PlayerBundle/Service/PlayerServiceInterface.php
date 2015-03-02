<?php

namespace TeamManager\PlayerBundle\Service;

interface PlayerServiceInterface
{
    /**
     * Get a Player by given id.
     *
     * @param int $id
     */
    public function get($id);

    /**
     * Post a player form, creates a new Player.
     *
     * @param array $parameters
     */
    public function post(array $parameters);
}