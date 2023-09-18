<?php


use PHPUnit\Framework\TestCase;
use Yolanda\Http\Routing\Exceptions\InvalidFunctionParametersException;
use Yolanda\Http\Routing\Exceptions\InvalidUrlParametersException;
use Yolanda\Http\Routing\Exceptions\RouteNotFoundException;
use Yolanda\Http\Routing\Router;

class RouterTest extends TestCase
{

    function function1(): float|int
    {
        return 5*5;
    }

    function function3(float $digit): float|int
    {
        return $digit*5;
    }

    /**
     * @covers \Yolanda\Http\Routing\Router::name
     * @covers \Yolanda\Http\Routing\Router::hasRouteByName
     * @return void
     */
    public function testSearchRouteByName()
    {
       $router  =   new Router();

       $router
           ->name('route1')
           ->get('/route1', [$this, 'function1'])->done();

       $router
           ->name('route2')
           ->post('/route2', [$this, 'function1'])->done();

       $router
           ->name('route3')
           ->put('/route3', [$this, 'function1'])->done();


       $this->assertNotNull($router->hasRouteByName('route3'));
    }


    /**
     * @covers \Yolanda\Http\Routing\Router::name
     * @covers \Yolanda\Http\Routing\Router::hasRouteByName
     * @return void
     */
    public function testSearchRouteByUri()
    {
        $router  =   new Router();

        $router
            ->name('route1')
            ->get('/route1', [$this, 'function1'])->done();

        $router
            ->name('route2')
            ->post('/route2', [$this, 'function1'])->done();

        $router
            ->name('route3')
            ->put('/route3', [$this, 'function1'])->done();


        $this->assertNotNull($router->hasRouteByUri('/route3'));
    }

    /**
     *
     * @covers \Yolanda\Http\Routing\Router::listRoutes
     *
     * @return void
     */
    public function testListAllRoute()
    {
        $router  =   new Router();

        $router
            ->name('route1')
            ->get('/route1', [$this, 'function1'])->done();

        $router
            ->name('route2')
            ->post('/route2', [$this, 'function1'])->done();

        $router
            ->name('route3')
            ->put('/route3', [$this, 'function1'])->done();

        $routes =   $router->listRoutes();

        $this->assertNotEmpty($routes, 'routes empty');
    }

    /**
     * @covers \Yolanda\Http\Routing\Router::done
     * @covers \Yolanda\Http\Routing\Router::doneWithParameters
     *
     * @return void
     * @throws ReflectionException
     * @throws InvalidFunctionParametersException
     * @throws InvalidUrlParametersException
     * @throws RouteNotFoundException
     */
    public function testDone()
    {
        $router  =   new Router();

        $router
            ->name('route1')
            ->get('/route1/', [$this, 'function1'])->done();

        $router
            ->name('route2')
            ->post('/route2', [$this, 'function1'])->done();

        $router
            ->name('route3')
            ->put('/route3/:digit', [$this, 'function3'])->done();


        $uriToMatch =   '/route1';
        $methodToMatch  =   'GET';
        $result1 = $router->doneWithParameters($uriToMatch, $methodToMatch);

        $uriToMatch =   '/route2';
        $methodToMatch  =   'POST';
        $result2 = $router->doneWithParameters($uriToMatch, $methodToMatch);

        $uriToMatch =   '/route3/3';
        $methodToMatch  =   'PUT';
        $result3 = $router->doneWithParameters($uriToMatch, $methodToMatch);



        $this->assertEquals($this->function1(), $result1);
        $this->assertEquals($this->function1(), $result2);
        $this->assertEquals($this->function3(3), $result3);

    }

}
