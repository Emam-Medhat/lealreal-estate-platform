# Module 5: نظام الوكلاء (Agents Management)

## الوصف
إدارة الوكلاء العقاريين وعملائهم مع نظام متكامل يشمل:
- تسجيل وإدارة الوكلاء
- تتبع الأداء والمؤشرات
- نظام العمولات والمكافآت
- إدارة المواعيد والعملاء
- التقارير الشاملة والمؤشرات

## 🛡️ Middlewares (2)
- **CheckAgentLicense.php** - التحقق من رخصة الوكيل
- **CheckAgentVerification.php** - التحقق من التحقق كوكلاء

## 📜 Policies (1)
- **AgentPolicy.php** - صلاحيات الوصول إلى بيانات الوكيل
  - `can('viewLeads', $agent)` - عرض العملاء
  - `can('manageClients', $agent)` - إدارة العملاء
  - `can('manageCommissions', $agent)` - إدارة العمولات
  - `can('viewPerformance', $agent)` - عرض مؤشرات الأداء
  - `can('manageAppointments', $agent)` - إدارة المواعيد

## 📡 Events (6)
- **AgentRegistered.php** - تسجيل وكلاء جديد
- **LeadAssignedToAgent.php** - تخصيص عميل للوكلاء
- **LeadConvertedToClient.php** - تحويل عميل إلى عميل
- **AppointmentScheduledWithAgent.php** - تحديد موعد مع الوكيل
- **CommissionEarned.php** - كسب عمولة
- **AgentReviewReceived.php** - استلام تقييم الأداء

## 👂 Listeners (6)
- **SendAgentWelcomeEmail.php** - إرسال إيميل ترحيبي
- **NotifyAgentNewLead.php** - إشعار بعميل جديد
- **SendAppointmentReminder.php** - إرسال تذكير موعد
- **CalculateCommission.php** - حساب العمولة
- **UpdateAgentPerformance.php** - تحديث مؤشرات الأداء
- **AgentReviewReceivedNotification.php** - إشعار استلام تقييم

## 🔧 Services (3)
- **AgentService.php** - الخدمات الأساسية للوكلاء
  - `registerAgent($data)` - تسجيل وكلاء
  - `assignLead($agentId, $leadId)` - تخصيص عميل
  - `convertLeadToClient($agentId, $leadId, $salePrice)` - تحويل عميل
  - `scheduleAppointment($agentId, $leadId, $dateTime, $location, $note)` - تحديد موعد

- **AgentCommissionService.php** - خدمة العمولات
  - `calculateCommission($agentId, $saleAmount, $type)` - حساب العمولة
  - `payCommission($agentId, $commissionId, $paymentMethod)` - دفع العمولة
  - `getCommissionHistory($agentId, $filters)` - سجل العمولات
  - `getCommissionSummary($agentId, $period)` - ملخص العمولات

- **AgentPerformanceService.php** - خدمة مؤشرات الأداء
  - `getMetrics($agentId, $period)` - مؤشرات الأداء
  - `getRanking($agentId, $period)` - ترتيب الوكلاء
  - `getMonthlyPerformance($agentId, $month, $year)` - مؤشرات شهرية

## ⚙️ Jobs (4)
- **CalculateAgentCommissions.php** - حساب العمولات الشهرية
- **SendAppointmentReminders.php** - إرسال تذكيرات المواعيد
- **UpdateAgentPerformanceMetrics.php** - تحديث مؤشرات الأداء
- **GenerateAgentMonthlyReport.php** - إنشاء تقارير شهرية

## 📬 Notifications (4)
- **AgentRegisteredNotification.php** - إشعار تسجيل الوكيل
- **NewLeadAssignedNotification.php** - إشعار تخصيص عميل
- **AppointmentReminderNotification.php** - إشعار تذكير موعد
- **CommissionPaidNotification.php** - إشعار دفع العمولة
- **AgentReviewReceivedNotification.php** - إشعار استلام تقييم

## 👁️ Observers (1)
- **AgentObserver.php** - مراقب نموذج الوكيل
  - `created()` - إنشاء مؤشرات أولية
  - `updated()` - تحديث مؤشرات الأداء
  - `deleted()` - أرشفة البيانات وإلغاء العمولات

## 🛣️ Routes
- **routes/agents.php** - مسارات متكاملة لنظام الوكلاء
  - مسارات لوحة تحكم الوكيل
  - مسارات إدارة الوكلاء
  - مسارات العملاء والمواعيد
  - مسارات العمولات والمؤشرات
  - مسارات التقارير
  - مسارات الإعدادات
  - مسارات API للمطورين

## ✨ المميزات
- نظام متكامل لإدارة الوكلاء العقاريين
- دعم كامل للغة العربية
- نظام رخصص متقدم
- نظام مؤشرات أداء تفصيلية
- نظام عمولات آلي مع حسابات متقدمة
- نظام تقارير شاملة مع تصدير متعدد الصيغ
- نظام مواعيد متقدم مع تذكيرات آلية
- واجهة API متكاملة
- إشعارات في الوقت الفعلي
