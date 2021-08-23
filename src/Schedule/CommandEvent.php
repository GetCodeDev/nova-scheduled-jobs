<?php

namespace Llaski\NovaScheduledJobs\Schedule;

use Illuminate\Console\Parser;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Arr;

class CommandEvent extends Event
{
    public function command()
    {
        preg_match("/artisan.*?\s(.*)/", $this->event->command, $matches);

        return $matches[1] ?? null;
    }

    public function className()
    {
        [$command] = Parser::parse($this->command());

        $commands = app(Kernel::class)->all();

        if (!isset($commands[$command])) {
            return '';
        }

        return get_class($commands[$command]);
    }

    public function description()
    {
        try {
            if ($this->event->description) {
                return $this->event->description;
            }

            $reflection = new \ReflectionClass($this->className());
            return (string) Arr::get($reflection->getDefaultProperties(), 'description', '');
        } catch (\ReflectionException $exception) {
            return '';
        }
    }

}
