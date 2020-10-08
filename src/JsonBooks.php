<?php
declare(strict_types=1);

namespace Lcobucci\MyApi;

use function fputs;
use function fwrite;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function array_filter;
use function array_values;

final class JsonBooks implements Books
{
    private string $filename;

    /** @var Book[] */
    private array $items = [];

    public function __construct(string $filename)
    {
        $this->filename = $filename;

        if (file_exists($filename)) {
            $this->items = $this->fromContent(file_get_contents($this->filename));
        }
    }

    public function __destruct()
    {
        $content = $this->toContent();

        if (! file_exists($this->filename) || sha1($content) !== sha1_file($this->filename)) {
            file_put_contents($this->filename, $content, LOCK_EX);
        }
    }

    private function fromContent(string $json): array
    {
        $items = [];

        foreach (json_decode($json, true, 512, JSON_THROW_ON_ERROR) as $item) {
            $items[$item['id']] = $this->convertItemToObject($item);
        }

        return $items;
    }

    private function toContent(): string
    {
        $data = array_map(
            [$this, 'convertObjectToItem'],
            array_values($this->items)
        );

        return json_encode($data);
    }

    private function convertItemToObject(array $item): Book
    {
        return new Book(
            Uuid::fromString($item['id']),
            $item['title'],
            $item['author']
        );
    }

    private function convertObjectToItem(Book $object): array
    {
        return [
            'id'     => (string) $object->getId(),
            'title'  => $object->getTitle(),
            'author' => $object->getAuthor(),
        ];
    }

    public function append(Book $book): void
    {
        $this->items[(string) $book->getId()] = $book;
    }

    public function find(UuidInterface $id): Book
    {
        if (! isset($this->items[(string) $id])) {
            throw new \OutOfBoundsException('Book not found');
        }

        return $this->items[(string) $id];
    }

    public function findAll(?string $title = null, ?string $author = null): array
    {
        $items = array_values($this->items);

        if ($title === null && $author === null) {
            return $items;
        }

        return array_filter(
            $items,
            static fn (Book $book): bool =>
                ($title && mb_stripos($book->getTitle(), $title) !== false)
                || ($author && mb_stripos($book->getAuthor(), $author) !== false)
        );
    }
}
