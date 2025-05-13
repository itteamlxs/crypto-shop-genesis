
import React from "react";
import Layout from "../components/Layout";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";

const SupportPage: React.FC = () => {
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // This would normally connect to an API endpoint
    toast.success("Your support request has been submitted. We'll respond shortly.");
  };
  
  return (
    <Layout>
      <div className="grid md:grid-cols-2 gap-10">
        <div>
          <h1 className="text-3xl font-bold mb-6">Support Center</h1>
          
          <div className="mb-8">
            <h2 className="text-xl font-bold mb-4">Contact Us</h2>
            <p className="mb-4">
              Have questions or need assistance? Our support team is available to help
              you with any inquiries related to orders, payments, or product information.
            </p>
            
            <div className="space-y-4">
              <div className="flex items-start">
                <div className="bg-primary-100 p-2 rounded-full mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="text-primary">
                    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"></path>
                  </svg>
                </div>
                <div>
                  <h3 className="font-semibold">Phone Support</h3>
                  <p>+1 (555) 123-4567</p>
                  <p className="text-sm text-gray-500">Monday - Friday, 9am - 5pm EST</p>
                </div>
              </div>
              
              <div className="flex items-start">
                <div className="bg-primary-100 p-2 rounded-full mr-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="text-primary">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <path d="M22 6l-10 7L2 6"></path>
                  </svg>
                </div>
                <div>
                  <h3 className="font-semibold">Email Support</h3>
                  <p>support@cryptoshop.com</p>
                  <p className="text-sm text-gray-500">We respond within 24 hours</p>
                </div>
              </div>
            </div>
          </div>
          
          <div>
            <h2 className="text-xl font-bold mb-4">Common Questions</h2>
            
            <div className="space-y-4">
              <Card className="p-4">
                <h3 className="font-semibold mb-2">How do I track my order?</h3>
                <p className="text-sm">
                  Once your order has shipped, you'll receive a confirmation email with tracking information.
                </p>
              </Card>
              
              <Card className="p-4">
                <h3 className="font-semibold mb-2">What should I do if my payment doesn't confirm?</h3>
                <p className="text-sm">
                  Cryptocurrency payments can sometimes take longer to confirm due to blockchain congestion.
                  If your payment doesn't confirm within an hour, please contact our support team.
                </p>
              </Card>
              
              <Card className="p-4">
                <h3 className="font-semibold mb-2">Do you ship internationally?</h3>
                <p className="text-sm">
                  Yes! We ship to most countries worldwide. Shipping rates and delivery times vary by location.
                </p>
              </Card>
              
              <Card className="p-4">
                <h3 className="font-semibold mb-2">How do returns work?</h3>
                <p className="text-sm">
                  If you're not satisfied with your purchase, you can request a return within 30 days.
                  Refunds are processed in the original cryptocurrency used for payment.
                </p>
              </Card>
            </div>
          </div>
        </div>
        
        <div>
          <Card className="p-6">
            <h2 className="text-xl font-bold mb-4">Send Us a Message</h2>
            <form onSubmit={handleSubmit}>
              <div className="space-y-4">
                <div>
                  <label htmlFor="name" className="block text-sm font-medium mb-1">
                    Name
                  </label>
                  <Input id="name" required />
                </div>
                
                <div>
                  <label htmlFor="email" className="block text-sm font-medium mb-1">
                    Email
                  </label>
                  <Input id="email" type="email" required />
                </div>
                
                <div>
                  <label htmlFor="orderNumber" className="block text-sm font-medium mb-1">
                    Order Number (if applicable)
                  </label>
                  <Input id="orderNumber" />
                </div>
                
                <div>
                  <label htmlFor="subject" className="block text-sm font-medium mb-1">
                    Subject
                  </label>
                  <Input id="subject" required />
                </div>
                
                <div>
                  <label htmlFor="message" className="block text-sm font-medium mb-1">
                    Message
                  </label>
                  <Textarea id="message" rows={5} required />
                </div>
                
                <Button type="submit" className="w-full">
                  Send Message
                </Button>
              </div>
            </form>
          </Card>
        </div>
      </div>
    </Layout>
  );
};

export default SupportPage;
