'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

export default function Header() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isLanguageOpen, setIsLanguageOpen] = useState(false);
  const [isCurrencyOpen, setIsCurrencyOpen] = useState(false);
  const [selectedLanguage, setSelectedLanguage] = useState('EN');
  const [selectedCurrency, setSelectedCurrency] = useState('EUR');

  // Close dropdowns when clicking outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      const target = event.target as HTMLElement;
      if (!target.closest('.dropdown')) {
        setIsLanguageOpen(false);
        setIsCurrencyOpen(false);
      }
    };

    document.addEventListener('click', handleClickOutside);
    return () => document.removeEventListener('click', handleClickOutside);
  }, []);

  return (
    <header className="c-header text-white">
      <nav className="navbar navbar-expand-lg c-nav">
        <div className="container">
          <Link className="navbar-brand" href="/" title="Mytransfers">
            <img src="/images/logo.png" alt="MyTransfers" style={{height: '40px', width: 'auto'}} />
          </Link>
          
          <div className="menu-mobile">
            <div className="link-mobile float-right">
              <button 
                className="navbar-toggler float-right py-2 ml-0" 
                type="button" 
                onClick={() => setIsMenuOpen(!isMenuOpen)}
                aria-label="Toggle navigation"
              >
                <span className="icon icon-menu"></span>
              </button>
            </div>
          </div>
          
          <div className={`collapse navbar-collapse p-3 p-lg-0 ${isMenuOpen ? 'show' : ''}`} id="navbarNavAltMarkup">
            <div className="navbar-nav ml-auto">
              <a className="nav-item nav-link br-01 px-4 underline-effect" href="/bookings" title="My Bookings" aria-label="My Bookings">
                My Bookings
              </a>
              
              <div className="position-relative dropdown">
                <a className="nav-item nav-link br-01 px-4 underline-effect" href="#" id="dropdown-collaborators">
                  Travel agencies
                </a>
                <div className="dropdown-menu" aria-labelledby="dropdown-collaborators">
                  <div className="dropdown-item">
                    <a className="col-12 pt-1 pb-2 px-3 link-hover link-ln" title="Register new agency" href="/register">
                      Register
                    </a>
                    <a className="col-12 pt-1 pb-2 px-3 link-hover link-ln" href="/login" title="Partner login">
                      <span>Login</span>
                    </a>
                    <a className="col-12 pt-1 pb-2 px-3 link-hover link-ln" href="/b2b" title="More information">
                      <span>More information</span>
                    </a>
                  </div>
                </div>
              </div>
              
              <a className="nav-item nav-link active px-4 underline-effect" href="/help" title="Help Centre" aria-label="Help Centre">
                Help Centre
              </a>
              
              {/* Language Dropdown */}
              <div className="position-relative dropdown">
                <a 
                  className="nav-item nav-link br-01 px-3 underline-effect d-flex align-items-center" 
                  href="#" 
                  id="dropdown-language"
                  onClick={(e) => {
                    e.preventDefault();
                    setIsLanguageOpen(!isLanguageOpen);
                    setIsCurrencyOpen(false);
                  }}
                  style={{ cursor: 'pointer' }}
                >
                  <span className="icon icon-language me-2"></span>
                  <span className="me-1">{selectedLanguage}</span>
                  <span className="icon icon-arrow-down"></span>
                </a>
                <div className={`dropdown-menu dropdown-menu-right ${isLanguageOpen ? 'show' : ''}`} aria-labelledby="dropdown-language">
                  <div className="dropdown-item">
                    <a 
                      className="col-12 pt-1 pb-2 px-3 link-hover link-ln" 
                      href="#" 
                      title="English"
                      onClick={(e) => {
                        e.preventDefault();
                        setSelectedLanguage('EN');
                        setIsLanguageOpen(false);
                      }}
                    >
                      English
                    </a>
                    <a 
                      className="col-12 pt-1 pb-2 px-3 link-hover link-ln" 
                      href="#" 
                      title="Türkçe"
                      onClick={(e) => {
                        e.preventDefault();
                        setSelectedLanguage('TR');
                        setIsLanguageOpen(false);
                      }}
                    >
                      Türkçe
                    </a>
                    <a 
                      className="col-12 pt-1 pb-2 px-3 link-hover link-ln" 
                      href="#" 
                      title="Español"
                      onClick={(e) => {
                        e.preventDefault();
                        setSelectedLanguage('ES');
                        setIsLanguageOpen(false);
                      }}
                    >
                      Español
                    </a>
                    <a 
                      className="col-12 pt-1 pb-2 px-3 link-hover link-ln" 
                      href="#" 
                      title="Français"
                      onClick={(e) => {
                        e.preventDefault();
                        setSelectedLanguage('FR');
                        setIsLanguageOpen(false);
                      }}
                    >
                      Français
                    </a>
                  </div>
                </div>
              </div>
              
              {/* Currency Dropdown */}
              <div className="position-relative dropdown">
                <a 
                  className="nav-item nav-link br-01 px-3 underline-effect d-flex align-items-center" 
                  href="#" 
                  id="dropdown-currency"
                  onClick={(e) => {
                    e.preventDefault();
                    setIsCurrencyOpen(!isCurrencyOpen);
                    setIsLanguageOpen(false);
                  }}
                  style={{ cursor: 'pointer' }}
                >
                  <span className="icon icon-currency me-2"></span>
                  <span className="me-1">{selectedCurrency}</span>
                  <span className="icon icon-arrow-down"></span>
                </a>
                <div className={`dropdown-menu dropdown-menu-right ${isCurrencyOpen ? 'show' : ''}`} aria-labelledby="dropdown-currency">
                  <div className="dropdown-item">
                    <a 
                      className="col-12 pt-1 pb-2 px-3 link-hover link-ln" 
                      href="#" 
                      title="Euro"
                      onClick={(e) => {
                        e.preventDefault();
                        setSelectedCurrency('EUR');
                        setIsCurrencyOpen(false);
                      }}
                    >
                      EUR - Euro
                    </a>
                    <a 
                      className="col-12 pt-1 pb-2 px-3 link-hover link-ln" 
                      href="#" 
                      title="US Dollar"
                      onClick={(e) => {
                        e.preventDefault();
                        setSelectedCurrency('USD');
                        setIsCurrencyOpen(false);
                      }}
                    >
                      USD - US Dollar
                    </a>
                    <a 
                      className="col-12 pt-1 pb-2 px-3 link-hover link-ln" 
                      href="#" 
                      title="Turkish Lira"
                      onClick={(e) => {
                        e.preventDefault();
                        setSelectedCurrency('TRY');
                        setIsCurrencyOpen(false);
                      }}
                    >
                      TRY - Turkish Lira
                    </a>
                    <a 
                      className="col-12 pt-1 pb-2 px-3 link-hover link-ln" 
                      href="#" 
                      title="British Pound"
                      onClick={(e) => {
                        e.preventDefault();
                        setSelectedCurrency('GBP');
                        setIsCurrencyOpen(false);
                      }}
                    >
                      GBP - British Pound
                    </a>
                  </div>
                </div>
              </div>
              
              <a className="nav-item nav-link c-login mx-2 px-3 mx-lg-2 my-2 my-lg-0 underline-effect" href="/login">
                <span className="icon icon-person"></span>
                <span>Login</span>
              </a>
            </div>
          </div>
        </div>
      </nav>
    </header>
  );
}