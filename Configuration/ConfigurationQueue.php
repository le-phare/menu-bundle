<?php

namespace Lephare\Bundle\MenuBundle\Configuration;

use SplPriorityQueue;

class ConfigurationQueue extends SplPriorityQueue
{
    public function __construct()
    {
        $this->setExtractFlags(SplPriorityQueue::EXTR_DATA);
    }

    public function compare($p1, $p2)
    {
        if ($p1 === $p2) {
            return 0;
        }
        return $p1 > $p2 ? -1 : 1;
    }
}
