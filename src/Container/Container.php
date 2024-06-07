<?php

namespace App\Container;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use App\Container\IContainer;

class Container implements IContainer
{
  private array $bindings = [];
  private array $instances = [];
  private array $singletons = [];

  public function set($id, $factory)
  {
    $this->bindings[$id] = $factory;
  }

  public function singleton($id, $factory)
  {
    $this->singletons[$id] = $factory;
  }

  public function get($id)
  {
    // Check if the instance is already created
    if (isset($this->instances[$id])) {
      return $this->instances[$id];
    }

    // Check if the service is a singleton
    if (isset($this->singletons[$id])) {
      $factory = $this->singletons[$id];
      $instance = $factory($this);
      $this->instances[$id] = $instance;
      return $instance;
    }

    // Check if the service is bound
    if (isset($this->bindings[$id])) {
      $factory = $this->bindings[$id];
      return $factory($this);
    }

    throw new \InvalidArgumentException("No binding found for [$id].");
  }

  public function build(string $class)
  {
    if (!class_exists($class)) {
      throw new \Exception("Class \"$class\" does not exist.");
    }

    $reflector = new ReflectionClass($class);
    if (!$reflector->isInstantiable()) {
      throw new \Exception("Target [$class] is not instantiable.");
    }

    $constructor = $reflector->getConstructor();
    if ($constructor === null) {
      return new $class;
    }

    $parameters = $constructor->getParameters();
    $dependencies = [];

    foreach ($parameters as $parameter) {
      $type = $parameter->getType();
      if ($type === null || $type->isBuiltin()) {
        if ($parameter->isDefaultValueAvailable()) {
          $dependencies[] = $parameter->getDefaultValue();
        } else {
          throw new \Exception("Unresolvable dependency [{$parameter->getName()}] in class {$parameter->getDeclaringClass()->getName()}");
        }
      } else {
        $dependencies[] = $this->get($type->getName());
      }
    }

    return $reflector->newInstanceArgs($dependencies);
  }

  public function callMethod($object, string $method, array $parameters = [])
  {
    $reflector = new ReflectionMethod($object, $method);
    $methodParameters = $reflector->getParameters();
    $dependencies = [];

    foreach ($methodParameters as $index => $parameter) {
      $name = $parameter->getName();
      $type = $parameter->getType();

      if ($index === 0 && $type && isset($this->instances[$type->getName()])) {
        $dependencies[] = $this->instances[$type->getName()];
      } elseif (array_key_exists($name, $parameters)) {
        $dependencies[] = $parameters[$name];
      } elseif ($type === null || $type->isBuiltin()) {
        if ($parameter->isDefaultValueAvailable()) {
          $dependencies[] = $parameter->getDefaultValue();
        } else {
          throw new \RuntimeException("Unresolvable dependency [{$name}] in method [$method]");
        }
      } else {
        $dependencies[] = $this->get($type->getName());
      }
    }

    return $reflector->invokeArgs($object, $dependencies);
  }
}

