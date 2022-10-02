<?php

namespace App\Http\Controllers;

class ProductsController extends Controller{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($request){
        $this->request = $request;
    }

    private function getQueryParams($queryString){
        $filters = [];
        $explodedQueryString = explode('&', $queryString);

        foreach ($explodedQueryString as $string) {
            $values = explode('=', $string);
            $key = $values[0];
            $val = $values[1];
            $filters[$key] = $val;
        }

        return $filters;
    }

    private function filterProductsList($products, $filters = []){
        //FILTERS
        if(!empty($filters['category'])){
            $products = array_filter($products, function($product) use ($filters){
                return $product['category'] == $filters['category'];
            });
            $products = array_values($products);
        }

        if(!empty($filters['priceLessThan'])){
            $products = array_filter($products, function($product) use ($filters){
                return $product['price'] <= (int)$filters['priceLessThan'];
            });
            $products = array_values($products);
        }

        return $products;
    }

    private function setProductsDiscount($products, $discountRules){

        foreach ($products as $k => $product) {
            //FORMAT RESPONSE PRICE
            $product['price'] = [
                'original'            => $product['price'],
                'final'               => $product['price'],
                'discount_percentage' => null,
                'currency'            => 'EUR'
            ];

            //APPLY DISCOUNT
            foreach ($discountRules as $rule) {
                if($product[$rule['key']] == $rule['value']){
                    $product['price']['final'] = $product['price']['original'] - 
                                                ($product['price']['original'] * ($rule['percentDiscount'] / 100));   
                    $product['price']['discount_percentage'] = $rule['percentDiscount'].'%';
                    break;
                }
            } 

            //UPDATE PRODUCT RESPONSE
            $products[$k] = $product;
        }

        return $products;
    }

    public function getProducts(){
        //PRODUCTS AND DISCOUNT RULES
        $products      = json_decode(file_get_contents(__DIR__ . '/../../../resources/data/products.json'), true)['products']; 
        $discountRules = json_decode(file_get_contents(__DIR__ . '/../../../resources/data/discount-rules.json'), true)['rules'];
        
        //FILTERS
        $filters = array();
        if(!empty($this->request->getQueryString())){
            $filters = $this->getQueryParams($this->request->getQueryString());
        }

        //OFFSET~LIMIT RESPONSE
        $offset = !empty($filters['offset']) ? $filters['offset'] : 0;
        $limit  = !empty($filters['limit']) ? $filters['limit'] : 5;

        //FILTER PRODUCTS
        $products = $this->filterProductsList($products, $filters);

        //APPLY DISCOUNTS
        $products = $this->setProductsDiscount($products, $discountRules);

        //RESPONSE LIST
        $count = 0;
        for ($i = $offset; $i < count($products); $i++) { 
            //LIMIT RESPONSE
            if($count == $limit) break;
            $response['products'][] = $products[$i];

            $count++;
        }

        return $response ?? null;
    }
}
