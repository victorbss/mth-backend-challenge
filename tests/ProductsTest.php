<?php


class ProductsTest extends TestCase{

    /**
     * SIMPLE INIT TEST
     * /products response status code equals 200
     *
     * @return void
     */
    public function testProducts(){
        $response = $this->call('GET', '/products');
        $this->assertEquals($response->status(), 200);
    }

}
