
import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { createOrder } from "../api/endpoints";
import Layout from "../components/Layout";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card } from "@/components/ui/card";
import { toast } from "sonner";

const CheckoutPage: React.FC = () => {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    
    try {
      const response = await createOrder(email);
      navigate(`/payment/${response.orderId}`);
    } catch (error) {
      toast.error("Failed to create order. Please try again.");
      console.error(error);
    } finally {
      setIsSubmitting(false);
    }
  };
  
  return (
    <Layout>
      <h1 className="text-3xl font-bold mb-6">Checkout</h1>
      
      <div className="grid md:grid-cols-2 gap-8">
        <div>
          <Card className="p-6">
            <h2 className="text-xl font-semibold mb-4">Customer Information</h2>
            <form onSubmit={handleSubmit}>
              <div className="mb-4">
                <label htmlFor="email" className="block text-sm font-medium mb-1">
                  Email Address
                </label>
                <Input
                  id="email"
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  placeholder="your-email@example.com"
                />
                <p className="text-sm text-gray-500 mt-1">
                  Payment confirmation will be sent to this email
                </p>
              </div>
              
              <Button 
                type="submit" 
                className="w-full" 
                disabled={isSubmitting}
              >
                {isSubmitting ? 'Processing...' : 'Continue to Payment'}
              </Button>
            </form>
          </Card>
          
          <div className="mt-6">
            <h2 className="text-xl font-semibold mb-4">How It Works</h2>
            <ol className="list-decimal pl-5 space-y-2">
              <li>Enter your email to receive payment instructions and order confirmation.</li>
              <li>You'll be redirected to a payment page with Bitcoin/cryptocurrency details.</li>
              <li>Send the exact amount to the provided cryptocurrency address.</li>
              <li>Once payment is confirmed, your order will be processed immediately.</li>
            </ol>
          </div>
        </div>
        
        <div>
          <Card className="p-6">
            <h2 className="text-xl font-semibold mb-4">Order Summary</h2>
            {/* In a real app, we'd fetch this from the API */}
            <div className="divide-y">
              <div className="py-3 flex justify-between">
                <span>Subtotal</span>
                <span>$XX.XX</span>
              </div>
              <div className="py-3 flex justify-between">
                <span>Tax</span>
                <span>$0.00</span>
              </div>
              <div className="py-3 flex justify-between font-bold">
                <span>Total</span>
                <span>$XX.XX</span>
              </div>
            </div>
          </Card>
          
          <Card className="p-6 mt-6">
            <h2 className="text-xl font-semibold mb-4">Accepted Cryptocurrencies</h2>
            <div className="grid grid-cols-2 gap-4">
              <div className="flex items-center">
                <div className="w-8 h-8 bg-gray-200 rounded-full mr-2"></div>
                <span>Bitcoin</span>
              </div>
              <div className="flex items-center">
                <div className="w-8 h-8 bg-gray-200 rounded-full mr-2"></div>
                <span>Ethereum</span>
              </div>
              <div className="flex items-center">
                <div className="w-8 h-8 bg-gray-200 rounded-full mr-2"></div>
                <span>Litecoin</span>
              </div>
              <div className="flex items-center">
                <div className="w-8 h-8 bg-gray-200 rounded-full mr-2"></div>
                <span>Bitcoin Cash</span>
              </div>
            </div>
          </Card>
        </div>
      </div>
    </Layout>
  );
};

export default CheckoutPage;
