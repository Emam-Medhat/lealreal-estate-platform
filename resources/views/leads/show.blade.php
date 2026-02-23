@extends('admin.layouts.admin')

@section('title', 'تفاصيل العميل المحتمل • ' . $lead->first_name . ' ' . $lead->last_name)

@push('styles')
<style>
/* COPIED STYLES FROM ADMIN DASHBOARD TO ENSURE CONSISTENCY */
:root {
  --blue:      #0D6EFD;
  --blue2:     #0B5ED7;
  --blue-g:    linear-gradient(135deg, #0D6EFD, #0a58ca);
  --purple:    #6F42C1;
  --purple-g:  linear-gradient(135deg, #6F42C1, #59359a);
  --green:     #198754;
  --green-g:   linear-gradient(135deg, #198754, #146c43);
  --orange:    #FD7E14;
  --orange-g:  linear-gradient(135deg, #FD7E14, #dc6a0c);
  --teal:      #0DCAF0;
  --red:       #DC3545;
  --yellow:    #FFC107;
  --sc-blue:   linear-gradient(135deg, #0D6EFD 0%, #0a58ca 100%);
  --sc-purple: linear-gradient(135deg, #6F42C1 0%, #59359a 100%);
  --sc-green:  linear-gradient(135deg, #198754 0%, #146c43 100%);
  --sc-orange: linear-gradient(135deg, #FD7E14 0%, #dc6a0c 100%);
  --bg:    #F1F3F8;
  --bg2:   #EAECF3;
  --card:  #FFFFFF;
  --card2: #F8F9FC;
  --t1: #1A1F36; --t2: #374151; --t3: #6B7280; --t4: #9CA3AF;
  --bd: #E5E7EB; --bd2: #D1D5DB; --bd-blue: rgba(13,110,253,.25);
  --sh-sm:  0 1px 4px rgba(0,0,0,.06), 0 2px 8px rgba(0,0,0,.04);
  --sh-md:  0 4px 16px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.05);
  --sh-lg:  0 8px 32px rgba(0,0,0,.1);
  --sh-blue:   0 6px 24px rgba(13,110,253,.3);
  --sh-purple: 0 6px 24px rgba(111,66,193,.25);
  --sh-green:  0 6px 24px rgba(25,135,84,.25);
  --sh-orange: 0 6px 24px rgba(253,126,20,.25);
  --r1:10px; --r2:14px; --r3:18px; --r4:24px;
  --tr:    all .25s cubic-bezier(.4,0,.2,1);
  --tr-sp: all .35s cubic-bezier(.34,1.3,.64,1);
  --ff: 'Cairo','Plus Jakarta Sans',system-ui,sans-serif;
}

/* PANELS */
.panel { background:var(--card); border:1px solid var(--bd); border-radius:var(--r3); overflow:hidden; box-shadow:var(--sh-sm); transition:var(--tr); margin-bottom: 22px; }
.panel:hover { box-shadow:var(--sh-md); }
.panel-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--bd); }
.panel-title { font-size:14.5px; font-weight:800; color:var(--t1); }
.panel-sub { font-size:11px; color:var(--t4); margin-top:2px; }
.panel-body { padding:16px 20px; }

/* BUTTONS */
.tbtn {
  display:inline-flex; align-items:center; gap:7px;
  padding:7px 17px; border-radius:var(--r1);
  font-size:12.5px; font-weight:700;
  cursor:pointer; border:none; font-family:var(--ff);
  text-decoration:none; transition:var(--tr);
}
.tbtn-ghost { background:var(--bg); color:var(--t3); border:1px solid var(--bd); }
.tbtn-ghost:hover { background:var(--bg2); color:var(--t2); }
.tbtn-primary { background:var(--blue-g); color:white; box-shadow:var(--sh-blue); }
.tbtn-primary:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(13,110,253,.35); color: white; }

/* QUICK ACTIONS */
.qa-grid { display:grid; grid-template-columns:repeat(2, 1fr); gap:12px; }
.qa-card {
  display:flex; flex-direction:column; align-items:center; gap:8px;
  padding:16px 10px; background:var(--card2); border:1px solid var(--bd);
  border-radius:var(--r2); text-decoration:none; cursor:pointer;
  transition:var(--tr-sp); font-family:var(--ff);
}
.qa-card:hover { transform:translateY(-4px); background:var(--card); border-color:var(--bd-blue); box-shadow:var(--sh-md); }
.qa-ic { width:40px; height:40px; border-radius:var(--r1); display:flex; align-items:center; justify-content:center; font-size:16px; transition:var(--tr-sp); }
.qa-card:hover .qa-ic { transform:scale(1.12) rotate(5deg); }
.qa-label { font-size:11px; font-weight:700; color:var(--t3); text-align:center; line-height:1.4; transition:color .18s; }
.qa-card:hover .qa-label { color:var(--t1); }

.qic-blue   { background:rgba(13,110,253,.1);  color:var(--blue); }
.qic-purple { background:rgba(111,66,193,.1);  color:var(--purple); }
.qic-green  { background:rgba(25,135,84,.1);   color:var(--green); }
.qic-orange { background:rgba(253,126,20,.1);  color:var(--orange); }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900" style="font-family: var(--ff);">{{ $lead->first_name }} {{ $lead->last_name }}</h1>
            <div class="flex items-center gap-2 mt-2 text-sm text-gray-500">
                <a href="{{ route('leads.index') }}" class="hover:text-blue-600 transition-colors">العملاء المحتملين</a>
                <i class="fas fa-chevron-left text-xs"></i>
                <span>تفاصيل العميل</span>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('leads.edit', $lead) }}" class="tbtn tbtn-ghost">
                <i class="fas fa-edit text-gray-400"></i> تعديل
            </a>
            <button onclick="convertLead()" class="tbtn tbtn-primary">
                <i class="fas fa-arrow-right-arrow-left"></i> تحويل إلى عميل
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Column -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Lead Info Card -->
            <div class="panel">
                <div class="panel-head">
                    <h2 class="panel-title flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">
                            <i class="fas fa-user"></i>
                        </span>
                        البيانات الأساسية
                    </h2>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border" 
                        style="background-color: {{ $lead->status?->color ?? '#6c757d' }}15; color: {{ $lead->status?->color ?? '#6c757d' }}; border-color: {{ $lead->status?->color ?? '#6c757d' }}30;">
                        {{ $lead->status?->name ?? 'غير محدد' }}
                    </span>
                </div>
                
                <div class="panel-body grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">البريد الإلكتروني</label>
                            <div class="flex items-center gap-3">
                                <div class="bg-gray-50 p-2 rounded-lg text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <p class="text-gray-900 font-medium">{{ $lead->email }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">رقم الهاتف</label>
                            <div class="flex items-center gap-3">
                                <div class="bg-gray-50 p-2 rounded-lg text-gray-400">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <p class="text-gray-900 font-medium dir-ltr text-right">{{ $lead->phone ?? '—' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">العنوان</label>
                            <div class="flex items-center gap-3">
                                <div class="bg-gray-50 p-2 rounded-lg text-gray-400">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <p class="text-gray-900 font-medium">{{ $lead->address ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">الشركة</label>
                            <div class="flex items-center gap-3">
                                <div class="bg-gray-50 p-2 rounded-lg text-gray-400">
                                    <i class="fas fa-building"></i>
                                </div>
                                <p class="text-gray-900 font-medium">{{ $lead->company ?? '—' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">المنصب الوظيفي</label>
                            <div class="flex items-center gap-3">
                                <div class="bg-gray-50 p-2 rounded-lg text-gray-400">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <p class="text-gray-900 font-medium">{{ $lead->position ?? '—' }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">المصدر</label>
                            <div class="flex items-center gap-3">
                                <div class="bg-gray-50 p-2 rounded-lg text-gray-400">
                                    <i class="fas fa-share-alt"></i>
                                </div>
                                <p class="text-gray-900 font-medium">{{ $lead->source?->name ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($lead->notes)
                <div class="px-6 pb-6 pt-0">
                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                        <label class="text-xs font-bold text-amber-600 uppercase tracking-wider block mb-2">ملاحظات</label>
                        <p class="text-gray-700 leading-relaxed">{{ $lead->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Activity Timeline -->
            <div class="panel">
                <div class="panel-head">
                    <h2 class="panel-title flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center text-xs">
                            <i class="fas fa-history"></i>
                        </span>
                        سجل النشاطات
                    </h2>
                    <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">عرض الكل</button>
                </div>
                
                <div class="panel-body">
                    @if($lead->activities->isEmpty())
                        <div class="text-center py-12">
                            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-clock text-gray-300 text-2xl"></i>
                            </div>
                            <h3 class="text-gray-900 font-medium mb-1">لا توجد نشاطات</h3>
                            <p class="text-gray-500 text-sm">لم يتم تسجيل أي نشاط لهذا العميل بعد</p>
                        </div>
                    @else
                        <div class="relative space-y-8 before:absolute before:inset-0 before:mr-5 before:h-full before:w-0.5 before:-translate-x-px before:bg-gradient-to-b before:from-gray-200 before:via-gray-200 before:to-transparent">
                            @foreach($lead->activities as $activity)
                                <div class="relative flex gap-6 group">
                                    <div class="absolute right-0 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-4 border-white bg-{{ $activity->type == 'call' ? 'blue' : ($activity->type == 'email' ? 'purple' : 'green') }}-50 text-{{ $activity->type == 'call' ? 'blue' : ($activity->type == 'email' ? 'purple' : 'green') }}-600 shadow-sm transition-all group-hover:scale-110">
                                        <i class="fas fa-{{ $activity->type == 'call' ? 'phone' : ($activity->type == 'email' ? 'envelope' : 'calendar-days') }} text-sm"></i>
                                    </div>
                                    <div class="mr-14 flex-auto rounded-xl bg-gray-50 p-4 ring-1 ring-inset ring-gray-200 transition-all hover:bg-white hover:shadow-md">
                                        <div class="flex justify-between gap-x-4">
                                            <div class="py-0.5 text-xs leading-5 text-gray-500">
                                                <span class="font-medium text-gray-900">{{ $activity->title }}</span>
                                            </div>
                                            <time datetime="{{ $activity->activity_date }}" class="flex-none py-0.5 text-xs leading-5 text-gray-500">{{ $activity->activity_date->format('Y-m-d H:i') }}</time>
                                        </div>
                                        <p class="text-sm leading-6 text-gray-600 mt-1">{{ $activity->description }}</p>
                                        <div class="mt-2 flex items-center gap-2 text-xs text-gray-400">
                                            <i class="fas fa-user-circle"></i>
                                            <span>{{ $activity->user->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            
            <!-- Lead Score -->
            <div class="panel">
                <div class="panel-body">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-6">تقييم العميل</h3>
                    
                    <div class="flex items-end gap-2 mb-4">
                        <span class="text-5xl font-extrabold text-gray-900 tracking-tight">{{ $lead->lead_score ?? 0 }}</span>
                        <span class="text-gray-400 font-medium mb-2">/ 100</span>
                    </div>

                    <div class="w-full bg-gray-100 rounded-full h-3 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-1000 ease-out" 
                             style="width: {{ $lead->lead_score ?? 0 }}%"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <span class="block text-xs text-gray-500 mb-1">القيمة المتوقعة</span>
                            <span class="block font-bold text-gray-800">{{ $lead->estimated_value ? number_format($lead->estimated_value) : '—' }}</span>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <span class="block text-xs text-gray-500 mb-1">المسؤول</span>
                            <span class="block font-bold text-gray-800 truncate">{{ $lead->assignedTo?->name ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="panel">
                <div class="panel-body">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">إجراءات سريعة</h3>
                    <div class="qa-grid">
                        <button onclick="addActivity('call')" class="qa-card group">
                            <div class="qa-ic qic-blue">
                                <i class="fas fa-phone"></i>
                            </div>
                            <span class="qa-label">اتصال</span>
                        </button>
                        
                        <button onclick="addActivity('email')" class="qa-card group">
                            <div class="qa-ic qic-purple">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <span class="qa-label">بريد</span>
                        </button>

                        <button onclick="addActivity('meeting')" class="qa-card group">
                            <div class="qa-ic qic-green">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <span class="qa-label">اجتماع</span>
                        </button>

                        <button onclick="addNote()" class="qa-card group">
                            <div class="qa-ic qic-orange">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <span class="qa-label">ملاحظة</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="panel">
                <div class="panel-body">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">الوسوم</h3>
                    @if($lead->tags && json_decode($lead->tags))
                        <div class="flex flex-wrap gap-2">
                            @foreach(json_decode($lead->tags) as $tag)
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors cursor-pointer">
                                    <i class="fas fa-hashtag text-gray-400 ml-1.5 text-[10px]"></i>
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 border-2 border-dashed border-gray-100 rounded-xl">
                            <p class="text-gray-400 text-sm">لا توجد وسوم</p>
                            <button class="mt-2 text-xs text-blue-600 hover:underline font-medium">إضافة وسم</button>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function convertLead() {
        // TODO: Implement lead conversion modal/logic
        alert('سيتم تنفيذ تحويل العميل هنا');
    }

    function addActivity(type) {
        // TODO: Implement add activity modal/logic
        const types = {
            'call': 'تسجيل مكالمة',
            'email': 'إرسال بريد',
            'meeting': 'جدولة اجتماع'
        };
        alert('سيتم ' + (types[type] || type) + ' هنا');
    }

    function addNote() {
        // TODO: Implement add note modal/logic
        alert('سيتم إضافة ملاحظة هنا');
    }
</script>
@endpush
