<?php

namespace Nebalus\Webapi\Api\Filter\Linktree\Analytics;

use Nebalus\Webapi\Api\Filter\AbstractFilter;
use Override;

class LinktreeClickFilter extends AbstractFilter
{
    #[Override] public function filterAndCheckIfStructureIsValid(array $params): bool
    {
        $requiredParams = [];
        if ($this->checkIfAnyRequiredParamsAreMissing($requiredParams, $params)) {
            return false;
        }

        $this->data = [];

        return true;
    }
}