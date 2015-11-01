<?php
namespace Application\View\Helper;

use Zend\Form\Element;
use Zend\Form\Exception;
use Zend\Form\FormInterface;
use Zend\Form\View\Helper\Form;

class FormStatistic extends Form
{
    /**
     * @inheritdoc
     */
    public function render(FormInterface $form)
    {
        $elementHelper = $this->view->plugin('form_element');
        $datePeriod = $form->get('datePeriod');
		/** @var Element $dateCalendarFirst */
        $dateCalendarFirst = $form->get('dateCalendarFirst');
		/** @var Element $dateCalendarLast */
		$dateCalendarLast = $form->get('dateCalendarLast');
        $datePeriodHTML = $elementHelper->render($datePeriod);
        $dateCalendarFirstHTML = $elementHelper->render($dateCalendarFirst);
        $dateCalendarLastHTML = $elementHelper->render($dateCalendarLast);

        $markup = '
            <form class="form-inline statistic" method="post">

              <div class="form-group">
              ' . $datePeriodHTML . '
              </div>

              <div class="form-group">
              ' . $dateCalendarFirst->getLabel() . '
              ' . $dateCalendarFirstHTML . '
              </div>

              <div class="form-group">
			  ' . $dateCalendarLast->getLabel() . '
              ' . $dateCalendarLastHTML . '
              </div>

              <button type="submit" class="btn btn-default">Применить</button>
            </form>
        ';


        return $markup;
    }

}
