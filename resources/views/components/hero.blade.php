<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-white mb-4">
                    Find Your Dream Property
                </h1>
                <p class="lead text-white-50 mb-4">
                    Discover the perfect home, apartment, or commercial property from our extensive collection. 
                    Expert agents, competitive prices, and seamless transactions.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('properties.index') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-search me-2"></i>Browse Properties
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-phone me-2"></i>Contact Agent
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <img src="https://via.placeholder.com/600x400/667eea/ffffff?text=Premium+Properties" 
                         alt="Properties" 
                         class="img-fluid rounded-3 shadow-lg">
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge bg-danger fs-6">Hot Deals</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
