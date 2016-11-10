<?php

namespace Stratadox\Di\Test\Asset;

class Baz implements BarInterface
{
    public function doSomethingUseful()
    {
        return 'something-equally-useful';
    }
}