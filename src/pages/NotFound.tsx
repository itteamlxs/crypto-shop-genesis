
import React from "react";
import { Link } from "react-router-dom";
import Layout from "../components/Layout";
import { Button } from "@/components/ui/button";

const NotFound: React.FC = () => {
  return (
    <Layout>
      <div className="flex flex-col items-center justify-center py-12">
        <h1 className="text-6xl font-bold text-gray-900 mb-4">404</h1>
        <h2 className="text-2xl font-medium text-gray-700 mb-6">Page Not Found</h2>
        <p className="text-gray-500 max-w-md text-center mb-8">
          The page you are looking for doesn't exist or has been moved.
        </p>
        <div className="flex gap-4">
          <Link to="/">
            <Button>Go to Homepage</Button>
          </Link>
          <Link to="/products">
            <Button variant="outline">Browse Products</Button>
          </Link>
        </div>
      </div>
    </Layout>
  );
};

export default NotFound;
