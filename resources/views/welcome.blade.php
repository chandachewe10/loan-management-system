<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link
      href="https://fonts.googleapis.com/css2?family=Hind:wght@600&family=Open+Sans:wght@400;600&display=swap"
      rel="stylesheet"
    />
    <link href="{{ asset('landingPage/css/style.css') }}" rel="stylesheet" />

    <title>{{ config('app.name') }}</title>

    <!-- Open Graph (Facebook & LinkedIn) -->
    <meta property="og:title" content="{{ config('app.name') }}" />
    <meta property="og:description" content="Lendfy is a loan management software that helps lenders automate workflows, reduce manual work, and launch new products with ease." />
    <meta property="og:image" content="{{ asset('logo.PNG') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />
    <meta property="og:locale" content="en_US" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="LendFy Loan Management" />
    <meta name="twitter:title" content="{{ config('app.name') }}" />
    <meta name="twitter:description" content="Lendfy is a loan management software that helps lenders automate workflows, reduce manual work, and launch new products with ease." />
    <meta name="twitter:image" content="{{ asset('logo.PNG') }}" />
    <meta name="twitter:url" content="{{ url()->current() }}" />
    <meta name="twitter:site" content="" />

    <!-- LinkedIn Enhancements -->
    <meta name="linkedin:owner" content="" />
    <meta name="linkedin:card" content="LendFy Loan Management" />
</head>

  <body>
    <header class="header">
      <div class="container">
        <div class="header__wrapper">
          <a class="c-link" href="#">
            <div class="c-logo">
              <img src="{{ asset('logo.jpg') }}" alt="Logo" class="c-logo__img" style="border-radius:100%" />
            </div>
          </a>
          <nav class="c-nav">
            <input id="dropdown" class="c-nav__toggle" type="checkbox" />
            <div class="c-nav__content">
              <ul class="c-list c-list--flex">
                <li class="c-list__item">
                  <a href="#pricing" class="c-link c-link--list">Pricing</a>
                </li>
                <li class="c-list__item">
                  <a href="#services" class="c-link c-link--list">Services</a>
                </li>
              </ul>
              <a href="{{ 'admin/register' }}" class="c-button c-button--primary" style="color:white; text-decoration:none;">Sign Up</a>
            </div>
          </nav>
        </div>
      </div>
    </header>
<style> 
.c-hero__button-group {
  position: relative;
  z-index: 100; /* Ensure it stays above other elements */
}

.c-button {
  position: relative;
  touch-action: manipulation; /* Improves touch responsiveness */
  -webkit-tap-highlight-color: rgba(0,0,0,0.1); /* Visual feedback on tap */
}

/* Mobile-specific fixes */
@media (max-width: 768px) {
  .c-hero__button-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px; /* Replaces the <br> with better spacing */
  }
  
  .c-button {
    width: 100%;
    max-width: 200px; /* Limits width but keeps buttons sizable */
  }
}
</style>
    <main>
      <section class="section">
        <div class="c-hero">
          <div class="container">
            <div class="c-hero__content">
              <h1 class="heading heading--1 heading--light">
                Grow Your Lending Business. Ditch the Spreadsheets
              </h1>
              <h4 class="heading heading--4 heading--blue">
                Lendfy is a loan management software that helps lenders automate workflows, reduce manual work, and launch new products with ease.
              </h4>
              <div class="c-hero__button-group">
  <a href="{{ 'admin/login' }}" 
     class="c-button c-button--primary" 
     style="color:white; text-decoration:none; display: inline-block; min-width: 120px; min-height: 44px; padding: 12px 24px; margin: 8px 0;">
     Login
  </a>
  <a href="{{ 'admin/register' }}" 
     class="c-button c-button--secondary" 
     style="color:white; text-decoration:none; display: inline-block; min-width: 120px; min-height: 44px; padding: 12px 24px; margin: 8px 0;">
     Sign Up
  </a>
</div>
            </div>

            <div class="c-hero__img-holder">
              <img
                class="c-hero__img"
                src="{{ asset('landingPage/img/dashboard2.PNG') }}"
                alt="Dashboard"
              />
            </div>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="container">
          <div class="section__title-wrapper">
            <h2 class="heading heading--2">Fully Automated</h2>
            <h4 class="heading heading--4">
              Send personalized loan offers, agreements, and payment reminders via email or SMS. Predefined templates automate outreach, ensuring customers stay informed without adding to your workload.
            </h4>
          </div>

          <div class="box box--flex">
            <article class="c-card">
              <div class="c-card__content">
                <h3 class="c-card__title heading heading--3">
                  Seamless collaboration
                </h3>
                <p class="c-paragraph c-card__text">
                  Lendfy was built with ease of use in mind. Take a look at the user interface for LendFy and you will be sure to understand where the difference lies.
                </p>
              </div>
              <div class="c-card__img-holder">
                <img class="c-card__img" src="{{ asset('landingPage/img/card-1.png') }}" alt="" />
              </div>
            </article>

            <article class="c-card">
              <div class="c-card__content">
                <h3 class="c-card__title heading heading--3">
                  Live Updates
                </h3>
                <p class="c-paragraph c-card__text">
                  Give customers instant reports to the status of their loan details, repayment schedules, and application statuses through <a href="https://swift-sms.net">Bulk SMS</a> Portal 
                </p>
              </div>
              <div class="c-card__img-holder">
                <img class="c-card__img" src="{{ asset('landingPage/img/card-2.png') }}" alt="" />
              </div>
            </article>

            <article class="c-card">
              <div class="c-card__content">
                <h3 class="c-card__title heading heading--3">
                  Flexibility
                </h3>
                <p class="c-paragraph c-card__text">
                  Pre-configured loan agreement templates, settlements, and workflows are available to simplify loan management and ensure efficient operations.
                </p>
              </div>
              <div class="c-card__img-holder">
                <img class="c-card__img" src="{{ asset('landingPage/img/card-3.png') }}" alt="" />
              </div>
            </article>
          </div>
        </div>
      </section>

      <section class="section features" id="services">
        <div class="container">
          <div class="box box--grid">
            <article class="c-feature">
              <div class="c-feature__img-holder">
                <img class="c-feature__img" src="{{ asset('landingPage/img/feature-1.png') }}" alt="" />
              </div>
              <div class="c-feature__content">
                <h3 class="c-feature__title heading heading--3">
                  Robust workflow
                </h3>
                <p class="c-paragraph c-feature__text">
                  LendFy, with its built-in CRM also comes with built-in accounting service tools like expense management to better manage your business.
                </p>
              </div>
            </article>

            <article class="c-feature">
              <div class="c-feature__img-holder">
                <img class="c-feature__img" src="{{ asset('landingPage/img/feature-2.png') }}" alt="" />
              </div>
              <div class="c-feature__content">
                <h3 class="c-feature__title heading heading--3">
                  Cloud Based
                </h3>
                <p class="c-paragraph c-feature__text">
                  LendFy is a cloud-based hosted service. There is no need for you to have a website, servers or configure databases to start using our system.
                </p>
              </div>
            </article>

            <article class="c-feature">
              <div class="c-feature__img-holder">
                <img class="c-feature__img" src="{{ asset('landingPage/img/feature-3.png') }}" alt="" />
              </div>
              <div class="c-feature__content">
                <h3 class="c-feature__title heading heading--3">
                  Scalability
                </h3>
                <p class="c-paragraph c-feature__text">
                  If your business has a website you can integrate it with LendFy to receive applications over API. Alternatively, if you don’t have a website we can always help you build one.
                </p>
              </div>
            </article>
          </div>

         
        </div>
      </section>

      <section class="section" id="pricing">
        <div class="container">
          <div class="section__title-wrapper">
            <h2 class="heading heading--2">Simple pricing</h2>
            <h4 class="heading heading--4">
              LendFy is a SAAS platform and our pricing model is based on your growth. It starts from $20 per month and increases as your business grows.
            </h4>
          </div>

          <div class="box box--grid box--gap2">
            <article class="c-price">
              <div class="c-price__header">
                <div class="c-price__amount">
                  <h3 class="c-price__amount--title heading heading--3">
                    <span class="c-price__amount--secondary">$</span>
                    <span class="c-price__amount--primary">20</span>
                    <span class="c-price__amount--secondary">/m</span>
                  </h3>
                </div>
                <p class="c-paragraph c-price__text">
                  You can cancel or change your plan at any time
                </p>
                <h5 class="heading heading--4 c-price__title">
                  What's included
                </h5>
              </div>
              <ul class="c-list">
                <li class="c-list__item c-price__list-item">
                  <div class="c-list__icon c-list__icon--check"></div>
                  <div class="c-list__text">Unlimited Users</div>
                </li>
                <li class="c-list__item c-price__list-item">
                  <div class="c-list__icon c-list__icon--check"></div>
                  <div class="c-list__text">1000 Loans Max</div>
                </li>
                <li class="c-list__item c-price__list-item">
                  <div class="c-list__icon c-list__icon--check"></div>
                  <div class="c-list__text">All other features Included</div>
                </li>
              </ul>
             
            </article>

            <article class="c-price c-price--highlight">
              <div class="c-price__header">
                <div class="c-price__amount">
                  <h3 class="c-price__amount--title heading heading--3">
                    <span class="c-price__amount--secondary">$</span>
                    <span class="c-price__amount--primary">54</span>
                    <span class="c-price__amount--secondary">/m</span>
                  </h3>
                </div>
                <p class="c-paragraph c-price__text">
                  You can cancel or change your plan at any time.
                </p>
                <h5 class="heading heading--4 c-price__title">
                  What's included
                </h5>
              </div>
              <ul class="c-list">
                <li class="c-list__item c-price__list-item">
                  <div class="c-list__icon c-list__icon--check"></div>
                  <div class="c-list__text">Unlimited Users</div>
                </li>
                <li class="c-list__item c-price__list-item">
                  <div class="c-list__icon c-list__icon--check"></div>
                  <div class="c-list__text">10,000 Loans Max</div>
                </li>
                <li class="c-list__item c-price__list-item">
                  <div class="c-list__icon c-list__icon--check"></div>
                  <div class="c-list__text">All other features Included</div>
                </li>
              </ul>
             
            </article>

            <article class="c-price">
              <div class="c-price__header">
                <div class="c-price__amount">
                  <h3 class="c-price__amount--title heading heading--3">
                    <span class="c-price__amount--secondary">$</span>
                    <span class="c-price__amount--primary">120</span>
                    <span class="c-price__amount--secondary">/m</span>
                  </h3>
                </div>
                <p class="c-paragraph c-price__text">
                  You can cancel or change your plan at any time.
                </p>
                <h5 class="heading heading--4 c-price__title">
                  What's included
                </h5>
              </div>
              <ul class="c-list">
                <li class="c-list__item c-price__list-item">
                  <div class="c-list__icon c-list__icon--check"></div>
                  <div class="c-list__text">Unlimited Users</div>
                </li>
                <li class="c-list__item c-price__list-item">
                  <div class="c-list__icon c-list__icon--check"></div>
                  <div class="c-list__text">Unlimited Loans</div>
                </li>
                <li class="c-list__item c-price__list-item">
                  <div class="c-list__icon c-list__icon--check"></div>
                  <div class="c-list__text">All other features Included</div>
                </li>
              </ul>
             
            </article>
          </div>
        </div>
      </section>

      <section class="section banner">
        <div class="container">
          <div class="c-banner">
            <h2 class="heading heading--2 c-banner__title">
              Request a Meeting
            </h2>
            <a href="#" class="c-button c-button--primary">Request now</a>
          </div>
        </div>
      </section>
    </main>

    <footer class="footer">
      <div class="container">
        <div class="c-footer">
          <div class="c-footer__box">
            <a class="c-link" href="#">
              <div class="c-logo">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" class="c-logo__img" style="border-radius:100%" />
                <!-- <span class="c-logo__text c-logo__text--white">Login</span> -->
              </div>
            </a>
            <p class="c-paragraph c-footer__text">
              © <?php echo date('Y'); ?> all rights reserved
            </p>
          </div>
          <div class="c-footer__box">
            <div class="c-footer__icons">
              <a href="#">
                <img class="c-footer__icon" src="{{ asset('landingPage/img/instagram.svg') }}" alt="Instagram" />
              </a>
              <a href="#">
                <img class="c-footer__icon" src="{{ asset('landingPage/img/twitter.svg') }}" alt="Twitter" />
              </a>
              <a href="#">
                <img class="c-footer__icon" src="{{ asset('landingPage/img/facebook.svg') }}" alt="Facebook" />
              </a>
            </div>
            <ul class="c-list c-list--flex c-list--align-right">
              <li class="c-list__item c-list__item--small">
                <a href="#" class="c-link c-link--list c-link--list-right">Contact</a>
              </li>
              <li class="c-list__item c-list__item--small">
                <a href="#" class="c-link c-link--list c-link--list-right">About us</a>
              </li>
              <li class="c-list__item c-list__item--small">
                <a href="#" class="c-link c-link--list c-link--list-right">FAQ</a>
              </li>
              <li class="c-list__item c-list__item--small">
                <a href="#" class="c-link c-link--list c-link--list-right">Support</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
  </body>
</html>