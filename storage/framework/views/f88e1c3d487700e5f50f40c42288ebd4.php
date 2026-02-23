<style>
    .bg-gradient-dark {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
}

.footer::before {
    content: '';
    position: absolute;
    inset: 0 0 auto 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, #3b82f6 50%, transparent);
    opacity: 0.35;
}

.footer-logo {
    transition: transform 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-4px);
    color: #60a5fa !important;
}

.hover-link {
    position: relative;
    transition: all 0.28s ease;
}

.hover-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 1.5px;
    bottom: -4px;
    left: 0;
    background-color: #60a5fa;
    transition: width 0.3s ease;
}

.hover-link:hover::after {
    width: 100%;
}

.footer-links a,
.footer-contact span {
    transition: padding-left 0.3s ease, color 0.3s ease;
}

.footer-links a:hover {
    padding-left: 8px;
    color: #93c5fd !important;
}

input::placeholder {
    color: rgba(255,255,255,0.5) !important;
}
</style>

<footer class="footer bg-gradient-dark text-white pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row gy-5">

            <!-- Company Brand & Description -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-4">
                    <div class="footer-logo me-3">
                        <i class="fas fa-building fs-3 text-primary"></i>
                    </div>
                    <h4 class="mb-0 fw-bold" style="letter-spacing: -0.5px;">Real Estate Pro</h4>
                </div>
                
                <p class="text-white-75 mb-4 lead-sm">
                    شريكك الموثوق في العثور على العقار المثالي. نقدم حلول عقارية شاملة للمشترين والبائعين والوكلاء بأعلى معايير الجودة والاحترافية.
                </p>

                <div class="social-icons d-flex gap-3">
                    <a href="#" class="text-white-75 hover-lift"><i class="fab fa-facebook-f fs-5"></i></a>
                    <a href="#" class="text-white-75 hover-lift"><i class="fab fa-twitter fs-5"></i></a>
                    <a href="#" class="text-white-75 hover-lift"><i class="fab fa-instagram fs-5"></i></a>
                    <a href="#" class="text-white-75 hover-lift"><i class="fab fa-linkedin-in fs-5"></i></a>
                    <a href="#" class="text-white-75 hover-lift"><i class="fab fa-whatsapp fs-5"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-heading mb-4 text-uppercase fw-bold">روابط سريعة</h6>
                <ul class="list-unstyled footer-links">
                    <li class="mb-2"><a href="<?php echo e(route('home')); ?>" class="text-white-75 text-decoration-none hover-link">الرئيسية</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('properties.index')); ?>" class="text-white-75 text-decoration-none hover-link">العقارات</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('agents.directory')); ?>" class="text-white-75 text-decoration-none hover-link">الوكلاء</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('about')); ?>" class="text-white-75 text-decoration-none hover-link">من نحن</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('contact')); ?>" class="text-white-75 text-decoration-none hover-link">اتصل بنا</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading mb-4 text-uppercase fw-bold">خدماتنا</h6>
                <ul class="list-unstyled footer-links">
                    <li class="mb-2"><a href="#" class="text-white-75 text-decoration-none hover-link">إدارة العقارات</a></li>
                    <li class="mb-2"><a href="#" class="text-white-75 text-decoration-none hover-link">الاستشارات الاستثمارية</a></li>
                    <li class="mb-2"><a href="#" class="text-white-75 text-decoration-none hover-link">تحليل السوق</a></li>
                    <li class="mb-2"><a href="#" class="text-white-75 text-decoration-none hover-link">الخدمات القانونية</a></li>
                    <li class="mb-2"><a href="#" class="text-white-75 text-decoration-none hover-link">مساعدة التمويل العقاري</a></li>
                </ul>
            </div>

            <!-- Contact & Newsletter -->
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-heading mb-4 text-uppercase fw-bold">معلومات التواصل</h6>
                <ul class="list-unstyled footer-contact">
                    <li class="mb-3">
                        <i class="fas fa-map-marker-alt me-3 text-primary"></i>
                        ١٢٣ شارع الأعمال، المدينة، الدولة ١٢٣٤٥
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-phone-alt me-3 text-primary"></i>
                        +٩٦٦ ١٢ ٣٤٥ ٦٧٨٩
                    </li>
                    <li class="mb-4">
                        <i class="fas fa-envelope me-3 text-primary"></i>
                        info@realestatepro.sa
                    </li>
                </ul>

                <!-- Simple Newsletter (اختياري - يمكن إزالته) -->
                <form class="footer-newsletter">
                    <div class="input-group input-group-sm">
                        <input type="email" class="form-control bg-dark border-secondary text-white" placeholder="بريدك الإلكتروني" aria-label="Newsletter">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i></button>
                    </div>
                    <small class="text-white-50 mt-2 d-block">اشترك للحصول على آخر العروض والنصائح العقارية</small>
                </form>
            </div>

        </div>

        <!-- Bottom Bar -->
        <div class="border-top border-secondary mt-5 pt-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <small class="text-white-75">
                        © <?php echo e(date('Y')); ?> Real Estate Pro – جميع الحقوق محفوظة
                    </small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small>
                        <a href="#" class="text-white-75 me-3 hover-link text-decoration-none">سياسة الخصوصية</a>
                        <a href="#" class="text-white-75 me-3 hover-link text-decoration-none">شروط الخدمة</a>
                        <a href="#" class="text-white-75 hover-link text-decoration-none">سياسة الكوكيز</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</footer><?php /**PATH F:\larvel state big\resources\views/components/footer.blade.php ENDPATH**/ ?>