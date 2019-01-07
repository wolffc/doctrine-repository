<?php
namespace Wolffc\DocrineRepository\ViewHelpers;

/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 *                                                                        *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
use Wolffc\DocrineRepository\DomainObject\AbstractDoctrineDomainObject;

/**
 * Form view helper. Generates a <form> Tag.
 *
 * = Basic usage =
 *
 * Use <f:form> to output an HTML <form> tag which is targeted at the specified
 * action, in the current controller and package.
 * It will submit the form data via a POST request. If you want to change this,
 * use method="get" as an argument.
 * <code title="Example">
 * <f:form action="...">...</f:form>
 * </code>
 *
 * = A complex form with a specified encoding type =
 *
 * <code title="Form with enctype set">
 * <f:form action=".." controller="..." package="..." enctype="multipart/form-data">...</f:form>
 * </code>
 *
 * = A Form which should render a domain object =
 *
 * <code title="Binding a domain object to a form">
 * <f:form action="..." name="customer" object="{customer}">
 * <f:form.hidden property="id" />
 * <f:form.textbox property="name" />
 * </f:form>
 * </code>
 * This automatically inserts the value of {customer.name} inside the textbox
 * and adjusts the name of the textbox accordingly.
 */
class Form extends \TYPO3\CMS\Fluid\ViewHelpers\FormViewHelper
{

    /**
     * Renders a hidden form field containing the technical identity of the given object.
     *
     * @param object $object Object to create the identity field for
     * @param string $name Name
     *
     * @return string A hidden field containing the Identity (UID in TYPO3 Flow, uid in
     * Extbase) of the given object or NULL if the
     * object is unknown to the persistence framework
     * @see \TYPO3\CMS\Extbase\Mvc\Controller\Argument::setValue()
     */
    protected function renderHiddenIdentityField($object, $name)
    {
        if (!is_object($object)
            || !($object instanceof AbstractDoctrineDomainObject)
            || ($object->_isNew())) {
            return '';
        }
        // Intentionally NOT using PersistenceManager::getIdentifierByObject here!!
        // Using that one breaks re-submission of data in forms in case of an error.
        $identifier = $object->getUid();
        if ($identifier === null) {
            return LF . '<!-- Object of type ' . get_class($object) . ' is without identity -->' . LF;
        }
        $name = $this->prefixFieldName($name) . '[__identity]';
        $this->registerFieldNameForFormTokenGeneration($name);

        return LF . '<input type="hidden" name="' . $name . '" value="' . $identifier . '" />' . LF;
    }
}
