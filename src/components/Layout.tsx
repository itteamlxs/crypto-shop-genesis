
import React from "react";
import { Link } from "react-router-dom";

interface LayoutProps {
  children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  return (
    <div className="min-h-screen flex flex-col">
      <header className="bg-zinc-900 text-white">
        <div className="container mx-auto px-4 py-4 flex justify-between items-center">
          <Link to="/" className="text-2xl font-bold">Crypto Shop</Link>
          <nav className="flex gap-6">
            <Link to="/" className="hover:text-zinc-300">Home</Link>
            <Link to="/products" className="hover:text-zinc-300">Products</Link>
            <Link to="/how-it-works" className="hover:text-zinc-300">How It Works</Link>
            <Link to="/cart" className="hover:text-zinc-300">Cart</Link>
          </nav>
        </div>
      </header>
      
      <main className="flex-grow container mx-auto px-4 py-8">
        {children}
      </main>
      
      <footer className="bg-zinc-900 text-white py-6">
        <div className="container mx-auto px-4">
          <div className="flex flex-col md:flex-row justify-between items-center">
            <p>© {new Date().getFullYear()} Crypto Shop. All rights reserved.</p>
            <div className="flex gap-4 mt-4 md:mt-0">
              <Link to="/support" className="hover:text-zinc-300">Support</Link>
              <Link to="/privacy" className="hover:text-zinc-300">Privacy Policy</Link>
              <Link to="/terms" className="hover:text-zinc-300">Terms of Service</Link>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default Layout;
