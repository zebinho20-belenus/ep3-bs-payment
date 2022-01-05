<?php

namespace Backend\View\Helper\SquareControl;

use Zend\View\Helper\AbstractHelper;

class DoorCodesFormat extends AbstractHelper
{

    public function __invoke($codes)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<table class="bordered-table">';

        $html .= '<tr class="gray">';
        $html .= '<th>' . $view->t('Name') . '</th>';
        $html .= '<th>' . $view->t('Date (Start)') . '</th>';
        $html .= '<th>' . $view->t('Date (End)') . '</th>';
        $html .= '<th>' . $view->t('Time') . '</th>';
        $html .= '<th class="no-print">&nbsp;</th>';
        $html .= '</tr>';

        foreach ($codes as $code) {
            $html .= $view->backendDoorCodeFormat($code);
        }

        $html .= '</table>';

        $html .= '<style type="text/css"> .priority-col, .actions-col { border: none !important; } </style>';

        // $view->headScript()->appendFile($view->basePath('js/controller/backend/square-control/index.min.js'));

        return $html;
    }

}
