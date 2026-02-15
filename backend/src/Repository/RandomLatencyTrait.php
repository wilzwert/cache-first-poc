<?php

namespace App\Repository;

trait RandomLatencyTrait
{

    protected function randomLatency() {
        // add latency between 1 and 2 seconds
        usleep(rand(1000, 2000)*1000);
    }
}
