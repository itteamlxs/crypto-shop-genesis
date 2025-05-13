
import React from "react";
import { useQuery } from "@tanstack/react-query";
import { fetchProducts } from "../api/endpoints";
import Layout from "../components/Layout";
import ProductCard from "../components/ProductCard";
import { Button } from "@/components/ui/button";
import { Link } from "react-router-dom";

const HomePage: React.FC = () => {
  const { data: products, isLoading, error } = useQuery({
    queryKey: ['featuredProducts'],
    queryFn: fetchProducts
  });

  return (
    <Layout>
      {/* Hero Section */}
      <section className="py-12 md:py-20 bg-gradient-to-r from-zinc-900 to-zinc-800 text-white rounded-lg mb-12">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl md:text-6xl font-bold mb-6">Shop with Cryptocurrency</h1>
          <p className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
            The easiest and safest way to shop online using Bitcoin and other cryptocurrencies.
          </p>
          <div className="flex flex-wrap gap-4 justify-center">
            <Link to="/products">
              <Button size="lg" className="text-lg">
                Browse Products
              </Button>
            </Link>
            <Link to="/how-it-works">
              <Button size="lg" variant="outline" className="text-lg">
                How It Works
              </Button>
            </Link>
          </div>
        </div>
      </section>

      {/* Featured Products */}
      <section>
        <div className="flex justify-between items-center mb-8">
          <h2 className="text-3xl font-bold">Featured Products</h2>
          <Link to="/products">
            <Button variant="ghost">View All</Button>
          </Link>
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

        {products && (
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            {products.slice(0, 8).map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        )}
      </section>

      {/* Why Choose Us */}
      <section className="py-12 my-12 bg-zinc-100 rounded-lg">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold mb-8 text-center">Why Choose Crypto Shop</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="bg-white p-6 rounded-lg shadow-sm">
              <h3 className="text-xl font-bold mb-2">Secure Payments</h3>
              <p>All cryptocurrency transactions are secure, private, and processed instantly.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-sm">
              <h3 className="text-xl font-bold mb-2">Global Delivery</h3>
              <p>We ship our products to customers worldwide with fast and reliable shipping.</p>
            </div>
            <div className="bg-white p-6 rounded-lg shadow-sm">
              <h3 className="text-xl font-bold mb-2">Excellent Support</h3>
              <p>Our customer support team is available 24/7 to assist you with any questions.</p>
            </div>
          </div>
        </div>
      </section>
    </Layout>
  );
};

export default HomePage;
