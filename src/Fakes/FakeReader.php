<?php

namespace TeamZac\LaravelShapefiles\Fakes;

use Illuminate\Support\LazyCollection;
use Illuminate\Support\Traits\ForwardsCalls;
use TeamZac\LaravelShapefiles\Contracts\ReaderContract;

class FakeReader implements ReaderContract
{
    use ForwardsCalls;

    protected ?string $prj = null;

    protected array $fieldNames = [];

    protected array $records = [];

    protected LazyCollection $collection;

    public static function make(): static
    {
        return new static;
    }

    public function withPrj(string $prj): static
    {
        $this->prj = $prj;
        return $this;
    }

    public function withFieldNames(array $fieldNames): static
    {
        $this->fieldNames = $fieldNames;
        return $this;
    }

    public function withRecords(array $records): static
    {
        $this->records = $records;
        return $this;
    }

    public function count(): int
    {
        return count($this->records);
    }

    public function getPrj(): ?string
    {
        return $this->prj;
    }

    public function getFieldNames(): array
    {
        return $this->fieldNames;
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->getCollection(), $method, $parameters);
    }

    protected function getCollection(): LazyCollection
    {
        if (! isset($this->collection)) {
            $records = $this->records;
            $this->collection = LazyCollection::make(function () use ($records) {
                foreach ($records as $record) {
                    yield $record;
                }
            });
        }

        return $this->collection;
    }
}
