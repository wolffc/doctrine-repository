Doctrine Repository
===================
Enables TYPO3 to use External Databases via Doctrine ORM


Setup
-----

### 1. Database Configuration

Create a configuration for your Cutstom DB `AdditionalConfiguration.php` we use `myDb` as a Identifier in the 
examples below
```php
$GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['myDb'] = [
    'charset' => 'utf8',
    'dbname' => 'TYPO3_DATABASE_DBNAME',
    'driver' => 'mysqli',
    'host' => 'TYPO3_DATABASE_HOST',
    'password' => 'TYPO3_DATABASE_PASSWORD',
    'port' => 3306,
    'user' => 'TYPO3_DATABASE_USERNAME',
];
```

### 2. Repository Definition

Create an Repository do get Access to the Database Connection by Extending 
`Wolffc\DocrineRepository\Persistence\AbstractDoctrineBasedRepository`

see example below:

```php
<?php


namespace MyCompany\MyExtension\Repository;


class MyCustomerRepository extends Wolffc\DocrineRepository\Persistence\AbstractDoctrineBasedRepository
{
    public function getDatabaseIdentifier()
    {
        return 'myDb';
    }
    
   
}

```

### 3. Domain Models

Create Domain Models which use Doctrine Annotations. see example Domain Object Below

```php
<?php

namespace MyCompany\MyExtension\Domain\Model;


/**
 * @Entity @Table(name="table_kunden")
 **/
class Customer extends Wolffc\DocrineRepository\DomainObject\AbstractDoctrineDomainObject
{

    /**
     * @var string
     * @Id @Column(type="integer", name="KundenNummer")
     */
    protected $customerNumber;

    /**
     * @var string
     * @Column(type="string", name="Name")
     */
    protected $name;
    

    /**
     * @var MyCompany\MyExtension\Domain\Model\ServiceOffice
     * @ManyToOne(targetEntity="Wolffc\DocrineRepository\Domain\Model\ServiceOffice")
     * @JoinColumn(name="Filiale", referencedColumnName="ID")
     */
    protected $serviceOffice;

 // ... define getters and Setters for Properties as usual ... 
    

    
```

### 4. Repository usage in Controllers

Start Using the Repository in Your Controller as you would do with any Extbase Repository


```php
<?php

    namespace MyCompany\MyExtension\Controller;
    
    use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
    use MyCompany\MyExtension\Repository\MyCustomerRepository;

    class CustomerController extends ActionController
    {
        /**
         * @var \MyCompany\MyExtension\Repository\MyCustomerRepository
         * */
        protected $customerRepository;
        
        public function listAction() {
            $customers = $this->customersRepository->findAll();
        }
```

Form ViewHelper
---------------
the Extension Provides an Form ViewHelper which is Able to handle Doctrine Based Object.
inside this form viewHelper you can use the regular Extbase/Fluid Form viewHelpers
See an Example Below:

```html
{namespace doctrine=Wolffc\DocrineRepository\ViewHelpers}

<div>
     <doctrine:form action="updateCustomer" object="{customer}" name="customer">
        <label>Name: <f:form.textfield property="name" /></label>
        <f:form.submit value="Submit"></f:form.submit> 
     </doctrine:form>
</div>

```

IMPORTANT NOTES: Persistance with Doctrine
------------------------------------------

currently if your call `persistAll()` method on a Repository all Objects Using the DoctrineRepositoy will be Persisted as 
this function calls  `$this->entityManager->flush();` not only Objects in this Repository!

if you are not careful this might lead to unintended Persistance of Objects you do not want to persist / modify.
