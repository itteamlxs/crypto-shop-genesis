
import { Product } from "../components/ProductCard";

const API_BASE = '/api';

export async function fetchProducts(): Promise<Product[]> {
  const response = await fetch(`${API_BASE}/products.php`);
  if (!response.ok) {
    throw new Error('Failed to fetch products');
  }
  return await response.json();
}

export async function fetchProduct(id: number): Promise<Product> {
  const response = await fetch(`${API_BASE}/products.php?id=${id}`);
  if (!response.ok) {
    throw new Error(`Failed to fetch product with id ${id}`);
  }
  return await response.json();
}

export async function addToCart(productId: number, quantity: number): Promise<void> {
  const formData = new FormData();
  formData.append('product_id', productId.toString());
  formData.append('quantity', quantity.toString());
  
  const response = await fetch(`${API_BASE}/cart/add.php`, {
    method: 'POST',
    body: formData,
  });
  
  if (!response.ok) {
    throw new Error('Failed to add product to cart');
  }
  
  return await response.json();
}

export async function getCart(): Promise<any> {
  const response = await fetch(`${API_BASE}/cart/index.php`);
  if (!response.ok) {
    throw new Error('Failed to fetch cart');
  }
  return await response.json();
}

export async function createOrder(email: string): Promise<any> {
  const formData = new FormData();
  formData.append('email', email);
  
  const response = await fetch(`${API_BASE}/orders/create.php`, {
    method: 'POST',
    body: formData,
  });
  
  if (!response.ok) {
    throw new Error('Failed to create order');
  }
  
  return await response.json();
}

export async function checkPaymentStatus(orderId: number): Promise<any> {
  const response = await fetch(`${API_BASE}/payment-status.php?id=${orderId}`);
  if (!response.ok) {
    throw new Error('Failed to check payment status');
  }
  return await response.json();
}
