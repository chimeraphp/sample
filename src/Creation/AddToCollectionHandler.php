<?php
declare(strict_types=1);

namespace Lcobucci\MyApi\Creation;

use Chimera\Mapping as Chimera;
use Lcobucci\MyApi\Book;
use Lcobucci\MyApi\Books;

/**
 * @Chimera\Routing\CreateEndpoint(
 *     path="/books",
 *     command=AddToCollection::class,
 *     name="book.create",
 *     redirectTo="book.fetch_one"
 * )
 */
final class AddToCollectionHandler
{
    public function __construct(private readonly Books $collection)
    {
    }

    /** @Chimera\ServiceBus\CommandHandler */
    public function handle(AddToCollection $command): void
    {
        $book = new Book(
            $command->id,
            $command->title,
            $command->author
        );

        $this->collection->append($book);
    }
}
