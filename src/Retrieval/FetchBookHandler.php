<?php
declare(strict_types=1);

namespace Lcobucci\MyApi\Retrieval;

use Chimera\Mapping as Chimera;
use Lcobucci\MyApi\Book;
use Lcobucci\MyApi\Books;

/**
 * @Chimera\Routing\FetchEndpoint(path="/books/{id}", query=FetchBook::class, name="book.fetch_one")
 * @Chimera\ServiceBus\QueryHandler(handles=FetchBook::class)
 */
final class FetchBookHandler
{
    /**
     * @var Books
     */
    private $books;

    public function __construct(Books $books)
    {
        $this->books = $books;
    }

    public function handle(FetchBook $query): Book
    {
        return $this->books->find($query->id);
    }
}
