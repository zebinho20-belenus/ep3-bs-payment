<?php

namespace Base\View\Helper;

use Base\Manager\OptionManager;
use Zend\View\Helper\AbstractHelper;

class PriceFormatPlain extends AbstractHelper
{

    protected $optionManager;

    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager;
    }

    public function __invoke($price, $rate = null, $gross = null, $perTime = null, $perQuantity = null, $perText = null, $break = false)
    {
        $view = $this->getView();
        $text = '';

        $text .= ' ' . $view->currencyFormat($price / 100) . ' ';

        if ($perText) {
            $text .= ' ' . $view->t($perText);
        }

        if ($perTime || $perQuantity) {
            $text .= ' / ';

            if ($perTime) {
                $text .= $view->prettyTime($perTime);
            }

            if ($perTime && $perQuantity) {
                $text .= ' & ';
            }

            if ($perQuantity) {
                $text .= $this->optionManager->need('subject.square.unit');
            }
        }

        if ($rate && $gross) {

            if ($break) {
                $text .= "\n";
            } else {
                $text .= '  ';
            }

            if ($gross) {
                $grossFormulation = $view->t('incl.');
            } else {
                $grossFormulation = $view->t('plus');
            }

            $text .= sprintf('%s %s%% %s',
                $grossFormulation, $rate, $view->t('VAT'));
        }

        return $text;
    }

}
