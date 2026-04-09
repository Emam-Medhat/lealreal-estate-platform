<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'report_id' => 'required|exists:reports,id',
            'format' => ['required', Rule::in(['pdf', 'excel', 'csv', 'html', 'json'])],
            'export_options' => 'nullable|array',
            'export_options.page_size' => 'nullable|in:A4,A3,Letter,Legal',
            'export_options.orientation' => 'nullable|in:portrait,landscape',
            'export_options.margin_top' => 'nullable|numeric|min:0|max:100',
            'export_options.margin_bottom' => 'nullable|numeric|min:0|max:100',
            'export_options.margin_left' => 'nullable|numeric|min:0|max:100',
            'export_options.margin_right' => 'nullable|numeric|min:0|max:100',
            'export_options.include_header' => 'nullable|boolean',
            'export_options.include_footer' => 'nullable|boolean',
            'export_options.include_page_numbers' => 'nullable|boolean',
            'export_options.include_toc' => 'nullable|boolean',
            'export_options.include_index' => 'nullable|boolean',
            'export_options.include_charts' => 'nullable|boolean',
            'export_options.include_images' => 'nullable|boolean',
            'export_options.compress_images' => 'nullable|boolean',
            'export_options.image_quality' => 'nullable|integer|min:1|max:100',
            'export_options.watermark' => 'nullable|string|max:255',
            'export_options.watermark_position' => 'nullable|in:top-left,top-center,top-right,middle-left,middle-center,middle-right,bottom-left,bottom-center,bottom-right',
            'export_options.watermark_opacity' => 'nullable|numeric|min:0|max:1',
            'export_options.watermark_size' => 'nullable|integer|min:8|max:72',
            'export_options.password' => 'nullable|string|min:8|max:255',
            'export_options.permissions' => 'nullable|array',
            'export_options.permissions.*' => 'in:print,copy,modify,annotate,fill-form,extract,assemble',
            'export_options.filename' => 'nullable|string|max:255',
            'export_options.date_format' => 'nullable|string|max:50',
            'export_options.number_format' => 'nullable|string|max:50',
            'export_options.currency_format' => 'nullable|string|max:50',
            'export_options.decimal_places' => 'nullable|integer|min:0|max:10',
            'export_options.group_separator' => 'nullable|string|max:5',
            'export_options.decimal_separator' => 'nullable|string|max:5',
            'export_options.sheet_name' => 'nullable|string|max:31',
            'export_options.auto_width' => 'nullable|boolean',
            'export_options.freeze_header' => 'nullable|boolean',
            'export_options.filter_enabled' => 'nullable|boolean',
            'export_options.delimiter' => 'nullable|string|max:5',
            'export_options.enclosure' => 'nullable|string|max:5',
            'export_options.escape' => 'nullable|string|max:5',
            'export_options.include_bom' => 'nullable|boolean',
            'export_options.line_ending' => 'nullable|in:\n,\r\n,\r',
            'export_options.css_file' => 'nullable|url',
            'export_options.embed_css' => 'nullable|boolean',
            'export_options.minify_html' => 'nullable|boolean',
            'export_options.pretty_json' => 'nullable|boolean',
            'delivery_options' => 'nullable|array',
            'delivery_options.method' => 'required_with:delivery_options|in:download,email,ftp,cloud,webhook',
            'delivery_options.email_to' => 'required_if:delivery_options.method,email|email',
            'delivery_options.email_cc' => 'nullable|array',
            'delivery_options.email_cc.*' => 'email',
            'delivery_options.email_bcc' => 'nullable|array',
            'delivery_options.email_bcc.*' => 'email',
            'delivery_options.email_subject' => 'required_if:delivery_options.method,email|string|max:255',
            'delivery_options.email_body' => 'nullable|string',
            'delivery_options.email_attachments' => 'nullable|array',
            'delivery_options.email_attachments.*' => 'string|max:255',
            'delivery_options.ftp_host' => 'required_if:delivery_options.method,ftp|string|max:255',
            'delivery_options.ftp_port' => 'nullable|integer|min:1|max:65535',
            'delivery_options.ftp_username' => 'required_if:delivery_options.method,ftp|string|max:255',
            'delivery_options.ftp_password' => 'required_if:delivery_options.method,ftp|string|max:255',
            'delivery_options.ftp_path' => 'nullable|string|max:255',
            'delivery_options.ftp_passive' => 'nullable|boolean',
            'delivery_options.ftp_ssl' => 'nullable|boolean',
            'delivery_options.cloud_provider' => 'required_if:delivery_options.method,cloud|in:aws,azure,google',
            'delivery_options.cloud_bucket' => 'required_if:delivery_options.method,cloud|string|max:255',
            'delivery_options.cloud_path' => 'nullable|string|max:255',
            'delivery_options.cloud_acl' => 'nullable|string|max:255',
            'delivery_options.webhook_url' => 'required_if:delivery_options.method,webhook|url',
            'delivery_options.webhook_method' => 'nullable|in:GET,POST,PUT',
            'delivery_options.webhook_headers' => 'nullable|array',
            'delivery_options.webhook_headers.*.key' => 'required|string|max:255',
            'delivery_options.webhook_headers.*.value' => 'required|string|max:255',
            'delivery_options.webhook_timeout' => 'nullable|integer|min:1|max:300',
            'security_options' => 'nullable|array',
            'security_options.encrypt_file' => 'nullable|boolean',
            'security_options.encryption_key' => 'required_if:security_options.encrypt_file,true|string|min:16',
            'security_options.sign_file' => 'nullable|boolean',
            'security_options.signature_key' => 'required_if:security_options.sign_file,true|string',
            'security_options.expire_after_downloads' => 'nullable|integer|min:1|max:1000',
            'security_options.expire_after_hours' => 'nullable|integer|min:1|max:8760',
            'security_options.ip_whitelist' => 'nullable|array',
            'security_options.ip_whitelist.*' => 'ip',
            'security_options.domain_whitelist' => 'nullable|array',
            'security_options.domain_whitelist.*' => 'string|max:255',
            'security_options.require_auth' => 'nullable|boolean',
            'security_options.auth_users' => 'required_if:security_options.require_auth,true|array',
            'security_options.auth_users.*' => 'exists:users,id',
            'filter_options' => 'nullable|array',
            'filter_options.include_pages' => 'nullable|array',
            'filter_options.include_pages.*' => 'integer|min:1',
            'filter_options.exclude_pages' => 'nullable|array',
            'filter_options.exclude_pages.*' => 'integer|min:1',
            'filter_options.include_sections' => 'nullable|array',
            'filter_options.include_sections.*' => 'string|max:255',
            'filter_options.exclude_sections' => 'nullable|array',
            'filter_options.exclude_sections.*' => 'string|max:255',
            'filter_options.date_range' => 'nullable|array',
            'filter_options.date_range.start' => 'nullable|date|required_with:filter_options.date_range.end',
            'filter_options.date_range.end' => 'nullable|date|required_with:filter_options.date_range.start|after_or_equal:filter_options.date_range.start',
            'filter_options.data_filters' => 'nullable|array',
            'filter_options.data_filters.*.column' => 'required|string|max:255',
            'filter_options.data_filters.*.operator' => 'required|in:=,!=,>,<,>=,<=,like,not_like,in,not_in,between,not_between,is_null,is_not_null',
            'filter_options.data_filters.*.value' => 'required|string',
            'custom_options' => 'nullable|array',
            'custom_options.*.key' => 'required|string|max:255',
            'custom_options.*.value' => 'required|string',
            'custom_options.*.type' => 'required|in:string,number,boolean,array,object',
        ];
    }

    public function messages()
    {
        return [
            'report_id.required' => 'معرف التقرير مطلوب',
            'report_id.exists' => 'التقرير المحدد غير موجود',
            'format.required' => 'تنسيق التصدير مطلوب',
            'format.in' => 'تنسيق التصدير غير صالح',
            'export_options.page_size.in' => 'حجم الصفحة غير صالح',
            'export_options.orientation.in' => 'اتجاه الصفحة غير صالح',
            'export_options.margin_top.numeric' => 'الهامش العلوي يجب أن يكون رقماً',
            'export_options.margin_top.min' => 'الهامش العلوي يجب أن يكون 0 أو أكثر',
            'export_options.margin_top.max' => 'الهامش العلوي يجب ألا يتجاوز 100',
            'export_options.margin_bottom.numeric' => 'الهامش السفلي يجب أن يكون رقماً',
            'export_options.margin_bottom.min' => 'الهامش السفلي يجب أن يكون 0 أو أكثر',
            'export_options.margin_bottom.max' => 'الهامش السفلي يجب ألا يتجاوز 100',
            'export_options.margin_left.numeric' => 'الهامش الأيسر يجب أن يكون رقماً',
            'export_options.margin_left.min' => 'الهامش الأيسر يجب أن يكون 0 أو أكثر',
            'export_options.margin_left.max' => 'الهامش الأيسر يجب ألا يتجاوز 100',
            'export_options.margin_right.numeric' => 'الهامش الأيمن يجب أن يكون رقماً',
            'export_options.margin_right.min' => 'الهامش الأيمن يجب أن يكون 0 أو أكثر',
            'export_options.margin_right.max' => 'الهامش الأيمن يجب ألا يتجاوز 100',
            'export_options.watermark_position.in' => 'موضع العلامة المائية غير صالح',
            'export_options.watermark_opacity.numeric' => 'شفافية العلامة المائية يجب أن تكون رقماً',
            'export_options.watermark_opacity.min' => 'شفافية العلامة المائية يجب أن تكون 0 أو أكثر',
            'export_options.watermark_opacity.max' => 'شفافية العلامة المائية يجب ألا تتجاوز 1',
            'export_options.watermark_size.integer' => 'حجم العلامة المائية يجب أن يكون رقماً',
            'export_options.watermark_size.min' => 'حجم العلامة المائية يجب أن يكون 8 أو أكثر',
            'export_options.watermark_size.max' => 'حجم العلامة المائية يجب ألا يتجاوز 72',
            'export_options.password.min' => 'كلمة المرور يجب أن تتكون من 8 أحرف على الأقل',
            'export_options.password.max' => 'كلمة المرور يجب ألا تتجاوز 255 حرف',
            'export_options.permissions.*.in' => 'صلاحية التصدير غير صالحة',
            'export_options.filename.max' => 'اسم الملف يجب ألا يتجاوز 255 حرف',
            'export_options.date_format.max' => 'تنسيق التاريخ يجب ألا يتجاوز 50 حرف',
            'export_options.number_format.max' => 'تنسيق الأرقام يجب ألا يتجاوز 50 حرف',
            'export_options.currency_format.max' => 'تنسيق العملة يجب ألا يتجاوز 50 حرف',
            'export_options.decimal_places.integer' => 'عدد الأماكن العشرية يجب أن يكون رقماً',
            'export_options.decimal_places.min' => 'عدد الأماكن العشرية يجب أن يكون 0 أو أكثر',
            'export_options.decimal_places.max' => 'عدد الأماكن العشرية يجب ألا يتجاوز 10',
            'export_options.group_separator.max' => 'فاصل المجموعات يجب ألا يتجاوز 5 أحرف',
            'export_options.decimal_separator.max' => 'فاصل العشري يجب ألا يتجاوز 5 أحرف',
            'export_options.sheet_name.max' => 'اسم الورقة يجب ألا يتجاوز 31 حرف',
            'export_options.delimiter.max' => 'المحدد يجب ألا يتجاوز 5 أحرف',
            'export_options.enclosure.max' => 'المغلف يجب ألا يتجاوز 5 أحرف',
            'export_options.escape.max' => 'الهروب يجب ألا يتجاوز 5 أحرف',
            'export_options.line_ending.in' => 'نهاية السطر غير صالحة',
            'delivery_options.method.required_with' => 'طريقة التسليم مطلوبة عند تحديد خيارات التسليم',
            'delivery_options.method.in' => 'طريقة التسليم غير صالحة',
            'delivery_options.email_to.required_if' => 'البريد الإلكتروني للمستلم مطلوب عند اختيار التسليم عبر البريد',
            'delivery_options.email_to.email' => 'البريد الإلكتروني للمستلم غير صالح',
            'delivery_options.email_cc.*.email' => 'البريد الإلكتروني CC غير صالح',
            'delivery_options.email_bcc.*.email' => 'البريد الإلكتروني BCC غير صالح',
            'delivery_options.email_subject.required_if' => 'عنوان البريد الإلكتروني مطلوب عند اختيار التسليم عبر البريد',
            'delivery_options.email_subject.max' => 'عنوان البريد الإلكتروني يجب ألا يتجاوز 255 حرف',
            'delivery_options.email_attachments.*.max' => 'اسم المرفق يجب ألا يتجاوز 255 حرف',
            'delivery_options.ftp_host.required_if' => 'مضيف FTP مطلوب عند اختيار التسليم عبر FTP',
            'delivery_options.ftp_host.max' => 'مضيف FTP يجب ألا يتجاوز 255 حرف',
            'delivery_options.ftp_port.min' => 'منفذ FTP يجب أن يكون 1 أو أكثر',
            'delivery_options.ftp_port.max' => 'منفذ FTP يجب ألا يتجاوز 65535',
            'delivery_options.ftp_username.required_if' => 'اسم مستخدم FTP مطلوب عند اختيار التسليم عبر FTP',
            'delivery_options.ftp_username.max' => 'اسم مستخدم FTP يجب ألا يتجاوز 255 حرف',
            'delivery_options.ftp_password.required_if' => 'كلمة مرور FTP مطلوبة عند اختيار التسليم عبر FTP',
            'delivery_options.ftp_password.max' => 'كلمة مرور FTP يجب ألا تتجاوز 255 حرف',
            'delivery_options.ftp_path.max' => 'مسار FTP يجب ألا يتجاوز 255 حرف',
            'delivery_options.cloud_provider.required_if' => 'مزود السحابة مطلوب عند اختيار التسليم السحابي',
            'delivery_options.cloud_provider.in' => 'مزود السحابة غير صالح',
            'delivery_options.cloud_bucket.required_if' => 'حاوية السحابة مطلوبة عند اختيار التسليم السحابي',
            'delivery_options.cloud_bucket.max' => 'حاوية السحابة يجب ألا تتجاوز 255 حرف',
            'delivery_options.cloud_path.max' => 'مسار السحابة يجب ألا يتجاوز 255 حرف',
            'delivery_options.cloud_acl.max' => 'ACL السحابة يجب ألا يتجاوز 255 حرف',
            'delivery_options.webhook_url.required_if' => 'رابط webhook مطلوب عند اختيار التسليم عبر webhook',
            'delivery_options.webhook_url.url' => 'رابط webhook غير صالح',
            'delivery_options.webhook_method.in' => 'طريقة webhook غير صالحة',
            'delivery_options.webhook_headers.*.key.required' => 'مفتاح رأس webhook مطلوب',
            'delivery_options.webhook_headers.*.key.max' => 'مفتاح رأس webhook يجب ألا يتجاوز 255 حرف',
            'delivery_options.webhook_headers.*.value.required' => 'قيمة رأس webhook مطلوبة',
            'delivery_options.webhook_headers.*.value.max' => 'قيمة رأس webhook يجب ألا تتجاوز 255 حرف',
            'delivery_options.webhook_timeout.min' => 'مهلة webhook يجب أن تكون 1 ثانية على الأقل',
            'delivery_options.webhook_timeout.max' => 'مهلة webhook يجب ألا تتجاوز 300 ثانية',
            'security_options.encryption_key.required_if' => 'مفتاح التشفير مطلوب عند تفعيل التشفير',
            'security_options.encryption_key.min' => 'مفتاح التشفير يجب أن يتكون من 16 حرف على الأقل',
            'security_options.signature_key.required_if' => 'مفتاح التوقيع مطلوب عند تفعيل التوقيع',
            'security_options.expire_after_downloads.min' => 'انتهاء الصلاحية بعد التحميل يجب أن يكون 1 أو أكثر',
            'security_options.expire_after_downloads.max' => 'انتهاء الصلاحية بعد التحميل يجب ألا يتجاوز 1000',
            'security_options.expire_after_hours.min' => 'انتهاء الصلاحية بعد الساعات يجب أن يكون 1 ساعة على الأقل',
            'security_options.expire_after_hours.max' => 'انتهاء الصلاحية بعد الساعات يجب ألا يتجاوز 8760 ساعة',
            'security_options.ip_whitelist.*.ip' => 'عنوان IP غير صالح',
            'security_options.domain_whitelist.*.max' => 'اسم النطاق يجب ألا يتجاوز 255 حرف',
            'security_options.auth_users.required_if' => 'قائمة المستخدمين مطلوبة عند تفعيل المصادقة',
            'security_options.auth_users.*.exists' => 'المستخدم المحدد غير موجود',
            'filter_options.include_pages.*.integer' => 'رقم الصفحة يجب أن يكون رقماً',
            'filter_options.include_pages.*.min' => 'رقم الصفحة يجب أن يكون 1 أو أكثر',
            'filter_options.exclude_pages.*.integer' => 'رقم الصفحة يجب أن يكون رقماً',
            'filter_options.exclude_pages.*.min' => 'رقم الصفحة يجب أن يكون 1 أو أكثر',
            'filter_options.include_sections.*.max' => 'اسم القسم يجب ألا يتجاوز 255 حرف',
            'filter_options.exclude_sections.*.max' => 'اسم القسم يجب ألا يتجاوز 255 حرف',
            'filter_options.date_range.start.required_with' => 'تاريخ البداية مطلوب عند تحديد تاريخ النهاية',
            'filter_options.date_range.end.required_with' => 'تاريخ النهاية مطلوب عند تحديد تاريخ البداية',
            'filter_options.date_range.end.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
            'filter_options.data_filters.*.column.required' => 'اسم العمود مطلوب',
            'filter_options.data_filters.*.column.max' => 'اسم العمود يجب ألا يتجاوز 255 حرف',
            'filter_options.data_filters.*.operator.required' => 'المعامل مطلوب',
            'filter_options.data_filters.*.operator.in' => 'المعامل غير صالح',
            'filter_options.data_filters.*.value.required' => 'القيمة مطلوبة',
            'filter_options.data_filters.*.value.max' => 'القيمة يجب ألا تتجاوز 255 حرف',
            'custom_options.*.key.required' => 'مفتاح الخيار المخصص مطلوب',
            'custom_options.*.key.max' => 'مفتاح الخيار المخصص يجب ألا يتجاوز 255 حرف',
            'custom_options.*.value.required' => 'قيمة الخيار المخصصة مطلوبة',
            'custom_options.*.type.required' => 'نوع الخيار المخصص مطلوب',
            'custom_options.*.type.in' => 'نوع الخيار المخصص غير صالح',
        ];
    }

    public function attributes()
    {
        return [
            'report_id' => 'معرف التقرير',
            'format' => 'تنسيق التصدير',
            'export_options' => 'خيارات التصدير',
            'export_options.page_size' => 'حجم الصفحة',
            'export_options.orientation' => 'اتجاه الصفحة',
            'export_options.margin_top' => 'الهامش العلوي',
            'export_options.margin_bottom' => 'الهامش السفلي',
            'export_options.margin_left' => 'الهامش الأيسر',
            'export_options.margin_right' => 'الهامش الأيمن',
            'export_options.include_header' => 'تضمين الرأس',
            'export_options.include_footer' => 'تضمين التذييل',
            'export_options.include_page_numbers' => 'تضمين أرقام الصفحات',
            'export_options.include_toc' => 'تضمين جدول المحتويات',
            'export_options.include_index' => 'تضمين الفهرس',
            'export_options.include_charts' => 'تضمين الرسوم البيانية',
            'export_options.include_images' => 'تضمين الصور',
            'export_options.compress_images' => 'ضغط الصور',
            'export_options.image_quality' => 'جودة الصورة',
            'export_options.watermark' => 'العلامة المائية',
            'export_options.watermark_position' => 'موضع العلامة المائية',
            'export_options.watermark_opacity' => 'شفافية العلامة المائية',
            'export_options.watermark_size' => 'حجم العلامة المائية',
            'export_options.password' => 'كلمة المرور',
            'export_options.permissions' => 'الصلاحيات',
            'export_options.filename' => 'اسم الملف',
            'export_options.date_format' => 'تنسيق التاريخ',
            'export_options.number_format' => 'تنسيق الأرقام',
            'export_options.currency_format' => 'تنسيق العملة',
            'export_options.decimal_places' => 'الأماكن العشرية',
            'export_options.group_separator' => 'فاصل المجموعات',
            'export_options.decimal_separator' => 'فاصل العشري',
            'export_options.sheet_name' => 'اسم الورقة',
            'export_options.auto_width' => 'العرض التلقائي',
            'export_options.freeze_header' => 'تجميد الرأس',
            'export_options.filter_enabled' => 'تفعيل الفلتر',
            'export_options.delimiter' => 'المحدد',
            'export_options.enclosure' => 'المغلف',
            'export_options.escape' => 'الهروب',
            'export_options.include_bom' => 'تضمين BOM',
            'export_options.line_ending' => 'نهاية السطر',
            'export_options.css_file' => 'ملف CSS',
            'export_options.embed_css' => 'تضمين CSS',
            'export_options.minify_html' => 'ضغط HTML',
            'export_options.pretty_json' => 'تنسيق JSON',
            'delivery_options' => 'خيارات التسليم',
            'delivery_options.method' => 'طريقة التسليم',
            'delivery_options.email_to' => 'البريد الإلكتروني للمستلم',
            'delivery_options.email_cc' => 'نسخة كربونية',
            'delivery_options.email_bcc' => 'نسخة كربونية مخفية',
            'delivery_options.email_subject' => 'عنوان البريد الإلكتروني',
            'delivery_options.email_body' => 'نص البريد الإلكتروني',
            'delivery_options.email_attachments' => 'مرفقات البريد الإلكتروني',
            'delivery_options.ftp_host' => 'مضيف FTP',
            'delivery_options.ftp_port' => 'منفذ FTP',
            'delivery_options.ftp_username' => 'اسم مستخدم FTP',
            'delivery_options.ftp_password' => 'كلمة مرور FTP',
            'delivery_options.ftp_path' => 'مسار FTP',
            'delivery_options.ftp_passive' => 'FTP سلبي',
            'delivery_options.ftp_ssl' => 'FTP SSL',
            'delivery_options.cloud_provider' => 'مزود السحابة',
            'delivery_options.cloud_bucket' => 'حاوية السحابة',
            'delivery_options.cloud_path' => 'مسار السحابة',
            'delivery_options.cloud_acl' => 'ACL السحابة',
            'delivery_options.webhook_url' => 'رابط webhook',
            'delivery_options.webhook_method' => 'طريقة webhook',
            'delivery_options.webhook_headers' => 'رؤوس webhook',
            'delivery_options.webhook_timeout' => 'مهلة webhook',
            'security_options' => 'خيارات الأمان',
            'security_options.encrypt_file' => 'تشفير الملف',
            'security_options.encryption_key' => 'مفتاح التشفير',
            'security_options.sign_file' => 'توقيع الملف',
            'security_options.signature_key' => 'مفتاح التوقيع',
            'security_options.expire_after_downloads' => 'انتهاء الصلاحية بعد التحميل',
            'security_options.expire_after_hours' => 'انتهاء الصلاحية بعد الساعات',
            'security_options.ip_whitelist' => 'القائمة البيضاء لعناوين IP',
            'security_options.domain_whitelist' => 'القائمة البيضاء للنطاقات',
            'security_options.require_auth' => 'تطلب المصادقة',
            'security_options.auth_users' => 'المستخدمون المصرح لهم',
            'filter_options' => 'خيارات الفلترة',
            'filter_options.include_pages' => 'تضمين الصفحات',
            'filter_options.exclude_pages' => 'استبعاد الصفحات',
            'filter_options.include_sections' => 'تضمين الأقسام',
            'filter_options.exclude_sections' => 'استبعاد الأقسام',
            'filter_options.date_range' => 'نطاق التاريخ',
            'filter_options.date_range.start' => 'تاريخ البداية',
            'filter_options.date_range.end' => 'تاريخ النهاية',
            'filter_options.data_filters' => 'فلاتر البيانات',
            'filter_options.data_filters.*.column' => 'اسم العمود',
            'filter_options.data_filters.*.operator' => 'المعامل',
            'filter_options.data_filters.*.value' => 'القيمة',
            'custom_options' => 'خيارات مخصصة',
            'custom_options.*.key' => 'مفتاح الخيار',
            'custom_options.*.value' => 'قيمة الخيار',
            'custom_options.*.type' => 'نوع الخيار',
        ];
    }

    protected function prepareForValidation()
    {
        // Convert boolean values
        $this->merge([
            'export_options.include_header' => $this->boolean('export_options.include_header', true),
            'export_options.include_footer' => $this->boolean('export_options.include_footer', true),
            'export_options.include_page_numbers' => $this->boolean('export_options.include_page_numbers', true),
            'export_options.include_toc' => $this->boolean('export_options.include_toc', false),
            'export_options.include_index' => $this->boolean('export_options.include_index', false),
            'export_options.include_charts' => $this->boolean('export_options.include_charts', true),
            'export_options.include_images' => $this->boolean('export_options.include_images', true),
            'export_options.compress_images' => $this->boolean('export_options.compress_images', false),
            'export_options.embed_css' => $this->boolean('export_options.embed_css', false),
            'export_options.minify_html' => $this->boolean('export_options.minify_html', false),
            'export_options.pretty_json' => $this->boolean('export_options.pretty_json', true),
            'export_options.include_bom' => $this->boolean('export_options.include_bom', false),
            'export_options.auto_width' => $this->boolean('export_options.auto_width', true),
            'export_options.freeze_header' => $this->boolean('export_options.freeze_header', false),
            'export_options.filter_enabled' => $this->boolean('export_options.filter_enabled', false),
            'delivery_options.ftp_passive' => $this->boolean('delivery_options.ftp_passive', true),
            'delivery_options.ftp_ssl' => $this->boolean('delivery_options.ftp_ssl', false),
            'security_options.encrypt_file' => $this->boolean('security_options.encrypt_file', false),
            'security_options.sign_file' => $this->boolean('security_options.sign_file', false),
            'security_options.require_auth' => $this->boolean('security_options.require_auth', false),
        ]);

        // Set default values
        if (!$this->has('export_options.page_size')) {
            $this->merge(['export_options.page_size' => 'A4']);
        }

        if (!$this->has('export_options.orientation')) {
            $this->merge(['export_options.orientation' => 'portrait']);
        }

        if (!$this->has('export_options.margin_top')) {
            $this->merge(['export_options.margin_top' => 20]);
        }

        if (!$this->has('export_options.margin_bottom')) {
            $this->merge(['export_options.margin_bottom' => 20]);
        }

        if (!$this->has('export_options.margin_left')) {
            $this->merge(['export_options.margin_left' => 20]);
        }

        if (!$this->has('export_options.margin_right')) {
            $this->merge(['export_options.margin_right' => 20]);
        }

        if (!$this->has('export_options.watermark_opacity')) {
            $this->merge(['export_options.watermark_opacity' => 0.3]);
        }

        if (!$this->has('export_options.watermark_size')) {
            $this->merge(['export_options.watermark_size' => 24]);
        }

        if (!$this->has('export_options.decimal_places')) {
            $this->merge(['export_options.decimal_places' => 2]);
        }

        if (!$this->has('export_options.delimiter')) {
            $this->merge(['export_options.delimiter' => ',']);
        }

        if (!$this->has('export_options.enclosure')) {
            $this->merge(['export_options.enclosure' => '"']);
        }

        if (!$this->has('export_options.escape')) {
            $this->merge(['export_options.escape' => '\\']);
        }

        if (!$this->has('export_options.line_ending')) {
            $this->merge(['export_options.line_ending' => "\n"]);
        }

        if (!$this->has('delivery_options.ftp_port')) {
            $this->merge(['delivery_options.ftp_port' => 21]);
        }

        if (!$this->has('delivery_options.webhook_method')) {
            $this->merge(['delivery_options.webhook_method' => 'POST']);
        }

        if (!$this->has('delivery_options.webhook_timeout')) {
            $this->merge(['delivery_options.webhook_timeout' => 30]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $validated = $validator->getData();

            // Additional validation logic
            if (isset($validated['export_options']['margin_top']) && isset($validated['export_options']['margin_bottom'])) {
                $totalMargin = $validated['export_options']['margin_top'] + $validated['export_options']['margin_bottom'];
                if ($totalMargin > 150) {
                    $validator->errors()->add('export_options.margin_bottom', 'مجموع الهوامش العلوية والسفلية يجب ألا يتجاوز 150');
                }
            }

            if (isset($validated['export_options']['margin_left']) && isset($validated['export_options']['margin_right'])) {
                $totalMargin = $validated['export_options']['margin_left'] + $validated['export_options']['margin_right'];
                if ($totalMargin > 150) {
                    $validator->errors()->add('export_options.margin_right', 'مجموع الهوامش اليسرى واليمنى يجب ألا يتجاوز 150');
                }
            }

            // Validate report ownership
            $report = \App\Models\Report::find($validated['report_id']);
            if ($report && $report->user_id !== auth()->id()) {
                $validator->errors()->add('report_id', 'ليست لديك صلاحية الوصول إلى هذا التقرير');
            }
        });
    }
}
