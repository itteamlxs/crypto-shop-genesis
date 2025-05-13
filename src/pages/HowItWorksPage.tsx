
import React from "react";
import Layout from "../components/Layout";
import { Card } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";

const HowItWorksPage: React.FC = () => {
  return (
    <Layout>
      <h1 className="text-3xl font-bold mb-6">How Crypto Shop Works</h1>
      
      <div className="mb-10">
        <p className="text-lg mb-4">
          Crypto Shop makes it easy to purchase products using cryptocurrency. 
          Follow these simple steps to complete your purchase:
        </p>
        
        <div className="grid md:grid-cols-4 gap-4 mb-8">
          <Card className="p-6">
            <div className="text-4xl font-bold text-primary mb-4">1</div>
            <h3 className="text-xl font-bold mb-2">Browse & Select</h3>
            <p>Browse our catalog and add products to your cart</p>
          </Card>
          
          <Card className="p-6">
            <div className="text-4xl font-bold text-primary mb-4">2</div>
            <h3 className="text-xl font-bold mb-2">Checkout</h3>
            <p>Enter your email to receive order confirmation and payment details</p>
          </Card>
          
          <Card className="p-6">
            <div className="text-4xl font-bold text-primary mb-4">3</div>
            <h3 className="text-xl font-bold mb-2">Pay with Crypto</h3>
            <p>Send the exact cryptocurrency amount to the provided address</p>
          </Card>
          
          <Card className="p-6">
            <div className="text-4xl font-bold text-primary mb-4">4</div>
            <h3 className="text-xl font-bold mb-2">Receive Your Order</h3>
            <p>Once payment is confirmed, your order is processed and shipped</p>
          </Card>
        </div>
        
        <div className="flex justify-center">
          <Link to="/products">
            <Button size="lg">Start Shopping</Button>
          </Link>
        </div>
      </div>
      
      <Separator className="my-8" />
      
      <div className="mb-10">
        <h2 className="text-2xl font-bold mb-4">Cryptocurrency Payments</h2>
        
        <div className="grid md:grid-cols-2 gap-8">
          <div>
            <h3 className="text-xl font-bold mb-2">Accepted Cryptocurrencies</h3>
            <p className="mb-4">We currently accept the following cryptocurrencies for payment:</p>
            
            <ul className="list-disc pl-5 mb-4 space-y-1">
              <li>Bitcoin (BTC)</li>
              <li>Ethereum (ETH)</li>
              <li>Litecoin (LTC)</li>
              <li>Bitcoin Cash (BCH)</li>
            </ul>
            
            <p className="text-sm text-gray-500">
              Don't see your preferred cryptocurrency? Contact us to request support for additional options.
            </p>
          </div>
          
          <div>
            <h3 className="text-xl font-bold mb-2">Payment Security</h3>
            <p className="mb-4">
              All cryptocurrency transactions are processed securely through our integration with BTCPay Server.
              Here's what you need to know:
            </p>
            
            <ul className="list-disc pl-5 space-y-1">
              <li>Payments are confirmed on the blockchain</li>
              <li>No personal financial information is stored</li>
              <li>Each payment request has a unique address</li>
              <li>Payment requests expire after 30 minutes for security</li>
            </ul>
          </div>
        </div>
      </div>
      
      <Separator className="my-8" />
      
      <div className="mb-10">
        <h2 className="text-2xl font-bold mb-4">Frequently Asked Questions</h2>
        
        <div className="space-y-6">
          <div>
            <h3 className="text-lg font-bold mb-1">How long do cryptocurrency payments take to process?</h3>
            <p>
              Most cryptocurrency payments are confirmed within minutes, though actual confirmation times
              depend on blockchain network conditions. Once we receive confirmation, your order is processed immediately.
            </p>
          </div>
          
          <div>
            <h3 className="text-lg font-bold mb-1">What if I send the wrong amount?</h3>
            <p>
              It's important to send the exact amount requested. If you send less, your payment won't be confirmed.
              If you send more, the excess amount will not be automatically refunded. Please contact customer support
              for assistance.
            </p>
          </div>
          
          <div>
            <h3 className="text-lg font-bold mb-1">Do you offer refunds?</h3>
            <p>
              Refunds are processed on a case-by-case basis. Due to the nature of blockchain transactions,
              refunds will be issued in the original cryptocurrency used for the purchase.
            </p>
          </div>
          
          <div>
            <h3 className="text-lg font-bold mb-1">How do I get a cryptocurrency wallet?</h3>
            <p>
              There are many cryptocurrency wallets available for desktop, mobile, and as hardware devices.
              Some popular options include Exodus, Electrum, MetaMask, and Ledger. Research to find the one
              that best suits your needs.
            </p>
          </div>
        </div>
      </div>
      
      <div className="bg-primary-50 p-6 rounded-lg">
        <h2 className="text-2xl font-bold mb-4">Need More Help?</h2>
        <p className="mb-4">
          If you have any questions about cryptocurrency payments or need assistance with your order,
          our customer support team is here to help.
        </p>
        <Link to="/support">
          <Button>Contact Support</Button>
        </Link>
      </div>
    </Layout>
  );
};

export default HowItWorksPage;
