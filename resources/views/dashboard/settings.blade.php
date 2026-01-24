@extends('layouts.dashboard')

@section('title', 'الإعدادات')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-800">إعدادات الحساب</h2>
                <p class="text-gray-600">إدارة تفضيلات الحساب وتنبيهات الخصوصية</p>
            </div>

            <form action="{{ route('dashboard.settings.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Notification Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">تنبيهات الإشعارات</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-700">إشعارات البريد الإلكتروني</h4>
                                <p class="text-sm text-gray-500">استلام تحديثات حول نشاط حسابك عبر البريد</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="marketing_consent" value="1" class="sr-only peer" {{ $user->marketing_consent ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-700">النشرة الإخبارية</h4>
                                <p class="text-sm text-gray-500">الاشتراك في النشرة الإخبارية للحصول على آخر العروض</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="newsletter_subscribed" value="1" class="sr-only peer" {{ $user->newsletter_subscribed ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-700">إشعارات الرسائل القصيرة (SMS)</h4>
                                <p class="text-sm text-gray-500">استلام تنبيهات هامة عبر الجوال</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sms_notifications" value="1" class="sr-only peer" {{ $user->sms_notifications ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Privacy & Security -->
                <div class="pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">الخصوصية والأمان</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-700">التحقق بخطوتين (2FA)</h4>
                                <p class="text-sm text-gray-500">إضافة طبقة أمان إضافية لحسابك</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="two_factor_enabled" value="1" class="sr-only peer" {{ $user->two_factor_enabled ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection