# Module 2: نظام المستخدمين (Users Management)

## الوصف
إدارة حسابات وملفات المستخدمين مع نظام متكامل يشمل:
- إدارة الملفات الشخصية
- التحقق من الهوية (KYC)
- المحافظة الرقمية
- تتبع النشاط
- الإشعارات
- الإعدادات والتفضيلات

## 🛡️ Middlewares
- **CheckKycVerification.php** - التحقق من اكتمال عملية KYC
- **CheckProfileComplete.php** - التحقق من اكتمال الملف الشخصي
- **TrackUserActivity.php** - تتبع نشاط المستخدم

## 📜 Policies
- **UserPolicy.php** - صلاحيات الوصول إلى بيانات المستخدم
  - `can('viewProfile', $user)` - عرض الملف الشخصي
  - `can('updateProfile', $user)` - تحديث الملف الشخصي
  - `can('manageWallet', $user)` - إدارة المحفظة
  - `can('viewActivityLog', $user)` - عرض سجل النشاط
  - `can('verifyKyc', $user)` - التحقق من الهوية
  - `can('uploadDocuments', $user)` - رفع المستندات
  - `can('viewSensitiveInfo', $user)` - عرض المعلومات الحساسة

## 📡 Events
- **ProfileUpdated.php** - تحديث الملف الشخصي
- **KycVerificationSubmitted.php** - تقديم طلب التحقق
- **KycVerificationApproved.php** - قبول التحقق
- **KycVerificationRejected.php** - رفض التحقق
- **PreferencesUpdated.php** - تحديث التفضيلات
- **AvatarChanged.php** - تغيير الصورة الشخصية

## 👂 Listeners
- **UpdateUserCache.php** - تحديث كاش المستخدم
- **NotifyProfileCompletion.php** - إشعار اكتمال الملف
- **SendKycVerificationEmail.php** - إرسال إيميل التحقق
- **UpdateSearchPreferences.php** - تحديث تفضيلات البحث

## 🔧 Services
- **UserService.php** - الخدمات الأساسية للمستخدم
  - `updateProfile($userId, $data)` - تحديث الملف الشخصي
  - `uploadAvatar($file, $userId)` - رفع الصورة الشخصية
  - `deleteUser($userId, $hardDelete)` - حذف المستخدم
  - `exportUserData($userId)` - تصدير بيانات المستخدم

- **ProfileService.php** - خدمة الملف الشخصي
  - `calculateCompletionPercentage($user)` - حساب نسبة الاكتمال
  - `suggestProfileImprovements($user)` - اقتراح تحسينات
  - `getProfileStrength($user)` - قوة الملف الشخصي
  - `getCompletionBreakdown($user)` - تفصيل الاكتمال

- **KycService.php** - خدمة التحقق من الهوية
  - `submitVerification($userId, $data)` - تقديم طلب التحقق
  - `verifyDocuments($kycId, $data)` - التحقق من المستندات
  - `approveVerification($kycId, $approvedBy)` - قبول التحقق
  - `rejectVerification($kycId, $rejectedBy, $reason)` - رفض التحقق
  - `getKycRequirements($level)` - متطلبات التحقق

- **WalletService.php** - خدمة المحفظة الرقمية
  - `getBalance($userId)` - الرصيد الحالي
  - `addFunds($userId, $amount, $type, $meta)` - إضافة أموال
  - `deductFunds($userId, $amount, $type, $meta)` - خصم الأموال
  - `freezeFunds($userId, $amount, $reason, $meta)` - تجميد الأموال
  - `unfreezeFunds($userId, $amount, $reason, $meta)` - إلغاء التجميد
  - `getTransactionHistory($userId, $filters)` - سجل المعاملات
  - `getWalletStatistics($userId, $period)` - إحصائيات المحفظة

## 📬 Notifications
- **ProfileCompletedNotification.php** - إشعار اكتمال الملف
- **KycApprovedNotification.php** - إشعار قبول التحقق
- **KycRejectedNotification.php** - إشعار رفض التحقق
- **WeeklyDigestNotification.php** - الملخص الأسبوعي

## 👁️ Observers
- **UserProfileObserver.php** - مراقب نموذج المستخدم
  - `updated()` - تحديث نسبة الاكتمال وتسجيل التغييرات
  - `creating()` - تعيين القيم الافتراضية
  - `created()` - إنشاء الملف والمحفظة

## 🚀 Jobs
- **ProcessKycDocuments.php** - معالجة مستندات KYC
- **GenerateUserReport.php** - إنشاء تقارير المستخدم
- **CleanInactiveUsers.php** - تنظيف المستخدمين غير النشطين
- **SendWeeklyActivityDigest.php** - إرسال الملخص الأسبوعي

## 🛣️ Routes
- **routes/user.php** - مسارات نظام المستخدمين
  - مسارات الملف الشخصي
  - مسارات التحقق من الهوية
  - مسارات المحفظة الرقمية
  - مسارات الإعدادات
  - مسارات النشاط والتقارير
  - مسارات API للمطورين

## 🔧 Configuration
- **config/user-services.php** - إعدادات خدمات المستخدمين
- **app/Providers/UserServiceProvider.php** - مزود خدمة المستخدمين
- **app/Providers/UserEventServiceProvider.php** - مزود خدمة الأحداث

## ✨ المميزات
- نظام متكامل لإدارة المستخدمين
- دعم كامل للغة العربية
- تتبع نشاط المستخدم بالتفصيل
- نظام KYC متعدد المستويات
- محفظة رقمية مع دعم متعدد العملات
- إشعارات في الوقت الفعلي
- تقارير متقدمة
- حماية وأمان متقدم
- واجهة API متكاملة
