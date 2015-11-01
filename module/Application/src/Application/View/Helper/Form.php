<?php
namespace Application\View\Helper;

use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;
use Zend\Form\View\Helper\Form as FormHelper;

class Form extends FormHelper
{
    /**
     * @inheritdoc
     */
    public function render(FormInterface $form)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $formContent = '';
        if ($message = $form->getOption('dangerMessage')) {
            $formContent = sprintf('<p class="bg-danger padding-16">%s</p>', $message);
        } else if ($message = $form->getOption('primaryMessage')) {
            $formContent = sprintf('<p class="bg-primary padding-16">%s</p>', $message);
        } else if ($message = $form->getOption('infoMessage')) {
            $formContent = sprintf('<p class="bg-info padding-16">%s</p>', $message);
        } else if ($message = $form->getOption('warningMessage')) {
            $formContent = sprintf('<p class="bg-warning padding-16">%s</p>', $message);
        } else if ($message = $form->getOption('successMessage')) {
            $formContent = sprintf('<p class="bg-success padding-16">%s</p>', $message);
        }

        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element);
            } else {
                $formRowHelper = new FormRow();
                $formRowHelper->setView($this->getView());
                $formContent.= $formRowHelper($element);
            }
        }

        return $this->openTag($form) . $formContent . $this->closeTag();
    }

}
