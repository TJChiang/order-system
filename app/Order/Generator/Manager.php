<?php

namespace App\Order\Generator;

use Illuminate\Support\Manager as SupportManager;
use RangeException;

class Manager extends SupportManager
{
    /**
     * @throws RangeException
     */
    public function getDefaultDriver(): string
    {
        throw new RangeException('No default driver.');
    }

    public function createOfficialDriver(): Generator
    {
        return $this->getContainer()->make(OfficialGenerator::class);
    }

    public function createAmazonDriver(): Generator
    {
        return $this->getContainer()->make(AmazonGenerator::class);
    }

    public function createMomoDriver(): Generator
    {
        return $this->getContainer()->make(MomoGenerator::class);
    }

    public function createHktvmallDriver(): Generator
    {
        return $this->getContainer()->make(HktvmallGenerator::class);
    }
}
