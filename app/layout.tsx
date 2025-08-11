import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import Script from 'next/script';
import './base.css';
import './default.css';
import './fonts-text.css';
import './app.css';
import './home.css';

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-inter',
});

export const metadata: Metadata = {
  title: 'Worldwide Transfers: Taxis and Private Transportation - MyTransfers',
  description: 'Book your private transfer from the airport to any destination today. Benefit from the best prices on our website with free cancellation up to 24 hours before your trip.',
  keywords: 'airport transfer, private transfer, taxi, transportation, mytransfers',
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body className={`${inter.variable} font-sans antialiased`}>
        {/* Bootstrap JS for basic functionality */}
        <Script
          src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
          strategy="afterInteractive"
        />
        
        {/* Uncomment below if you want to use original JS files */}
        {/*
        <Script
          src="/js/init.js"
          strategy="beforeInteractive"
        />
        <Script
          src="/js/lang/en.js"
          strategy="afterInteractive"
        />
        <Script
          src="/js/search/controller.js"
          strategy="afterInteractive"
        />
        <Script
          src="/js/home/home.js"
          strategy="afterInteractive"
        />
        */}
        
        <main>{children}</main>
      </body>
    </html>
  );
}
