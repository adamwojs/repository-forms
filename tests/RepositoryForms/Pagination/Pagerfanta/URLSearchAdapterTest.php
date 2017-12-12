<?php

namespace EzSystems\RepositoryForms\Tests\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\URLService;
use eZ\Publish\API\Repository\Values\URL\Query\Criterion;
use eZ\Publish\API\Repository\Values\URL\Query\SortClause;
use eZ\Publish\API\Repository\Values\URL\SearchResult;
use eZ\Publish\API\Repository\Values\URL\URL;
use eZ\Publish\API\Repository\Values\URL\URLQuery;
use EzSystems\RepositoryForms\Pagination\Pagerfanta\URLSearchAdapter;
use PHPUnit\Framework\TestCase;

class URLSearchAdapterTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\URLService|\PHPUnit_Framework_MockObject_MockObject */
    private $urlService;

    protected function setUp()
    {
        $this->urlService = $this->createMock(URLService::class);
    }

    public function testGetNbResults()
    {
        $query = $this->createURLQuery();

        $searchResults = new SearchResult([
            'items' => [],
            'count' => 13,
        ]);

        $this->urlService
            ->expects($this->once())
            ->method('findUrls')
            ->willReturnCallback(function (URLQuery $q) use ($query, $searchResults) {
                $this->assertEquals($query->filter, $q->filter);
                $this->assertEquals($query->sortClauses, $q->sortClauses);
                $this->assertEquals(0, $q->offset);
                $this->assertEquals(0, $q->limit);

                return $searchResults;
            });

        $adapter = new URLSearchAdapter($query, $this->urlService);

        $this->assertEquals($searchResults->count, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $query = $this->createURLQuery();
        $limit = 25;
        $offset = 10;

        $searchResults = new SearchResult([
            'items' => [
                $this->createMock(URL::class),
                $this->createMock(URL::class),
                $this->createMock(URL::class),
            ],
            'count' => 13,
        ]);

        $this->urlService
            ->expects($this->once())
            ->method('findUrls')
            ->willReturnCallback(function (URLQuery $q) use ($query, $limit, $offset, $searchResults) {
                $this->assertEquals($query->filter, $q->filter);
                $this->assertEquals($query->sortClauses, $q->sortClauses);
                $this->assertEquals($limit, $q->limit);
                $this->assertEquals($offset, $q->offset);

                return $searchResults;
            });

        $adapter = new URLSearchAdapter($query, $this->urlService);

        $this->assertEquals($searchResults->items, $adapter->getSlice($offset, $limit));
    }

    private function createURLQuery()
    {
        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->sortClauses = [
            new SortClause\Id(),
        ];

        return $query;
    }
}
