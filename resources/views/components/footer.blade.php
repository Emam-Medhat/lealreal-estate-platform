<footer class="footer bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-lg-4 mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-building me-2"></i>Real Estate Pro
                </h5>
                <p class="text-light">
                    Your trusted partner in finding the perfect property. We offer comprehensive real estate solutions for buyers, sellers, and agents.
                </p>
                <div class="social-links mt-3">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-light text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="{{ route('properties.index') }}" class="text-light text-decoration-none">Properties</a></li>
                    <li class="mb-2"><a href="{{ route('agents.directory') }}" class="text-light text-decoration-none">Agents</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-light text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}" class="text-light text-decoration-none">Contact</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="mb-3">Services</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Property Management</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Investment Advisory</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Market Analysis</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Legal Services</a></li>
                    <li class="mb-2"><a href="#" class="text-light text-decoration-none">Mortgage Assistance</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 mb-4">
                <h6 class="mb-3">Contact Info</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        123 Business Ave, City, State 12345
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        +1 (555) 123-4567
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        info@realestatepro.com
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="row">
            <div class="col-md-6">
                <p class="mb-0 text-light">
                    &copy; {{ date('Y') }} Real Estate Pro. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-light text-decoration-none me-3">Privacy Policy</a>
                <a href="#" class="text-light text-decoration-none me-3">Terms of Service</a>
                <a href="#" class="text-light text-decoration-none">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
