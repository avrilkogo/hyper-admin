<?php

declare(strict_types=1);

namespace App\Annotation;

use App\Init\AuthInit;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Hyperf\Di\Annotation\AnnotationCollector;

/**
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
class Auth extends AbstractAnnotation
{
    public $auth;

    public $module;

    public function collectMethod(string $className, ?string $target): void
    {
        $routeName = AuthInit::makeKey($className, $target);
        AuthInit::addAuth($routeName,$this->auth, $this->module);
        AnnotationCollector::collectMethod($className, $target, static::class, $this);
    }
}