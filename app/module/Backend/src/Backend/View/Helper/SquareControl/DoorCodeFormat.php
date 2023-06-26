<?php

namespace Backend\View\Helper\SquareControl;

use Zend\View\Helper\AbstractHelper;

class DoorCodeFormat extends AbstractHelper
{

    public function __invoke($code)
    {
        $view = $this->getView();
        $html = '';

        $dcid = $code->uuid;

        $html .= '<tr>';

        $html .= sprintf('<td><span class="gray">%s</span></td>',
            $code->name);

        $html .= sprintf('<td>%s</td>',
                        $view->dateFormat($code->timeFrom, \IntlDateFormatter::MEDIUM));

        $html .= sprintf('<td>%s</td>',
                        $view->dateFormat($code->timeTo, \IntlDateFormatter::MEDIUM));
        
        $html .= sprintf('<td>%s</td>',
            $view->timeRange($code->timeFrom, $code->timeTo, '%s to %s'));

        $html .= '<td class="actions-col no-print">'
              . '<a href="' . $view->url('backend/squarecontrol/deletedoorcode', ['dcid' => $dcid]) . '" class="unlined gray symbolic symbolic-cross"><span class="symbolic-label">' . $view->t('Delete') . '</span></a></td>';

        $html .= '</tr>';

        return $html;
    }

}
