<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/13/18
 * Time: 3:57 PM
 */

namespace Seydu\EloquentMetadata\Mapping;


interface DumperInterface
{

    /**
     * @param ClassMetadataFactoryInterface $factory
     * @return string
     */
    public function dump(ClassMetadataFactoryInterface $factory);
}