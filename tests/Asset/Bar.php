<?php

namespace Stratadox\Di\Test\Asset;

class Bar implements BarInterface
{
    public function doSomethingUseful()
    {
        return 'something-useful';
    }
}