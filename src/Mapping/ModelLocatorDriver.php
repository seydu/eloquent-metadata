<?php
/**
 * Created by PhpStorm.
 * User: seydu
 * Date: 4/13/18
 * Time: 3:57 PM
 */

namespace Seydu\EloquentMetadata\Mapping;


class ModelLocatorDriver implements DriverInterface
{
    /**
     * @var array
     */
    private $paths;

    /**
     * @var array|null
     */
    private $classNames;

    public function __construct(array $paths)
    {
        $this->paths  = $paths;
    }

    private function loadClassNames()
    {
        if ($this->classNames !== null) {
            return $this;
        }

        if (!$this->paths) {
            throw new MappingException("Specifying a path to your models is required");
        }

        $classes = [];
        $includedFiles = [];
        foreach ($this->paths as $path) {
            if ( ! is_dir($path)) {
                throw new MappingException("Model mapping path '$path' is not a valid directory");
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($iterator as $file) {
                $sourceFile = realpath($file);
                require_once $sourceFile;

                $includedFiles[] = $sourceFile;
            }
        }

        $declared = get_declared_classes();
        foreach ($declared as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            if (!in_array($sourceFile, $includedFiles)) {
                continue;
            }
            //@todo: do we need to require models to extend Laravels' bas model?
            //if so use a test like $rc->isSubclassOf(Model::class) before adding
            //$className to $classes
            $classes[] = $className;
        }
        $this->classNames = $classes;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function loadMetadataForClass($className, ClassMetadataInterface $metadata)
    {
    }

    /**
     * @inheritdoc
     */
    public function getAllClassNames()
    {
        return $this->loadClassNames()->classNames;
    }
}