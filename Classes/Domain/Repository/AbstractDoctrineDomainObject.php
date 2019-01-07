<?php
namespace Wolffc\DocrineRepository\Domain\Repository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Type\TypeInterface;

/**
 * A generic Domain Object.
 *
 * All Model domain objects need to inherit from either AbstractEntity or
 * AbstractValueObject, as this provides important framework information.
 */
abstract class AbstractDoctrineDomainObject
{

    /**
     * Getter for uid.
     *
     * @return mixed the unique identifier or NULL if none set yet.
     */
    abstract public function getUid();

    /**
     * Returns the class name and the uid of the object as string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getUid();
    }

    /**
     * Returns TRUE if the object is new (the uid was not set, yet). Only for internal use
     *
     * @return bool
     */
    // phpcs:ignore
    public function _isNew()
    {
        return $this->getUid() === null;
    }
}
