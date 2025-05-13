
import React from "react";
import { Link } from "react-router-dom";
import Layout from "../components/Layout";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";

const ThankYouPage: React.FC = () => {
  // For demo purposes, this would normally come from the API or URL params
  const orderNumber = "1234567890";

  return (
    <Layout>
      <div className="max-w-2xl mx-auto text-center">
        <div className="mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="text-green-600">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
          </div>
          <h1 className="text-3xl font-bold mb-2">Thank You for Your Order!</h1>
          <p className="text-xl">Your payment has been confirmed</p>
        </div>
        
        <Card className="p-6 mb-8">
          <div className="mb-4">
            <p className="text-sm text-gray-500">Order Number</p>
            <p className="text-lg font-bold">{orderNumber}</p>
          </div>
          
          <p className="mb-4">
            We've sent a confirmation email with all the details of your purchase.
          </p>
          
          <div className="bg-blue-50 p-4 rounded-lg text-left mb-4">
            <h3 className="font-medium mb-2">What happens next?</h3>
            <ol className="list-decimal pl-5 space-y-1">
              <li>Your order is now being processed.</li>
              <li>You'll receive shipping information via email.</li>
              <li>Track your order using the order number above.</li>
            </ol>
          </div>
        </Card>
        
        <div className="flex flex-col sm:flex-row justify-center gap-4">
          <Link to="/products">
            <Button>Continue Shopping</Button>
          </Link>
          <Link to="/support">
            <Button variant="outline">Contact Support</Button>
          </Link>
        </div>
      </div>
    </Layout>
  );
};

export default ThankYouPage;
