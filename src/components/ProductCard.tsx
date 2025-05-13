
import React from "react";
import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";

export interface Product {
  id: number;
  name: string;
  price: number;
  description: string;
  image_url: string;
  stock: number;
}

interface ProductCardProps {
  product: Product;
}

const ProductCard: React.FC<ProductCardProps> = ({ product }) => {
  return (
    <Card className="overflow-hidden">
      <div className="aspect-ratio-square overflow-hidden h-48">
        <img 
          src={product.image_url || "/placeholder.svg"}
          alt={product.name}
          className="w-full h-full object-cover"
        />
      </div>
      <CardHeader>
        <CardTitle className="truncate">{product.name}</CardTitle>
      </CardHeader>
      <CardContent>
        <p className="text-2xl font-bold">${product.price.toFixed(2)}</p>
        <p className="text-sm text-muted-foreground mt-2 line-clamp-2">{product.description}</p>
      </CardContent>
      <CardFooter className="flex justify-between">
        <Link to={`/product/${product.id}`}>
          <Button variant="outline">View Details</Button>
        </Link>
        <Button 
          disabled={product.stock <= 0}
        >
          {product.stock > 0 ? 'Add to Cart' : 'Out of Stock'}
        </Button>
      </CardFooter>
    </Card>
  );
};

export default ProductCard;
