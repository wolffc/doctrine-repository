<?php
namespace Wolffc\DocrineRepository\Property\TypeConverter;

/*                                                                        *
 * This script belongs to the Extbase framework                           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
use Wolffc\DocrineRepository\DomainObject\AbstractDoctrineDomainObject;
use Wolffc\DocrineRepository\Persistence\AbstractDoctrineBasedRepository;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;

/**
 * This converter transforms arrays or strings to objects managed by doctrine.
 */
class DoctrineObjectConverter extends PersistentObjectConverter
{

    /**
     * @var array
     */
    protected $sourceTypes = array('integer', 'string', 'array');

    /**
     * @var string
     */
    protected $targetType = 'object';

    /**
     * @var int
     */
    protected $priority = 25;


    /**
     * We can only convert if the $targetType is either tagged with entity or value object.
     *
     * @param mixed $source
     * @param string $targetType
     * @return bool
     */
    public function canConvertFrom($source, $targetType)
    {
        return is_subclass_of($targetType, AbstractDoctrineDomainObject::class);
    }

    /**
     * Fetch an object from persistence layer.
     *
     * @param mixed $identity
     * @param string $targetType
     * @throws \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\InvalidSourceException
     * @return object
     */
    protected function fetchObjectFromPersistence($identity, $targetType)
    {
        $repositoryClassName = ClassNamingUtility::translateModelNameToRepositoryName($targetType);
        $repository = $this->objectManager->get($repositoryClassName);

        if ($repository instanceof AbstractDoctrineBasedRepository) {
            $object = $repository->findByIdentifier($identity);
        } else {
            throw new \TYPO3\CMS\Extbase\Property\Exception\InvalidSourceException(
                sprintf(
                    'Repository for object type %s not found or not an instance of AbstractDoctrineBasedRepository.',
                    $targetType
                ),
                1440086222
            );
        }

        if ($object === null) {
            throw new \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException(
                sprintf(
                    'Object of type %s with identity "%s" not found.',
                    $targetType,
                    print_r($identity, true)
                ),
                1440086223
            );
        }
        return $object;
    }
}
