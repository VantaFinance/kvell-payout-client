<?php
/**
 * Kvell Payout Client
 *
 * @author Vlad Shashkov <v.shashkov@pos-credit.ru>
 * @copyright Copyright (c) 2023, The Vanta
 */

declare(strict_types=1);

namespace Vanta\Integration\KvellPayout\Request;

enum TaxSystem: string
{
    /**
     * Общая СН
     */
    case OSN = 'osn';

    /**
     * Упрощенная СН (доходы)
     */
    case ESN = 'esn';

    /**
     * Единый налог на вмененный доход
     */
    case ENVD = 'envd';

    /**
     * Патентная СН
     */
    case PATENT = 'patent';

    /**
     * Упрощенная СН (доходы)
     */
    case USN_INCOME = 'usn_income';

    /**
     * Упрощенная СН (доходы минус расходы)
     */
    case USN_INCOME_OUTCOME = 'usn_income_outcome';
}
