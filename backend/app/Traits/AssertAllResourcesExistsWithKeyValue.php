<?php

namespace App\Traits;

trait AssertAllResourcesExistsWithKeyValue
{
  /**
   * This will assert that a every $resource has the $key with $value on it
   * @param string $key What you are searching for
   * @param $value The value that you are expect to find
   * @param array $resources The array that contains your keys
   */
  public function assertAllResourcesExistsWithKeyValue(string $key, $value, array $resources, string $message = '')
  {
    $found = true;
    foreach ($resources as $resource) {
      if (array_key_exists($key, $resource) && $resource[$key] !== $value) {
        $found = false;
        break;
      }
    }
    $this->assertTrue($found, $message);
  }
  /**
   * Ensures that each resource does not contain the specified key with the given value
   * @param string $key What you are searching for
   * @param $value The value that you are expect to find
   * @param array $resources The array that contains your keys
   */
  public function assertResourcesNotHaveKeyWithValue(string $key, $value, array $resources, string $message = '')
  {
    $found = true;
    foreach ($resources as $resource) {
      if (array_key_exists($key, $resource) && $resource[$key] === $value) {
        $found = false;
        break;
      }
    }
    $this->assertTrue($found, $message);
  }
  public function assertResourceHaveKeyWithValue(string $key, $value, array $resources, string $message = '')
  {
    $found = false;
    foreach ($resources as $resource) {
      if (array_key_exists($key, $resource) && $resource[$key] === $value) {
        $found = true;
        break;
      }
    }
    $this->assertTrue($found, $message);
  }
}
