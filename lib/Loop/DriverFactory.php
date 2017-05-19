<?php

namespace Amp\Loop;

// @codeCoverageIgnoreStart
class DriverFactory {
    /**
     * Creates a new loop instance and chooses the best available driver.
     *
     * @return Driver
     *
     * @throws \Error If an invalid class has been specified via AMP_LOOP_DRIVER
     */
    public function create(): Driver {
        if ($driver = $this->createDriverFromEnv()) {
            return $driver;
        }

        if (UvDriver::isSupported()) {
            return new UvDriver;
        }

        if (EvDriver::isSupported()) {
            return new EvDriver;
        }

        if (EventDriver::isSupported()) {
            return new EventDriver;
        }

        return new NativeDriver;
    }

    private function createDriverFromEnv() {
        $driver = \getenv("AMP_LOOP_DRIVER");

        if (!$driver) {
            return null;
        }

        if (!\class_exists($driver)) {
            throw new \Error(\sprintf(
                "Driver '%s' does not exist, falling back to default driver.",
                $driver
            ));
        }

        if (!\is_subclass_of($driver, Driver::class)) {
            throw new \Error(\sprintf(
                "Driver '%s' is not a subclass of '%s', falling back to default driver.",
                $driver,
                Driver::class
            ));
        }

        return new $driver;
    }
}
// @codeCoverageIgnoreEnd
