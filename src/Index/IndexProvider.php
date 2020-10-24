<?php declare(strict_types=1);

namespace StarDict\Index;

interface IndexProvider
{
    public function getIndexDataHandler(): IndexDataHandler;
}