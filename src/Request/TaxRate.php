<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Request;

enum TaxRate: string
{
    /**
     * без НДС
     */
    case NONE = 'none';

    /**
     * НДС по ставке 0%
     */
    case VAT_0 = 'vat0';

    /**
     * НДС чека по ставке 10%
     */
    case VAT10 = 'vat10';

    /**
     * НДС чека по ставке 18%
     */
    case VAT18 = 'vat18';

    /**
     * НДС чека по ставке 20%
     */
    case VAT20 = 'vat20';

    /**
     * НДС чека по расчетной ставке 10/110
     */
    case VAT110 = 'vat110';

    /**
     * НДС чека по расчетной ставке 18/118
     */
    case VAT118 = 'vat118';

    /**
     * НДС чека по расчетной ставке 20/120
     */
    case VAT120 = 'vat120';
}
