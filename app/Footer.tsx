export default function OriginalFooter() {
  return (
    <footer className="container-fluid bg-black text-white pt-5">
      <div className="container">
        <div className="row text-left">
          <div className="col-12 col-md-3 mb-4">
            <p className="font-weight-bold font-22">Most popular</p>
            <ul className="list-unstyled">
              <li><a href="/bookings" className="text-white font-16">My Bookings</a></li>
              <li><a href="/register" className="text-white font-16">Create an account</a></li>
              <li><a href="#" className="text-white font-16">Get the App</a></li>
              <li><a className="text-white font-16" href="/help" title="Help Centre">Help Centre</a></li>
            </ul>
          </div>
          <div className="col-12 col-md-3 mb-4">
            <p className="font-weight-bold font-22">Office</p>
            <ul className="list-unstyled">
              <li><a className="text-white font-16" href="/about-us" title="About Us">About Us</a></li>
              <li><a className="text-white font-16" href="/b2b" title="Travel Agencies">Travel Agencies</a></li>
              <li><a className="text-white font-16" href="/drivers" title="Travel Partners">Drive with us</a></li>
              <li><a className="text-white font-16" href="/driver-platform" title="Travel Partners">Driver platform</a></li>
            </ul>
          </div>
          <div className="col-12 col-md-3 mb-4">
            <p className="font-weight-bold font-22">Legal</p>
            <ul className="list-unstyled">
              <li><a className="text-white font-16" href="/terms" title="Terms & Conditions">Terms & Conditions</a></li>
              <li><a className="text-white font-16" href="/privacy" title="Privacy Policy">Privacy Policy</a></li>
              <li><a className="text-white font-16" href="/cookies" title="Cookie Policy">Cookie Policy</a></li>
            </ul>
          </div>
          <div className="col-12 col-md-3 mb-4">
            <p className="font-weight-bold font-22">Location</p>
            <div className="mb-2">
              <p className="small mb-0 font-16">C/ D&apos;En Bosc 16 Palma de Mallorca Illes Balears 07002 ES | (+34) 911 880 435</p>
            </div>
          </div>
        </div>
        <hr className="border-secondary" />
        <div className="d-flex flex-column flex-md-row justify-content-between align-items-center py-3">
          <img src="/images/logo.png" alt="MyTransfers" style={{height: '50px', width: 'auto'}} />
          <p className="small f-bold mb-0 mt-3 mt-md-0">
            © MyTransfers. 2025. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  );
}