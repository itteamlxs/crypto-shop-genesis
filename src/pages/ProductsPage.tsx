
import React, { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { fetchProducts } from "../api/endpoints";
import Layout from "../components/Layout";
import ProductCard from "../components/ProductCard";
import { Input } from "@/components/ui/input";

const ProductsPage: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState("");
  
  const { data: products, isLoading, error } = useQuery({
    queryKey: ['products'],
    queryFn: fetchProducts
  });

  const filteredProducts = products?.filter(product => 
    product.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    product.description.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <Layout>
      <div className="mb-8">
        <h1 className="text-4xl font-bold mb-6">Products</h1>
        <Input
          type="text"
          placeholder="Search products..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="max-w-md"
        />
      </div>

      {isLoading && (
        <div className="flex justify-center py-12">
          <p>Loading products...</p>
        </div>
      )}

      {error && (
        <div className="bg-red-100 text-red-700 p-4 rounded">
          <p>Error loading products. Please try again later.</p>
        </div>
      )}

      {filteredProducts && filteredProducts.length > 0 ? (
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          {filteredProducts.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>
      ) : (
        <div className="text-center py-12">
          <p className="text-lg">No products found matching your search.</p>
        </div>
      )}
    </Layout>
  );
};

export default ProductsPage;
