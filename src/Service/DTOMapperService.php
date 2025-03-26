<?php

namespace App\Service;

use App\DTO\Category as CategoryDTO;
use App\DTO\Product as ProductDTO;
use App\Entity\Category as CategoryEntity;
use App\Entity\Product as ProductEntity;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class DTOMapperService
{
    public function __construct(
        #[Autowire('%app.upload_dir_name%')] private string $uploadDirName,
    ) {}

    public function mapCategory(CategoryEntity $category): ?CategoryDTO
    {
        if (null === $id = $category->getId()) {
            return null;
        }
        if (null === $name = $category->getName()) {
            return null;
        }

        return new CategoryDTO(
            id: $id,
            name: $name,
            color: $category->getColor(),
            products: array_filter(
                array_map(
                    fn(ProductEntity $product) => $this->mapProduct($product, $category),
                    $category->getProducts()->toArray(),
                )
            ),
        );
    }

    public function mapProduct(ProductEntity $product, ?CategoryEntity $category): ?ProductDTO
    {
        if (null === $id = $product->getId()) {
            return null;
        }
        if (null === $name = $product->getName()) {
            return null;
        }
        if (null === $price = $product->getPrice()) {
            return null;
        }
        if (null === $icon = $product->getIcon()) {
            return null;
        }

        return new ProductDTO(
            id: $id,
            name: $name,
            price: $price,
            color: $product->getColor() ?? $category?->getColor() ?? 'transparent',
            icon: $icon,
            imageUrl: (null !== $imageFile = $product->getImage())
                ? "/{$this->uploadDirName}/{$imageFile}"
                : null,
        );
    }
}
