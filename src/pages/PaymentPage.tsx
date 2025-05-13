
import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { checkPaymentStatus } from "../api/endpoints";
import Layout from "../components/Layout";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { toast } from "sonner";

const PaymentPage: React.FC = () => {
  const { orderId } = useParams<{ orderId: string }>();
  const navigate = useNavigate();
  const [status, setStatus] = useState<string>("pending");
  const [loading, setLoading] = useState<boolean>(false);
  
  // For demo purposes, this would normally come from the API
  const paymentDetails = {
    total: 199.99,
    btcAmount: 0.00754321,
    address: "bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh",
    expiresAt: new Date(Date.now() + 30 * 60 * 1000), // 30 minutes from now
  };
  
  const checkStatus = async () => {
    if (!orderId) return;
    
    setLoading(true);
    try {
      const result = await checkPaymentStatus(Number(orderId));
      setStatus(result.status);
      
      if (result.status === "completed") {
        toast.success("Payment confirmed!");
        setTimeout(() => {
          navigate("/thank-you");
        }, 2000);
      } else if (result.status === "expired") {
        toast.error("Payment time has expired.");
      }
    } catch (error) {
      console.error("Error checking payment status:", error);
    } finally {
      setLoading(false);
    }
  };
  
  // Check payment status every 30 seconds
  useEffect(() => {
    const interval = setInterval(() => {
      checkStatus();
    }, 30000);
    
    return () => clearInterval(interval);
  }, [orderId]);
  
  // Calculate remaining time
  const [timeLeft, setTimeLeft] = useState<string>("");
  
  useEffect(() => {
    const calculateTimeLeft = () => {
      const difference = paymentDetails.expiresAt.getTime() - new Date().getTime();
      
      if (difference <= 0) {
        setTimeLeft("Expired");
        return;
      }
      
      const minutes = Math.floor((difference / 1000 / 60) % 60);
      const seconds = Math.floor((difference / 1000) % 60);
      
      setTimeLeft(`${minutes}:${seconds < 10 ? '0' : ''}${seconds}`);
    };
    
    calculateTimeLeft();
    const timer = setInterval(calculateTimeLeft, 1000);
    
    return () => clearInterval(timer);
  }, []);
  
  return (
    <Layout>
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold mb-6">Complete Your Payment</h1>
        
        <Card className="p-6 mb-6">
          <div className="mb-6 text-center">
            <div className={`inline-block px-3 py-1 rounded-full text-sm font-medium
              ${status === "completed" ? "bg-green-100 text-green-800" : 
                status === "expired" ? "bg-red-100 text-red-800" : 
                "bg-yellow-100 text-yellow-800"}
            `}>
              {status === "completed" ? "Payment Confirmed" :
               status === "expired" ? "Payment Expired" :
               "Awaiting Payment"}
            </div>
          </div>
          
          {status === "pending" && (
            <>
              <div className="text-center mb-6">
                <p className="text-lg mb-2">Send exactly</p>
                <p className="text-3xl font-bold mb-2">{paymentDetails.btcAmount} BTC</p>
                <p className="text-gray-500">(${paymentDetails.total.toFixed(2)} USD)</p>
              </div>
              
              <div className="mb-6 text-center">
                <div className="bg-gray-100 p-4 rounded-lg mb-2">
                  <p className="font-mono text-sm break-all">{paymentDetails.address}</p>
                </div>
                <Button variant="outline" onClick={() => navigator.clipboard.writeText(paymentDetails.address)}>
                  Copy Address
                </Button>
              </div>
              
              <div className="text-center mb-6">
                <div className="w-48 h-48 bg-gray-200 mx-auto mb-2">
                  {/* This would be a QR code in a real app */}
                  <div className="flex items-center justify-center h-full text-gray-500">
                    QR Code Placeholder
                  </div>
                </div>
                <p className="text-sm text-gray-500">
                  Scan to pay with your cryptocurrency wallet
                </p>
              </div>
              
              <div className="bg-yellow-50 p-4 rounded-lg text-center mb-6">
                <p className="text-sm">
                  <strong>Time remaining:</strong> {timeLeft}
                </p>
                <p className="text-xs text-gray-500 mt-1">
                  This payment request will expire after 30 minutes
                </p>
              </div>
              
              <div className="text-center">
                <Button onClick={checkStatus} disabled={loading}>
                  {loading ? "Checking..." : "Check Payment Status"}
                </Button>
              </div>
            </>
          )}
          
          {status === "completed" && (
            <div className="text-center">
              <p className="text-xl mb-4">Your payment has been confirmed!</p>
              <p className="mb-6">You will receive a confirmation email shortly.</p>
              <Button onClick={() => navigate("/thank-you")}>
                Continue to Order Confirmation
              </Button>
            </div>
          )}
          
          {status === "expired" && (
            <div className="text-center">
              <p className="text-xl mb-4">This payment request has expired</p>
              <p className="mb-6">Please return to checkout to create a new payment request.</p>
              <Button onClick={() => navigate("/checkout")}>
                Return to Checkout
              </Button>
            </div>
          )}
        </Card>
        
        <Card className="p-6">
          <h2 className="text-lg font-semibold mb-2">Need Help?</h2>
          <p className="mb-4">
            If you're having trouble with your payment, please contact our support team 
            at support@cryptoshop.com or visit our help center.
          </p>
          <div className="flex gap-4">
            <Button variant="outline" onClick={() => navigate("/support")}>
              Contact Support
            </Button>
            <Button variant="outline" onClick={() => navigate("/how-it-works")}>
              How Cryptocurrency Payments Work
            </Button>
          </div>
        </Card>
      </div>
    </Layout>
  );
};

export default PaymentPage;
