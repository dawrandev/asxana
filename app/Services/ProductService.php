<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    public function __construct(protected ProductRepository $productRepository) {}

    public function getProducts(array $data)
    {
        return $this->productRepository->getProducts($data);
    }
}
