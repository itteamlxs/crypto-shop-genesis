
import React from "react";
import { useQuery } from "@tanstack/react-query";
import { getCart } from "../api/endpoints";
import Layout from "../components/Layout";
import { Button } from "@/components/ui/button";
import { Link, useNavigate } from "react-router-dom";

const CartPage: React.FC = () => {
  const navigate = useNavigate();
  const { data: cart, isLoading, error } = useQuery({
    queryKey: ['cart'],
    queryFn: getCart
  });

  const handleCheckout = () => {
    navigate('/checkout');
  };

  if (isLoading) {
    return (
      <Layout>
        <div className="flex justify-center py-12">
          <p>Loading cart...</p>
        </div>
      </Layout>
    );
  }

  if (error) {
    return (
      <Layout>
        <div className="bg-red-100 text-red-700 p-4 rounded">
          <p>Error loading cart. Please try again later.</p>
        </div>
      </Layout>
    );
  }

  const isEmpty = !cart || !cart.items || cart.items.length === 0;

  return (
    <Layout>
      <h1 className="text-3xl font-bold mb-6">Shopping Cart</h1>
      
      {isEmpty ? (
        <div className="text-center py-12">
          <p className="text-xl mb-4">Your cart is empty</p>
          <Link to="/products">
            <Button>Continue Shopping</Button>
          </Link>
        </div>
      ) : (
        <>
          <div className="bg-white rounded-lg shadow overflow-hidden mb-6">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {cart.items.map((item: any) => (
                  <tr key={item.product.id}>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="h-16 w-16 flex-shrink-0">
                          <img 
                            src={item.product.image_url || "/placeholder.svg"}
                            alt={item.product.name}
                            className="h-full w-full object-cover"
                          />
                        </div>
                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">
                            {item.product.name}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-900">${item.product.price.toFixed(2)}</div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-900">{item.quantity}</div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-900">${(item.product.price * item.quantity).toFixed(2)}</div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          
          <div className="bg-white rounded-lg shadow p-6 mb-6">
            <div className="flex justify-between mb-2">
              <span>Subtotal</span>
              <span>${cart.total.toFixed(2)}</span>
            </div>
            <div className="flex justify-between mb-4">
              <span>Tax</span>
              <span>$0.00</span>
            </div>
            <div className="flex justify-between text-xl font-bold">
              <span>Total</span>
              <span>${cart.total.toFixed(2)}</span>
            </div>
          </div>
          
          <div className="flex justify-between">
            <Link to="/products">
              <Button variant="outline">Continue Shopping</Button>
            </Link>
            <Button onClick={handleCheckout}>Proceed to Checkout</Button>
          </div>
        </>
      )}
    </Layout>
  );
};

export default CartPage;
