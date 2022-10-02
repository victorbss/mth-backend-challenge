<?php


class ProductsTest extends TestCase{

    /**
     * SIMPLE INIT TEST
     * /products response status code equals 200
     *
     * @return void
     */
    public function testProductsResponseOK(){
        $this->get('/products');
        $this->assertResponseOk();
    }

}
