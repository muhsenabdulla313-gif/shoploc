@extends('layout.master')

@section('body')

<style>
/* Page Layout */
.contact-wrapper {
  background-color: #f9f9f9;
  min-height: 100vh;
  padding: 60px 0;
  font-family: 'Poppins', sans-serif;
}

.contact-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Header Section */
.contact-header {
  text-align: center;
  margin-bottom: 60px;
}

.contact-header h1 {
  font-size: 3rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 15px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.contact-header p {
  color: #666;
  font-size: 1.1rem;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}

/* Content Grid */
.contact-content {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 40px;
  background: white;
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.05);
  overflow: hidden;
}

/* Left Side - Info */
.contact-info {
  /* BLUE THEME */
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
  padding: 60px 40px;
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.info-section h2 {
  font-size: 2rem;
  font-weight: 600;
  margin-bottom: 20px;
}

.info-section p {
  color: rgba(255,255,255,0.9);
  line-height: 1.6;
  margin-bottom: 40px;
}

.detail-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 30px;
  padding: 15px;
  background: rgba(255,255,255,0.1);
  border-radius: 12px;
  backdrop-filter: blur(5px);
  transition: transform 0.3s ease;
}

.detail-item:hover {
  transform: translateX(10px);
  background: rgba(255,255,255,0.15);
}

.detail-icon {
  font-size: 24px;
  margin-right: 20px;
  background: white;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  /* BLUE ICON COLOR */
  color: #1d4ed8;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.detail-content h3 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 5px;
}

.detail-content p {
  color: rgba(255,255,255,0.9);
  font-size: 0.95rem;
  margin: 0;
  line-height: 1.5;
}

/* Right Side - Form */
.contact-form-section {
  padding: 60px;
}

.contact-form-section h2 {
  font-size: 2rem;
  color: #333;
  margin-bottom: 30px;
  font-weight: 600;
}

.form-group {
  margin-bottom: 25px;
}

.form-group label {
  display: block;
  font-weight: 500;
  color: #555;
  margin-bottom: 10px;
  font-size: 0.95rem;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 15px 20px;
  border: 2px solid #eee;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  font-family: inherit;
  background: #fcfcfc;
}

.form-group input:focus,
.form-group textarea:focus {
  /* BLUE FOCUS */
  border-color: #1d4ed8;
  background: white;
  outline: none;
  box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.12);
}

.submit-btn {
  /* BLUE BUTTON */
  background: #1d4ed8;
  color: white;
  padding: 16px 40px;
  border: none;
  border-radius: 50px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
  width: 100%;
}

.submit-btn:hover {
  background: #000;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

/* Messages */
.success-message {
  background: #d4edda;
  color: #155724;
  padding: 15px;
  border-radius: 10px;
  margin-bottom: 20px;
  border-left: 5px solid #28a745;
}

.error-message {
  background: #f8d7da;
  color: #721c24;
  padding: 15px;
  border-radius: 10px;
  margin-bottom: 20px;
  border-left: 5px solid #dc3545;
}

/* Responsive */
@media (max-width: 992px) {
  .contact-content {
    grid-template-columns: 1fr;
  }

  .contact-info {
    padding: 40px;
  }

  .contact-form-section {
    padding: 40px;
  }
}

@media (max-width: 768px) {
  .contact-wrapper {
    padding: 30px 0;
  }

  .contact-header h1 {
    font-size: 2.2rem;
  }

  .detail-item {
    flex-direction: column;
    text-align: center;
    align-items: center;
  }

  .detail-icon {
    margin-right: 0;
    margin-bottom: 15px;
  }

  .detail-item:hover {
    transform: translateY(-5px);
  }

  .submit-btn {
    padding: 14px 30px;
  }
}
</style>

<div class="contact-wrapper">
  <div class="contact-container">
    <div class="contact-header">
      <h1>Contact Us</h1>
      <p>We'd love to hear from you! Reach out to us with any questions or feedback.</p>
    </div>

    <div class="contact-content">
      {{-- LEFT SIDE --}}
      <div class="contact-info">
        <div class="info-section">
          <h2>Get In Touch</h2>
          <p>Have questions about our products or services? Our team is here to help! We strive to provide the best customer experience possible.</p>
        </div>

        <div class="contact-details">
          <div class="detail-item">
            <div class="detail-icon">
              <i class="fa fa-map-marker-alt"></i>
            </div>
            <div class="detail-content">
              <h3>Our Location</h3>
              <p>
                Deli Kalanad(PO)<br>
                Kasaragod-671317, Kerala
              </p>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon">
              <i class="fa fa-phone"></i>
            </div>
            <div class="detail-content">
              <h3>Phone Number</h3>
              <p>
                <a href="tel:+918848748469" style="color: white; text-decoration: none;">+91 8848 748 469</a>
              </p>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon">
              <i class="fa fa-envelope"></i>
            </div>
            <div class="detail-content">
              <h3>Email Address</h3>
              <p>
                <a href="mailto:razicdeli@gmail.com" style="color: white; text-decoration: none;">razicdeli@gmail.com</a>
              </p>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT SIDE --}}
      <div class="contact-form-section">
        <h2>Send Us a Message</h2>

        {{-- Success / Error --}}
        @if(session('success'))
          <div class="success-message">
            <p><i class="fa fa-check-circle"></i> {{ session('success') }}</p>
          </div>
        @endif

        @if($errors->any())
          <div class="error-message">
            <p><i class="fa fa-exclamation-circle"></i> {{ $errors->first() }}</p>
          </div>
        @endif

        <form class="contact-form" method="POST" action="{{ route('contact.submit') }}" id="contactForm">
          @csrf

          <div class="form-group">
            <label for="name">Full Name</label>
            <input
              type="text"
              id="name"
              name="name"
              value="{{ old('name') }}"
              required
              placeholder="Enter your full name"
            />
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input
              type="email"
              id="email"
              name="email"
              value="{{ old('email') }}"
              required
              placeholder="Enter your email address"
            />
          </div>

          <div class="form-group">
            <label for="subject">Subject</label>
            <input
              type="text"
              id="subject"
              name="subject"
              value="{{ old('subject') }}"
              required
              placeholder="What is this regarding?"
            />
          </div>

          <div class="form-group">
            <label for="message">Message</label>
            <textarea
              id="message"
              name="message"
              required
              placeholder="Type your message here..."
              rows="5"
            >{{ old('message') }}</textarea>
          </div>

          <button type="submit" class="submit-btn" id="submitBtn">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</div>

@include('footer')
@endsection
