<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/13/18
 * Time: 3:57 PM
 */

namespace Seydu\EloquentMetadata\Mapping;


class Dumper implements DumperInterface
{

    public function dump(ClassMetadataFactoryInterface $factory)
    {
        return serialize($factory->getAllMetadata());
    }
}