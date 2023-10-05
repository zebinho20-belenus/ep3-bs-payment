<?php

namespace Base\View\Helper;

use Base\Manager\OptionManager;
use Zend\View\Helper\AbstractHelper;

class PriceFormat extends AbstractHelper
{

    protected $optionManager;

    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager;
    }

    public function __invoke($price, $rate = null, $gross = null, $perTime = null, $perQuantity = null, $perText = null, $break = true)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<span class="symbolic symbolic-tag">';

        $html .= '<b>' . $view->currencyFormat($price / 100) . '</b>';

        if ($perText) {
            $html .= ' ' . $view->t($perText);
        }

        if ($perTime || $perQuantity) {
            $html .= ' / ';

            if ($perTime) {
                $html .= $view->prettyTime($perTime);
            }

            if ($perTime && $perQuantity) {
                $html .= ' &amp; ';
            }

            if ($perQuantity) {
                $html .= $this->optionManager->need('subject.square.unit');
            }
        }

        $html .= '</span>';

        return $html;
    }

}