<?php

use PHPUnit\Framework\TestCase;
use Yolanda\Routing\Route;

class RouteTest extends TestCase
{


    public function funtionTemplateTest(float $digit): string
    {
        return sqrt($digit);
    }

    public function funtion2TemplateTest(float $digit, string $otherParameter): string
    {
        return $otherParameter . number_format($digit) ;
    }


    /**
     * @covers \Yolanda\Routing\Route::name
     * @covers \Yolanda\Routing\Route::uri
     * @covers \Yolanda\Routing\Route::method
     * @return void
     */
    public function testGet(): void
    {
        $name   =   'route1';
        $uri    =   'route1/test/{digit}/other/{otherParameter}/:otherOtherParameter';
        $method =   'GET';

        $route  =   new Route($name, $uri, $method, [$this,'funtionTemplateTest']);


        $this->assertEquals($name, $route->name());
        $this->assertEquals($uri, $route->uri());
        $this->assertContains($method, $route->method());
        $this->assertContains('digit', $route->parameters());
        $this->assertContains('otherParameter', $route->parameters());
        $this->assertContains('otherOtherParameter', $route->parameters());
        $this->assertArrayHasKey('digit', $route->parametersWithValues());
        $this->assertArrayHasKey('otherParameter', $route->parametersWithValues());
        $this->assertContains('otherOtherParameter', $route->parameters());
    }


    /**
     * @covers \Yolanda\Routing\Route::matches
     * @return void
     */
    public function testMatches(): void
    {
        $name   =   'route1';
        $uri    =   'route1/test/{digit}/other/{otherParameter}/:otherOtherParameter';
        $uriToMatch =   '/route1/test/14/other/12/34';
        $uriToMatchFalse =   'route1/test/14/other';
        $methodToMatch =   'GET';
        $method =   'GET';

        $route  =   new Route($name, $uri, $method, [$this,'funtionTemplateTest']);

        $this->assertTrue($route->matches($uriToMatch, $methodToMatch));
        $this->assertFalse($route->matches($uriToMatchFalse, $methodToMatch));
    }

    /**
     *
     * @covers \Yolanda\Routing\Route::dispatch
     */
    public function testDispatch(): void
    {
        $name   =   'route1';
        $uri    =   'route1/test/{digit}/:otherParameter';
        $method =   'GET';
        $value  =   1;

        $route  =   new Route($name, $uri, $method, [$this,'funtionTemplateTest']);

        $route->setParameterValue('digit', $value);
        $route->setParameterValue('otherParameter', 'otherThing');
        $this->assertEquals($this->funtionTemplateTest($value), $route->dispatch());


    }


}