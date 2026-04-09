

<style>
    /* ─────────────── FOOTER STYLES ─────────────── */
    #site-footer {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        position: relative;
        padding-top: 80px;
        padding-bottom: 40px;
        color: #cbd5e1;
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', 'Cairo', sans-serif;
    }

    #site-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(37, 99, 235, 0.4) 50%, transparent);
    }

    /* Shimmer Effect */
    .footer-shimmer {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.03) 0%, transparent 70%);
        pointer-events: none;
        animation: footer-shimmer-spin 20s linear infinite;
    }

    @keyframes footer-shimmer-spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Columns */
    .footer-col-title {
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 25px;
        position: relative;
        display: inline-block;
    }

    .footer-col-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        right: 0;
        width: 30px;
        height: 2px;
        background: #3b82f6;
        border-radius: 2px;
    }

    /* Links */
    .footer-link {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #94a3b8;
        text-decoration: none;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .footer-link:hover {
        color: #ffffff;
        transform: translateX(-5px);
    }

    .footer-link i {
        font-size: 0.8rem;
        color: #3b82f6;
        opacity: 0.7;
    }

    /* Branding */
    .footer-brand {
        margin-bottom: 20px;
    }

    .footer-brand .logo-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .footer-brand i {
        font-size: 2rem;
        color: #3b82f6;
    }

    .footer-brand h2 {
        font-size: 1.6rem;
        font-weight: 900;
        color: #fff;
        margin: 0;
        letter-spacing: -0.5px;
    }

    /* Social Icons */
    .footer-socials {
        display: flex;
        gap: 12px;
        margin-top: 25px;
    }

    .social-btn {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .social-btn:hover {
        background: #3b82f6;
        border-color: #3b82f6;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
    }

    /* Newsletter */
    .footer-news-wrap {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 40px;
    }

    .news-input-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .news-input {
        flex: 1;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 12px 20px;
        color: #fff;
        outline: none;
        transition: border-color 0.3s;
    }

    .news-input:focus {
        border-color: #3b82f6;
    }

    .news-btn {
        padding: 12px 25px;
        background: #3b82f6;
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .news-btn:hover {
        background: #2563eb;
        transform: scale(1.05);
    }

    /* Bottom Bar */
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        padding-top: 30px;
        display: flex;
        flex-direction: column;
        md-flex-direction: row;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .copyright {
        font-size: 0.85rem;
        color: #64748b;
    }

    .bottom-links {
        display: flex;
        gap: 20px;
    }

    .bottom-links a {
        color: #64748b;
        text-decoration: none;
        font-size: 0.85rem;
        transition: color 0.3s;
    }

    .bottom-links a:hover {
        color: #fff;
    }
</style>

<footer id="site-footer">
    <div class="footer-shimmer"></div>

    <div class="container relative z-10">
        <div class="row gy-5">
            <!-- Brand Column -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <a href="<?php echo e(route('home')); ?>" class="logo-wrap">
                        <i class="fas fa-building-circle-check"></i>
                        <h2>عقاري Pro</h2>
                    </a>
                </div>
                <p class="leading-relaxed mb-4">
                    نحن نعيد تعريف تجربة البحث عن العقارات في المملكة. منصة ذكية تجمع بين التكنولوجيا المتطورة والخبرة
                    العقارية العميقة لنقدم لك المسكن الذي تحلم به بضغطة زر.
                </p>
                <div class="footer-socials">
                    <a href="#" class="social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn" title="Twitter"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" class="social-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-btn" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <!-- Quick Access -->
            <div class="col-lg-2 col-md-4 col-6">
                <h6 class="footer-col-title">استكشاف</h6>
                <div class="footer-links">
                    <a href="<?php echo e(route('home')); ?>" class="footer-link"><i class="fas fa-chevron-left"></i> الرئيسية</a>
                    <a href="<?php echo e(route('properties.index')); ?>" class="footer-link"><i class="fas fa-chevron-left"></i>
                        العقارات</a>
                    <a href="<?php echo e(route('agents.directory')); ?>" class="footer-link"><i class="fas fa-chevron-left"></i>
                        الوكلاء</a>
                    <a href="<?php echo e(route('projects.index')); ?>" class="footer-link"><i class="fas fa-chevron-left"></i>
                        المشاريع</a>
                </div>
            </div>

            <!-- More Links -->
            <div class="col-lg-2 col-md-4 col-6">
                <h6 class="footer-col-title">عن المنصة</h6>
                <div class="footer-links">
                    <a href="<?php echo e(route('about')); ?>" class="footer-link"><i class="fas fa-chevron-left"></i> من نحن</a>
                    <a href="<?php echo e(route('contact')); ?>" class="footer-link"><i class="fas fa-chevron-left"></i> اتصل
                        بنا</a>
                    <a href="<?php echo e(route('news.index')); ?>" class="footer-link"><i class="fas fa-chevron-left"></i>
                        الأخبار</a>
                    <a href="<?php echo e(route('ai.chat')); ?>" class="footer-link"><i class="fas fa-chevron-left"></i> المساعد
                        الذكي</a>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="col-lg-4 col-md-8">
                <div class="footer-news-wrap">
                    <h6 class="footer-col-title !mb-2">انضم لنشرتنا البريدية</h6>
                    <p class="text-sm">كن أول من يعلم عن أحدث الفرص الاستثمارية.</p>
                    <form class="news-input-group">
                        <input type="email" class="news-input" placeholder="البريد الإلكتروني..." required>
                        <button type="submit" class="news-btn">اشترك</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <p class="copyright">
                &copy; <?php echo e(date('Y')); ?> <strong>عقاري Pro</strong>. صنع بشغف لخدمتكم.
            </p>
            <div class="bottom-links">
                <a href="#">سياسة الخصوصية</a>
                <a href="#">شروط الاستخدام</a>
                <a href="#">سياسة الكوكيز</a>
            </div>
        </div>
    </div>
</footer><?php /**PATH D:\larvel state big\resources\views/components/footer.blade.php ENDPATH**/ ?>