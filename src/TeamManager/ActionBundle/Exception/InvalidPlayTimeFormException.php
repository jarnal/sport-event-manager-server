<?php

namespace TeamManager\ActionBundle\Exception;

use TeamManager\CommonBundle\Exception\InvalidEntityFormException;

/**
 * Class InvalidFormException
 * @package TeamManager\PlayerBundle\Exception
 */
class InvalidPlayTimeFormException extends InvalidEntityFormException
{

    /**
     * @param string $message
     * @param null $form
     */
    public function __construct($message, $form = null)
    {
        parent::__construct($message, $form);
    }

}