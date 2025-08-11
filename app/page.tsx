import OriginalHeader from './Header';
import OriginalFooter from './Footer';

export default function HomePage() {
  return (
    <>
      {/* Banner Section with Header */}
      <section className="container-fluid c-banner">
        <OriginalHeader />

        {/* Hero Content */}
        <div className="row">
          <div className="col-12">
            <div className="container z-index-search">
              <div className="row my-4">
                <div className="col-12 text-left">
                  <h1 className="cb-txt">Are you looking for airport transfers?</h1>
                  <p className="cb-txt-subtitle">You have come to the right place</p>
                </div>
                <div className="col-12 col-lg-7 c-mobile"></div>
              </div>

              {/* Search Form */}
              <div className="row c-box">
                <form className="col-12">
                  <div className="container my-2">
                    <div className="my-row">
                      {/* Trip Type Switch */}
                      <div className="switch-col tab-content p-2">
                        <div className="two-part-switch f-bold">
                          <input type="radio" id="switch-oneway" name="trip_type" className="switch-checkbox" value="oneway" defaultChecked />
                          <label htmlFor="switch-oneway" className="switch-option active">One way</label>
                          <input type="radio" id="switch-roundtrip" name="trip_type" className="switch-checkbox" value="roundtrip" />
                          <label htmlFor="switch-roundtrip" className="switch-option">Round trip</label>
                        </div>
                      </div>

                      {/* Passengers (Desktop) */}
                      <div className="passengers-col tab-content p-2">
                        <div className="c-form position-relative">
                          <label className="p-2 label-passenger">Passengers</label>
                          <div className="value form-control-passengers">2 Adults</div>
                          <span className="icon-passenger icon-people"></span>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Search Fields */}
                  <div className="container tab-content">
                    <div className="my-row py-4">
                      {/* From */}
                      <div className="input-pick-up">
                        <div className="c-form position-relative">
                          <label className="p-2 label-title">From</label>
                          <input type="text" className="form-control" placeholder="Pickup location" />
                          <span className="icon icon-location"></span>
                        </div>
                      </div>

                      {/* To */}
                      <div className="input-drop-off">
                        <div className="c-form position-relative">
                          <label className="p-2 label-title">To</label>
                          <input type="text" className="form-control" placeholder="Dropoff location" />
                          <span className="icon icon-location"></span>
                        </div>
                      </div>

                      {/* Date and Time */}
                      <div className="group-arrival-datetime">
                        <div className="input-date-arrival">
                          <div className="c-form position-relative">
                            <label className="p-2 label-title">Pickup date</label>
                            <input type="date" className="form-control" />
                            <span className="icon icon-calendar"></span>
                          </div>
                        </div>
                        <div className="input-time-arrival">
                          <div className="c-form position-relative">
                            <label className="p-2 label-title">Time</label>
                            <input type="time" className="form-control" defaultValue="12:00" />
                            <span className="icon icon-clock"></span>
                          </div>
                        </div>
                      </div>

                      {/* Return Date (Hidden by default) */}
                      <div className="group-departure-datetime">
                        <div className="input-date-departure">
                          <div className="c-form position-relative">
                            <label className="p-2 label-title">Return date</label>
                            <div className="add-return pointer">+ Add return</div>
                          </div>
                        </div>
                        <div className="input-time-departure pointer">
                          <div className="c-form position-relative">
                            <label className="p-2 label-title">Time</label>
                          </div>
                        </div>
                      </div>

                      {/* Passengers (Mobile) */}
                      <div className="input-passengers">
                        <div className="c-form position-relative">
                          <label className="p-2 label-title">Passengers</label>
                          <div className="value form-control">2 Adults</div>
                          <span className="icon icon-people"></span>
                        </div>
                      </div>

                      {/* Search Button */}
                      <div className="button">
                        <button className="f-bold font-14" type="submit">Search</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Banner Home Section */}
      <section className="container-fluid c-banner-home">
        <div className="container">
          <div className="row">
            <div className="col-6 py-4 py-lg-5"></div>
            <div className="col-12 col-md-6 py-4 py-lg-5 mb-4 mb-lg-0">
              <div className="text-white text-right f-bold h5 text-bg-airport">
                Book your transfer to the Airport or your private ride with ease. Enjoy our service at best rates available.
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Why We Are Different */}
      <section className="container-fluid py-4 c-why">
        <div className="container pt-1 pt-4">
          <div className="row">
            <div className="col-12">
              <h2 className="py-2 text-center m-0 font-30 mb-4">Discover why we are different</h2>
            </div>
            <div className="row mt-2">
              <div className="col-12 col-md-3 mb-4 text-center">
                <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/door-to-door.png" alt="Door-to-door" className="mr-3" style={{ height: '90px' }} />
                <h3 className="text-center font-16 f-bold mb-1">Door To Door</h3>
                <p className="text-center font-14">From the Airport directly to your destination</p>
              </div>
              <div className="col-12 col-md-3 mb-4 text-center">
                <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/private-transfers.png" alt="Private Transfers" className="mr-3" style={{ height: '90px' }} />
                <h3 className="text-center font-16 f-bold mb-1">Private Transfers</h3>
                <p className="text-center font-14">We offer only private transfers, no shared service</p>
              </div>
              <div className="col-12 col-md-3 mb-4 text-center">
                <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/meet-greet.png" alt="Meet&Greet" className="mr-3" style={{ height: '90px' }} />
                <h3 className="text-center font-16 f-bold mb-1">Meet & Greet</h3>
                <p className="text-center font-14">Our driver will meet & greet you in the arrivals hall</p>
              </div>
              <div className="col-12 col-md-3 mb-4 text-center">
                <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/call-center.png" alt="Customer Support" className="mr-3" style={{ height: '90px' }} />
                <h3 className="text-center font-16 f-bold mb-1">24/7 Customer Support</h3>
                <p className="text-center font-14">We are here to help! Before, during and after your trip</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Divider */}
      <div className="the_divider">
        <div style={{ backgroundColor: '#fff', position: 'absolute', left: '50%', transform: 'translate(-50%, 0)', top: '-21px' }}>
          <img loading="lazy" src="/images/arrow-mytransfers.svg" alt="Arrow-MyTransfers" style={{ width: '70px', paddingRight: '20px', paddingLeft: '20px' }} />
        </div>
      </div>

      {/* Reviews Section */}
      <section className="container-fluid py-4 my-4">
        <div className="container mb-4">
          <div className="row">
            <div className="col-12">
              <h2 className="py-2 text-center m-0 f-bold font-30 mb-3">Our Reviews</h2>
              <div>
                <div className="col-12 c-c-item mb-3 id-summary bg-white last-block-mobile hide-summary text-center">
                  <span className="font-20 mr-2 position-less-t-5">
                    Rated <strong>Excellent</strong> <span>Based on <strong>7,420reviews</strong> on</span>
                    <span>
                      <span className="icon icon-star-line color-secundary font-30 position-t-4"></span>
                      <span className="font-20"><strong>Trustpilot</strong></span>
                    </span>
                  </span>
                </div>
              </div>
            </div>
            <div className="col-12">
              <div className="d-flex flex-nowrap scroll-container justify-content-start justify-content-md-center">
                <div className="review-card mx-2">
                  <div className="stars-wrapper">
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                  </div>
                  <p className="font-weight-bold font-16 mt-3">Excellent Service</p>
                  <p className="review-text">All perfect. Driver was waiting ahead of time. Nice, comfortable car and smooth drive. Thanks!</p>
                  <p className="review-user font-14"><strong>PiotrZawadzki </strong></p>
                </div>
                <div className="review-card mx-2">
                  <div className="stars-wrapper">
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                  </div>
                  <p className="font-weight-bold font-16 mt-3">Excellent Service</p>
                  <p className="review-text">Thank you B.Junior for the friendly welcome</p>
                  <p className="review-user font-14"><strong>ShahramAzamy </strong></p>
                </div>
                <div className="review-card mx-2">
                  <div className="stars-wrapper">
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                  </div>
                  <p className="font-weight-bold font-16 mt-3">Excellent Service</p>
                  <p className="review-text">I arrived at Cancun airport pleasantly surprised to find my driver immediately, I was told the drive would be 20 minutes to my resort, it felt like...</p>
                  <p className="review-user font-14"><strong>RosalinaRostkowski </strong></p>
                </div>
                <div className="review-card mx-2">
                  <div className="stars-wrapper">
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                    <div className="stars">★</div>
                  </div>
                  <p className="font-weight-bold font-16 mt-3">Excellent Service</p>
                  <p className="review-text">Over the top service!  100% Great!</p>
                  <p className="review-user font-14"><strong>JamesRobinson </strong></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Divider */}
      <div className="the_divider">
        <div style={{ backgroundColor: '#fff', position: 'absolute', left: '50%', transform: 'translate(-50%, 0)', top: '-21px' }}>
          <img loading="lazy" src="/images/arrow-mytransfers.svg" alt="Arrow-MyTransfers" style={{ width: '70px', paddingRight: '20px', paddingLeft: '20px' }} />
        </div>
      </div>

      {/* Did You Know Section */}
      <section className="container-fluid py-4 my-4">
        <div className="container">
          <div className="row">
            <div className="col-12">
              <h2 className="py-2 text-center m-0 font-30 mb-3 f-bold">Did you know</h2>
              <div>
                <ul className="m-0 list-unstyled">
                  <li className="py-2 mb-2 m-0 text-wrap w-100 d-flex align-items-start">
                    <img className="width-check-circule flex-shrink-0" src="/images/check_circle.svg" alt="" />
                    <span className="font-16 ms-2">We know that finding a taxi service in a foreign country can be difficult and stressful. Book ahead and enjoy the best service with all inclusive, no surprises.</span>
                  </li>
                  <li className="py-2 mb-2 m-0 text-wrap w-100 d-flex align-items-start">
                    <img className="width-check-circule flex-shrink-0" src="/images/check_circle.svg" alt="" />
                    <span className="font-16 ms-2">MyTransfers offers reliable transfers to and from major airports around the world, safer, more comfortable, and cheaper than the taxi service. We have an appropriate vehicle for every situation.</span>
                  </li>
                  <li className="py-2 mb-2 m-0 text-wrap w-100 d-flex align-items-start">
                    <img className="width-check-circule flex-shrink-0" src="/images/check_circle.svg" alt="" />
                    <span className="font-16 ms-2">You can choose from several available vehicles, so you can always enjoy maximum comfort. Whether you are traveling alone, in pairs or in groups, we have the perfect vehicle for every occasion.</span>
                  </li>
                  <li className="py-2 mb-2 m-0 text-wrap w-100 d-flex align-items-start">
                    <img className="width-check-circule flex-shrink-0" src="/images/check_circle.svg" alt="" />
                    <span className="font-16 ms-2">The driver will pick you up directly from the airport terminal, it will help with your luggage and take you directly to your hotel or any other destination, and vice versa.</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Divider */}
      <div className="the_divider">
        <div style={{ backgroundColor: '#fff', position: 'absolute', left: '50%', transform: 'translate(-50%, 0)', top: '-21px' }}>
          <img loading="lazy" src="/images/arrow-mytransfers.svg" alt="Arrow-MyTransfers" style={{ width: '70px', paddingRight: '20px', paddingLeft: '20px' }} />
        </div>
      </div>

      {/* Vehicle Types Section */}
      <section className="container-fluid py-4 c-cars">
        <div className="container">
          <div className="row">
            <div className="col-12 p-0">
              <h2 className="mt-4 text-center m-0 font-30">Maximum comfort and safety during your trip</h2>
              <p className="text-center mt-0">Authorized vehicles, experienced drivers</p>
              <div className="cars-container">
                <div className="cars-wrapper">
                  <div className="item px-0">
                    <figure>
                      <img className="" width="370" height="170" data-src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-1.jpg" data-srcset="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-1.jpg 1x" title="ECONOMY CLASS" alt="ECONOMY CLASS" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-1.jpg" srcSet="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-1.jpg 1x" />
                    </figure>
                    <h3 className="text-center font-16 f-bold">ECONOMY CLASS</h3>
                    <p className="text-center font-14">For a couple or a family with children</p>
                  </div>
                  <div className="item px-0">
                    <figure>
                      <img className="" width="370" height="170" data-src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-2.jpg" data-srcset="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-2.jpg 1x" title="BUSINESS CLASS" alt="BUSINESS CLASS" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-2.jpg" srcSet="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-2.jpg 1x" />
                    </figure>
                    <h3 className="text-center font-16 f-bold">BUSINESS CLASS</h3>
                    <p className="text-center font-14">Comfortable for business trips</p>
                  </div>
                  <div className="item px-0">
                    <figure>
                      <img className="" width="370" height="170" data-src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-3.jpg" data-srcset="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-3.jpg 1x" title="FOR GROUPS" alt="FOR GROUPS" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-3.jpg" srcSet="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/car-3.jpg 1x" />
                    </figure>
                    <h3 className="text-center font-16 f-bold">FOR GROUPS</h3>
                    <p className="text-center font-14">For groups up to 19 people or with large luggage</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* How It Works Section */}
      <section className="container-fluid py-4">
        <div className="container">
          <div className="row">
            <div className="col-12">
              <div className="card_container_how_works">
                <h2 className="mb-2 text-center m-0 font-30">How it works</h2>
                <div className="row">
                  <div className="col-lg">
                    <div className="steppa">1</div>
                    <h3 className="titolo_step">Book your airport transfer worldwide</h3>
                    <div className="desc_step">
                      Mytransfers offers private airport transfers bookings with instant confirmation worldwide. We work directly with local drivers and select the best services for our clients. In this way we ensure that your vacation will start in the best possible way.
                    </div>
                    <div className="desc_step">
                      We constantly review our service to ensure that you will enjoy your ride with Mytransfers in any destination you choose.
                    </div>
                  </div>
                  <div className="col-lg">
                    <div className="steppa">2</div>
                    <h3 className="titolo_step">Our easy transfer booking process</h3>
                    <div className="desc_step">
                      Choose your origin and destination and in just 3 click you will have your private transfer booked. Forget about long waiting taxi lines at the airport, our driver will be waiting for you with a sign at your arrival.
                    </div>
                  </div>
                  <div className="col-lg">
                    <div className="steppa">3</div>
                    <h3 className="titolo_step">Airport,  Port or Train Stations</h3>
                    <div className="desc_step">
                      With us you can book your private transfer from any airport, port or train station. We monitor the arrival of the means of transport to ensure that your transfer will be carried out on time and with the maximum guarantees of quality and safety.
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Popular Destinations */}
      <section className="container-fluid py-4">
        <div className="container">
          <div className="row">
            <div className="col-12">
              <h2 className="mt-4 text-md-left text-center m-0 font-30 mb-4">The most popular destinations</h2>
            </div>
            <div className="col-12 col-md-4 px-sm-2 mt-2 mb-3 mb-md-2">
              <div className="card card-airport" style={{ width: 'auto' }}>
                <a href="https://www.mytransfers.com/en/destination/spain/palma-mallorca-airport/" className="destination_title " title="Palma Airport">
                  <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/airports/47.jpg" className="card-img-top" alt="..." />
                  <div className="card-body">
                    <p className="font-16 f-bold mb-0 text-black">Palma Airport</p>
                  </div>
                </a>
              </div>
            </div>
            <div className="col-12 col-md-4 px-sm-2 mt-2 mb-3 mb-md-2">
              <div className="card card-airport" style={{ width: 'auto' }}>
                <a href="https://www.mytransfers.com/en/destination/spain/barcelona-airport/" className="destination_title " title="Barcelona Airport">
                  <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/airports/85.jpg" className="card-img-top" alt="..." />
                  <div className="card-body">
                    <p className="font-16 f-bold mb-0 text-black">Barcelona Airport</p>
                  </div>
                </a>
              </div>
            </div>
            <div className="col-12 col-md-4 px-sm-2 mt-2 mb-3 mb-md-2">
              <div className="card card-airport" style={{ width: 'auto' }}>
                <a href="https://www.mytransfers.com/en/destination/spain/malaga-airport/" className="destination_title " title="Málaga Airport">
                  <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/airports/151.jpg" className="card-img-top" alt="..." />
                  <div className="card-body">
                    <p className="font-16 f-bold mb-0 text-black">Málaga Airport</p>
                  </div>
                </a>
              </div>
            </div>
            <div className="col-12 col-md-4 px-sm-2 mt-2 mb-3 mb-md-2">
              <div className="card card-airport" style={{ width: 'auto' }}>
                <a href="https://www.mytransfers.com/en/destination/spain/gran-canaria-airport/" className="destination_title " title="Gran Canaria Airport">
                  <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/airports/705.jpg" className="card-img-top" alt="..." />
                  <div className="card-body">
                    <p className="font-16 f-bold mb-0 text-black">Gran Canaria Airport</p>
                  </div>
                </a>
              </div>
            </div>
            <div className="col-12 col-md-4 px-sm-2 mt-2 mb-3 mb-md-2">
              <div className="card card-airport" style={{ width: 'auto' }}>
                <a href="https://www.mytransfers.com/en/destination/spain/tenerife-south-airport/" className="destination_title " title="Tenerife South Airport">
                  <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/airports/799.jpg" className="card-img-top" alt="..." />
                  <div className="card-body">
                    <p className="font-16 f-bold mb-0 text-black">Tenerife South Airport</p>
                  </div>
                </a>
              </div>
            </div>
            <div className="col-12 col-md-4 px-sm-2 mt-2 mb-3 mb-md-2">
              <div className="card card-airport" style={{ width: 'auto' }}>
                <a href="https://www.mytransfers.com/en/destination/spain/lanzarote-airport/" className="destination_title " title="Lanzarote Airport">
                  <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/airports/859.jpg" className="card-img-top" alt="..." />
                  <div className="card-body">
                    <p className="font-16 f-bold mb-0 text-black">Lanzarote Airport</p>
                  </div>
                </a>
              </div>
            </div>
            <div className="col-12 col-md-4 px-sm-2 mt-2 mb-3 mb-md-2">
              <div className="card card-airport" style={{ width: 'auto' }}>
                <a href="https://www.mytransfers.com/en/destination/spain/fuerteventura-airport/" className="destination_title " title="Fuerteventura Airport">
                  <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/airports/919.jpg" className="card-img-top" alt="..." />
                  <div className="card-body">
                    <p className="font-16 f-bold mb-0 text-black">Fuerteventura Airport</p>
                  </div>
                </a>
              </div>
            </div>
            <div className="col-12 col-md-4 px-sm-2 mt-2 mb-3 mb-md-2">
              <div className="card card-airport" style={{ width: 'auto' }}>
                <a href="https://www.mytransfers.com/en/destination/spain/alicante-airport/" className="destination_title " title="Alicante Airport">
                  <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/airports/1031.jpg" className="card-img-top" alt="..." />
                  <div className="card-body">
                    <p className="font-16 f-bold mb-0 text-black">Alicante Airport</p>
                  </div>
                </a>
              </div>
            </div>
            <div className="col-12 col-md-4 px-sm-2 mt-2 mb-3 mb-md-2">
              <div className="card card-airport" style={{ width: 'auto' }}>
                <a href="https://www.mytransfers.com/en/destination/spain/madrid-airport-barajas-mad/" className="destination_title " title="Madrid Airport">
                  <img loading="lazy" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/airports/1095.jpg" className="card-img-top" alt="..." />
                  <div className="card-body">
                    <p className="font-16 f-bold mb-0 text-black">Madrid Airport</p>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Mobile App Section */}
      <section className="container-fluid pt-4" style={{ paddingBottom: '0px !important' }}>
        <div className="container">
          <div className="row">
            <div className="col-12 col-md-5" style={{ alignContent: 'center' }}>
              <p className="pt-4 f-semibold text-start mb-0 font-30">Easy Booking </p>
              <p className="pb-4 pt-2 text-start mt-0 m-0 font-30">Download the Mytransfers app on your mobile phone.</p>
              <div className="row">
                <div className="col-12">
                  <a href="https://play.google.com/store/apps/details?id=com.mytransfers.app" target="_blank" title="Google Play - Mytransfers">
                    <img className="" width="150" height="45" data-src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/app_logos/googleplay.png" data-srcset="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/app_logos/googleplay.png 1x" alt="Google Play" title="Google Play" style={{ width: '150px', height: '45px' }} src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/app_logos/googleplay.png" srcSet="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/app_logos/googleplay.png 1x" />
                  </a>
                  <a href="https://apps.apple.com/us/app/mytransfers/id1618322222" target="_blank" title="App Store - Mytransfers">
                    <img className="" width="150" height="45" data-src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/app_logos/appstore.png" data-srcset="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/app_logos/appstore.png 1x" alt="App Store" title="App Store" style={{ width: '150px', height: '45px' }} src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/app_logos/appstore.png" srcSet="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/app_logos/appstore.png 1x" />
                  </a>
                </div>
              </div>
            </div>
            <div className="col-12 col-md-7 center-devices-mobiles">
              <img loading="lazy" className="img-devices-app" src="https://d1cj8q6w07zyiq.cloudfront.net/mytransfersweb/prod/images/mobiles-app.webp" alt="" />
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <OriginalFooter />
    </>
  );
}