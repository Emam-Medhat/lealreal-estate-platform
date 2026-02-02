<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert sample digital signatures
        DB::table('digital_signatures')->insert([
            [
                'document_title' => 'عقد بيع عقاري',
                'signer_name' => 'أحمد محمد',
                'status' => 'active',
                'verified' => 'متحقق',
                'icon' => 'check',
                'color' => 'green',
                'type' => 'توقيع رقمي متقدم',
                'validity' => 'سنتان',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2)
            ],
            [
                'document_title' => 'اتفاقية شراكة',
                'signer_name' => 'شركة النخبة',
                'status' => 'active',
                'verified' => 'متحقق',
                'icon' => 'check',
                'color' => 'green',
                'type' => 'توقيع مؤسسي',
                'validity' => '3 سنوات',
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5)
            ],
            [
                'document_title' => 'وكالة رسمية',
                'signer_name' => 'فاطمة علي',
                'status' => 'active',
                'verified' => 'متحقق',
                'icon' => 'check',
                'color' => 'green',
                'type' => 'توقيع بسيط',
                'validity' => 'سنة واحدة',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay()
            ],
            [
                'document_title' => 'إقرار دين',
                'signer_name' => 'محمد خالد',
                'status' => 'active',
                'verified' => 'متحقق',
                'icon' => 'check',
                'color' => 'green',
                'type' => 'توقيع حكومي',
                'validity' => '5 سنوات',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2)
            ]
        ]);

        // Insert sample certificates
        DB::table('certificates')->insert([
            [
                'certificate_id' => 'CERT-' . rand(100000, 999999),
                'type' => 'شهادة توقيع رقمي',
                'status' => 'issued',
                'validity' => 'سنتان',
                'issuer' => 'هيئة الحكومة الرقمية',
                'recipient' => 'أحمد محمد',
                'issued_at' => now()->subDays(30),
                'expires_at' => now()->addYears(2)
            ],
            [
                'certificate_id' => 'CERT-' . rand(100000, 999999),
                'type' => 'شهادة توثيق',
                'status' => 'issued',
                'validity' => '3 سنوات',
                'issuer' => 'وزارة العدل',
                'recipient' => 'شركة النخبة',
                'issued_at' => now()->subDays(15),
                'expires_at' => now()->addYears(3)
            ]
        ]);

        // Insert sample security logs
        DB::table('security_logs')->insert([
            [
                'type' => 'signature_security',
                'status' => 'آمن',
                'description' => 'فحص أمان التوقيعات الرقمية',
                'severity' => 'low',
                'created_at' => now()->subHour()
            ],
            [
                'type' => 'certificate_security',
                'status' => 'آمن',
                'description' => 'فحص أمان الشهادات',
                'severity' => 'low',
                'created_at' => now()->subHours(2)
            ]
        ]);

        // Insert sample compliance data
        DB::table('compliance_checks')->insert([
            ['status' => 'passed', 'check_type' => 'regulatory', 'description' => 'فحص الامتثال التنظيمي'],
            ['status' => 'passed', 'check_type' => 'internal', 'description' => 'فحص الامتثال الداخلي'],
            ['status' => 'failed', 'check_type' => 'security', 'description' => 'فحص أمان البيانات'],
            ['status' => 'passed', 'check_type' => 'financial', 'description' => 'فحص الامتثال المالي']
        ]);

        DB::table('risk_assessments')->insert([
            ['status' => 'identified', 'risk_level' => 'low', 'title' => 'مخاطر بيانات'],
            ['status' => 'identified', 'risk_level' => 'medium', 'title' => 'مخاطر تشغيلية'],
            ['status' => 'mitigated', 'risk_level' => 'high', 'title' => 'مخاطر قانونية']
        ]);

        DB::table('compliance_documents')->insert([
            ['status' => 'completed', 'document_type' => 'policy', 'title' => 'سياسة الخصوصية'],
            ['status' => 'completed', 'document_type' => 'procedure', 'title' => 'إجراءات التشغيل'],
            ['status' => 'in_progress', 'document_type' => 'manual', 'title' => 'دليل الموظفين']
        ]);

        DB::table('compliance_reviews')->insert([
            ['status' => 'completed', 'review_type' => 'quarterly', 'title' => 'مراجعة ربع سنوية'],
            ['status' => 'pending', 'review_type' => 'annual', 'title' => 'مراجعة سنوية'],
            ['status' => 'completed', 'review_type' => 'monthly', 'title' => 'مراجعة شهرية']
        ]);

        DB::table('regulatory_compliance')->insert([
            ['name' => 'التراخيص التجارية', 'status' => 'مكتمل', 'percentage' => 100, 'icon' => 'check', 'color' => 'green'],
            ['name' => 'السجلات التجارية', 'status' => 'مكتمل', 'percentage' => 100, 'icon' => 'check', 'color' => 'green'],
            ['name' => 'الضرائب الفيدرالية', 'status' => 'مكتمل', 'percentage' => 100, 'icon' => 'check', 'color' => 'green'],
            ['name' => 'حماية البيانات', 'status' => 'يحتاج تحسين', 'percentage' => 85, 'icon' => 'exclamation', 'color' => 'yellow']
        ]);

        DB::table('internal_compliance')->insert([
            ['name' => 'سياسة الخصوصية', 'status' => 'مكتملة', 'icon' => 'check', 'color' => 'green'],
            ['name' => 'مدونة السلوك', 'status' => 'محدثة', 'icon' => 'check', 'color' => 'green'],
            ['name' => 'تدريب الموظفين', 'status' => '75% مكتمل', 'icon' => 'sync', 'color' => 'yellow'],
            ['name' => 'إدارة المخاطر', 'status' => 'نشطة', 'icon' => 'check', 'color' => 'green']
        ]);

        DB::table('compliance_activities')->insert([
            ['title' => 'اكتمال مراجعة التراخيص التجارية', 'time' => 'قبل 2 ساعة', 'status' => 'مكتمل', 'icon' => 'check', 'color' => 'green'],
            ['title' => 'تحديث سياسة الخصوصية', 'time' => 'قبل 5 ساعات', 'status' => 'مكتمل', 'icon' => 'check', 'color' => 'green'],
            ['title' => 'فحص الامتثال الضريبي', 'time' => 'قبل يوم', 'status' => 'قيد المعالجة', 'icon' => 'sync', 'color' => 'yellow'],
            ['title' => 'تدريب الموظفين على الامتثال', 'time' => 'قبل يومين', 'status' => 'مكتمل', 'icon' => 'check', 'color' => 'green']
        ]);

        // Insert sample notary data
        DB::table('notary_documents')->insert([
            ['status' => 'signed', 'document_type' => 'عقد', 'title' => 'عقد بيع عقاري', 'client_name' => 'أحمد محمد', 'completed_at' => now()->subHours(6), 'created_at' => now()->subHours(12)],
            ['status' => 'signed', 'document_type' => 'وكالة', 'title' => 'وكالة رسمية', 'client_name' => 'فاطمة علي', 'completed_at' => now()->subHours(12), 'created_at' => now()->subHours(18)],
            ['status' => 'in_progress', 'document_type' => 'إقرار', 'title' => 'إقرار دين', 'client_name' => 'محمد خالد', 'completed_at' => null, 'created_at' => now()->subHours(3)],
            ['status' => 'signed', 'document_type' => 'شهادة', 'title' => 'شهادة ميلاد', 'client_name' => 'سارة أحمد', 'completed_at' => now()->subDay(), 'created_at' => now()->subDays(2)]
        ]);

        DB::table('notary_clients')->insert([
            ['name' => 'أحمد محمد', 'status' => 'active', 'email' => 'ahmed@example.com', 'phone' => '0501234567'],
            ['name' => 'فاطمة علي', 'status' => 'active', 'email' => 'fatima@example.com', 'phone' => '0509876543'],
            ['name' => 'محمد خالد', 'status' => 'active', 'email' => 'mohammed@example.com', 'phone' => '0504567891'],
            ['name' => 'سارة أحمد', 'status' => 'active', 'email' => 'sara@example.com', 'phone' => '0502345678']
        ]);

        DB::table('notary_requests')->insert([
            ['request_id' => '#1234', 'title' => 'توثيق عقد بيع عقاري', 'type' => 'توثيق', 'status' => 'قيد المعالجة', 'icon' => 'file-contract', 'color' => 'blue'],
            ['request_id' => '#1233', 'title' => 'شهادة توقيع رقمي', 'type' => 'شهادة', 'status' => 'مكتمل', 'icon' => 'certificate', 'color' => 'green'],
            ['request_id' => '#1232', 'title' => 'استشارة قانونية', 'type' => 'استشارة', 'status' => 'مجدول', 'icon' => 'gavel', 'color' => 'purple'],
            ['request_id' => '#1231', 'title' => 'وكالة رسمية', 'type' => 'وكالة', 'status' => 'مكتمل', 'icon' => 'file-signature', 'color' => 'green']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clean up sample data
        DB::table('notary_requests')->delete();
        DB::table('notary_clients')->delete();
        DB::table('notary_documents')->delete();
        DB::table('compliance_activities')->delete();
        DB::table('internal_compliance')->delete();
        DB::table('regulatory_compliance')->delete();
        DB::table('compliance_reviews')->delete();
        DB::table('compliance_documents')->delete();
        DB::table('risk_assessments')->delete();
        DB::table('compliance_checks')->delete();
        DB::table('security_logs')->delete();
        DB::table('certificates')->delete();
        DB::table('digital_signatures')->delete();
    }
};
