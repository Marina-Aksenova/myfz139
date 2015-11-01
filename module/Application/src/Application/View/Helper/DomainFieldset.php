<?php
namespace Application\View\Helper;

use Lists\Fieldset\DomainFieldset as DomainFieldsetForm;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Collection as CollectionElement;
use Zend\Form\View\Helper\FormElement;
use Zend\Form\View\Helper\FormRow;

class DomainFieldset extends FormRow
{
    public function __invoke(ElementInterface $element = null, $labelPosition = null, $renderErrors = null, $partial = null)
    {
        return $this->render($element);
    }

    /**
     * @inheritdoc
     */
    public function render(ElementInterface $element, $labelPosition = null)
    {
        /** @var \Lists\Fieldset\DomainFieldset $element */

        $elementHelper = $this->getElementHelper();
        $buttonHelper = $this->view->plugin('form_button');

        /** @var Element $resourceElement */
        $resourceElement = $element->get('resource');
        $resourceElement->setAttribute('placeholder', 'Домен');
        $resourceElement->setAttribute('class', 'form-control input-resource');
        $resourceElementErrors = implode('<br>', $resourceElement->getMessages());

        /** @var Element $descriptionElement */
        $descriptionElement = $element->get('description');
        $descriptionElement->setAttribute('placeholder', 'Описание');
        $descriptionElement->setAttribute('class', 'col-sm-10 form-control input-resource');

        /** @var Element $descriptionElement */
        $buttonElement = $element->get('remove');
        $buttonElement->setAttribute('class', 'btn btn-sm glyphicon glyphicon-remove  domain-remove');

        return '
            <div class="form-horizontal form-domain-collection clearfix">
                <label class="' . ($resourceElementErrors ? ' has-error' : '') . '">
                    <span class="col-sm-2">Домен</span>
                    <div class="col-sm-10 input-div">
                        <div>' .
                            $elementHelper->render($resourceElement) . '
                        </div>
                        <div>
                            <p class="help-block help-block-error"> ' .
                                $resourceElementErrors . '
                            </p>
                        </div>
                    </div>
                </label>
                <label>
                    <span class="col-sm-2">Описание</span>' .
                    $elementHelper->render($descriptionElement) . '
                </label>' .
                $buttonHelper->render($buttonElement, '') . '
            </div>
        ';
    }
}