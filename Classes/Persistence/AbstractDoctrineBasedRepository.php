<?php
namespace Wolffc\DocrineRepository\Persistence;

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
use Doctrine\Common\EventManager;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\NotImplementedException;

/**
 * Abstract repository implementing the basic repository methods
 */
abstract class AbstractDoctrineBasedRepository implements
    \TYPO3\CMS\Extbase\Persistence\RepositoryInterface,
    \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $objectType;

    /**
     * @var array
     */
    protected $defaultOrderings = array();

    /**
     * @var \Wolffc\DocrineRepository\Persistence\DoctrineEntityManagerManager
     *
     */
    protected $entityManagerManager;
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EntityRepository
     */
    protected $entityRepository;

    /**
     * Constructs a new Repository
     *
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     * @throws \Exception
     */
    public function __construct(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->objectType = ClassNamingUtility::translateRepositoryNameToModelName($this->getRepositoryClassName());

        $this->entityManagerManager = $this->objectManager->get(DoctrineEntityManagerManager::class);
        $this->entityManagerManager->setConnectionConfiguration(
            $this->getDatabaseIdentifier(),
            $this->getDatabaseConfiguration()
        );

        $this->entityManager = $this->entityManagerManager->getEntityManagerForDatabase($this->getDatabaseIdentifier());
        $this->attachEventListeners($this->entityManager->getEventManager());
        $this->entityRepository = $this->entityManager->getRepository($this->objectType);
    }

    /**
     * Attaches Doctrine EventsListeners if the are Configured
     * @param EventManager $eventManager
     * @return bool
     * @throws \Exception
     */
    protected function attachEventListeners(EventManager $eventManager)
    {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['docrine_repository']['eventListeners'])) {
            return false;
        }
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['docrine_repository']['eventListeners'] as
            $eventName => $classses) {
            if (!is_array($classses)) {
                throw new \Exception(
                    'TYPO3_CONF_VARS.EXTCONF.docrine_repository.eventListeners.' .
                    $eventName . ' Must be of Type Array ',
                    1489062657628
                );
            }
            foreach ($classses as $className) {
                $classInstance = $this->objectManager->get($className);
                $eventManager->addEventListener($eventName, $classInstance);
            }
        }
    }


    /**
     * @return array
     * @throws \Exception
     */
    public function getDatabaseConfiguration()
    {
        return DatabaseConfigurationService::getDatabaseConfiguration($this->getDatabaseIdentifier());
    }

    /**
     * @return string
     */
    abstract public function getDatabaseIdentifier();

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     *
     */
    public function persistAll()
    {
        $this->entityManager->flush();
    }

    /**
     * Adds an object to this repository.
     *
     * @param object $object The object to add
     * @return void
     * @api
     */
    public function add($object)
    {
        $this->entityManager->persist($object);
    }

    /**
     * Removes an object from this repository.
     *
     * @param object $object The object to remove
     * @return void
     * @api
     */
    public function remove($object)
    {
        $this->entityManager->remove($object);
    }

    /**
     * Replaces an existing object with the same identifier by the given object
     *
     * @param object $modifiedObject The modified object
     * @api
     */
    public function update($modifiedObject)
    {
        $this->entityManager->persist($modifiedObject);
    }

    /**
     * Returns all objects of this repository.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array The query result
     * @api
     */
    public function findAll()
    {
        return $this->entityRepository->findAll();
    }

    /**
     * Returns the total number objects of this repository.
     *
     * @return int The object count
     * @api
     */
    public function countAll()
    {
        $query = $this->entityManager->createQuery('SELECT COUNT(*) FROM ' . $this->objectType);
        return $query->getSingleScalarResult();
    }

    /**
     * Removes all objects of this repository as if remove() was called for
     * all of them.
     *
     * @return void
     * @api
     */
    public function removeAll()
    {
        // TODO: Implement removeAll() method.
    }

    /**
     * Finds an object matching the given identifier.
     *
     * @param int $uid The identifier of the object to find
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    public function findByUid($uid)
    {
        $object = $this->findByIdentifier($uid);
        return $object;
    }

    /**
     * Finds an object matching the given identifier.
     *
     * @param mixed $identifier The identifier of the object to find
     * @return object The matching object if found, otherwise NULL
     * @api
     */
    abstract public function findByIdentifier($identifier);

    /**
     * Sets the property names to order the result by per default.
     * Expected like this:
     * array(
     * 'foo' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
     * 'bar' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
     * )
     *
     * @param array $defaultOrderings The property names to order by
     * @return void
     * @api
     */
    public function setDefaultOrderings(array $defaultOrderings)
    {
        // TODO: Implement setDefaultOrderings() method.
    }

    /**
     * Sets the default query settings to be used in this repository
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $defaultQuerySettings
     * The query settings to be used by default
     *
     * @return void
     * @api
     */
    public function setDefaultQuerySettings(
        \TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface $defaultQuerySettings
    ) {
        // Not necessary for doctrine based repository
    }

    /**
     *
     */
    public function createQuery()
    {
        //throw new NotImplementedException('Not yet implemented for doctrine based repository', 1440088605);
        $query = $this->entityManager->createQuery('SELECT * FROM ' . $this->objectType);
        return $query;
    }

    /**
     * Returns the class name of this class.
     *
     * @return string Class name of the repository.
     */
    protected function getRepositoryClassName()
    {
        return get_class($this);
    }
}
