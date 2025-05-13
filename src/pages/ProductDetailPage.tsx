
import React, { useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { fetchProduct, addToCart } from "../api/endpoints";
import Layout from "../components/Layout";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { toast } from "sonner";

const ProductDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [quantity, setQuantity] = useState(1);
  
  const { data: product, isLoading, error } = useQuery({
    queryKey: ['product', id],
    queryFn: () => fetchProduct(Number(id)),
    enabled: !!id
  });

  const handleAddToCart = async () => {
    if (!product) return;
    
    try {
      await addToCart(product.id, quantity);
      toast.success("Product added to cart!");
      navigate("/cart");
    } catch (error) {
      toast.error("Failed to add product to cart. Please try again.");
      console.error(error);
    }
  };

  if (isLoading) {
    return (
      <Layout>
        <div className="flex justify-center py-12">
          <p>Loading product details...</p>
        </div>
      </Layout>
    );
  }

  if (error || !product) {
    return (
      <Layout>
        <div className="bg-red-100 text-red-700 p-4 rounded">
          <p>Error loading product details. Please try again later.</p>
          <Button onClick={() => navigate("/products")} className="mt-4">
            Back to Products
          </Button>
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      <div className="grid md:grid-cols-2 gap-10">
        <div>
          <img 
            src={product.image_url || "/placeholder.svg"}
            alt={product.name}
            className="w-full h-auto rounded-lg shadow"
          />
        </div>
        
        <div>
          <h1 className="text-3xl font-bold mb-2">{product.name}</h1>
          <p className="text-3xl font-bold text-primary mb-4">${product.price.toFixed(2)}</p>
          
          <div className="mb-6">
            <h2 className="text-xl font-semibold mb-2">Description</h2>
            <p className="text-gray-700">{product.description}</p>
          </div>
          
          <Card className="p-4 mb-6">
            <div className="flex items-center mb-4">
              <p className="mr-4">Quantity:</p>
              <div className="flex items-center">
                <Button 
                  variant="outline" 
                  size="icon" 
                  onClick={() => setQuantity(Math.max(1, quantity - 1))}
                >
                  -
                </Button>
                <span className="mx-4">{quantity}</span>
                <Button 
                  variant="outline" 
                  size="icon" 
                  onClick={() => setQuantity(Math.min(product.stock, quantity + 1))}
                  disabled={quantity >= product.stock}
                >
                  +
                </Button>
              </div>
            </div>
            
            <p className="mb-4">
              <span className="font-medium">Availability: </span>
              {product.stock > 0 ? (
                <span className="text-green-600">{product.stock} in stock</span>
              ) : (
                <span className="text-red-600">Out of stock</span>
              )}
            </p>
            
            <Button 
              onClick={handleAddToCart} 
              disabled={product.stock <= 0}
              className="w-full"
            >
              {product.stock > 0 ? 'Add to Cart' : 'Out of Stock'}
            </Button>
          </Card>
          
          <div>
            <h2 className="text-xl font-semibold mb-2">Payment</h2>
            <p className="text-gray-700 mb-4">
              We accept Bitcoin, Ethereum, and other major cryptocurrencies.
            </p>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default ProductDetailPage;
