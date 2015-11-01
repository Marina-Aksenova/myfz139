<?php
namespace Application\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\LabelAwareInterface;
use Zend\Form\View\Helper\FormRow as FormRowHelper;

class FormRow extends FormRowHelper
{
    /**
     * @inheritdoc
     */
    public function render(ElementInterface $element, $labelPosition = null)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $elementHelper = $this->getElementHelper();

        $label = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();

        if (isset($label) && '' !== $label) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate($label, $this->getTranslatorTextDomain());
            }
        }

        // Does this element have errors ?
        if (count($element->getMessages()) > 0 && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        $elementErrors = '';
        if ($this->renderErrors) {
            $elementErrors = implode('<br />', $element->getMessages());
        }

        // hidden elements do not need a <label> -https://github.com/zendframework/zf2/issues/5607
        $type = $element->getAttribute('type');

        switch ($type) {
            case 'button':
            case 'submit':
                $elementString = $elementHelper->render($element->setAttribute('class', 'btn btn-default'));
                break;
            case 'password':
            case 'text':
                $elementString = $elementHelper->render($element->setAttribute('class', 'form-control'));
                break;
            case 'radio':
                $elementString = '';
                foreach ($element->getValueOptions() as $name => $radioLabel) {
                    $elementString .= sprintf('
                        <div class="radio">
                            <label>
                                <input type="radio" name="%s" value="%s" %s>
                                %s
                            </label>
                        </div>
                    ',
                        $element->getName(),
                        $name,
                        $element->getValue() == $name ? ' checked' : '',
                        $radioLabel);
                }
                break;
            case 'multi_checkbox':
                $elementString = '';
                foreach ($element->getValueOptions() as $name => $radioLabel) {
                    $elementString .= sprintf('
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="%s" value="%s" %s>
                                %s
                            </label>
                        </div>
                    ',
                        $element->getName(),
                        $name,
                        $element->getValue() == $name ? ' checked' : '',
                        $radioLabel);
                }
                break;
            default:
                $elementString = $elementHelper->render($element);
                break;
        }

        if ($type === 'hidden') {
            return $elementString;
        }

        if ($label) {

            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            // If element has id attribute
            $labelFor = '';
            if ($elementId = $element->getAttribute('id')) {
                $labelFor = $elementId;
            }

            $markup = sprintf('
                <div class="form-group%s">
                    <label%s class="col-sm-2 control-label">%s</label>
                    <div class="col-sm-10">
                        %s
                        <p class="help-block help-block-error">
                            %s
                        </p>
                    </div>
                </div>',
                ($elementErrors ? ' has-error' : ''),
                ($labelFor ? ' for="' . $labelFor . '"' : ''),
                $label,
                $elementString,
                $elementErrors
            );

        } else {
            $markup = sprintf('
                        <div class="form-group' . ($elementErrors ? ' has-error' : '') . '">
                            <div class="col-sm-offset-2 col-sm-10">
                                %s
                                <p class="help-block help-block-error">
                                    %s
                                </p>
                            </div>
                        </div>',
                $elementString,
                $elementErrors
            );
        }

        return $markup;
    }

}
