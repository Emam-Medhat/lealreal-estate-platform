<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>اختبار إنشاء عقار مع صور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>اختبار إنشاء عقار مع صور</h1>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <form action="{{ route('test.property.create') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">عنوان العقار</label>
                    <input type="text" name="title" class="form-control" value="عقار اختبار" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">نوع العقار</label>
                    <select name="property_type_id" class="form-control" required>
                        <option value="8">بنتهاوس</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">نوع الإعلان</label>
                    <select name="listing_type" class="form-control" required>
                        <option value="sale">بيع</option>
                        <option value="rent">إيجار</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">السعر</label>
                    <input type="number" name="price" class="form-control" value="100000" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">العملة</label>
                    <select name="currency" class="form-control" required>
                        <option value="SAR">ريال سعودي</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" class="form-control" value="الرياض، المملكة العربية السعودية" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">غرف نوم</label>
                    <input type="number" name="bedrooms" class="form-control" value="3">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">حمامات</label>
                    <input type="number" name="bathrooms" class="form-control" value="2">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">المساحة (متر مربع)</label>
                    <input type="number" name="area" class="form-control" value="150">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">وحدة المساحة</label>
                    <select name="area_unit" class="form-control">
                        <option value="sq_m">متر مربع</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">وصف العقار</label>
                <textarea name="description" class="form-control" rows="3">عقار اختبار للتحقق من رفع الصور</textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">الصور الرئيسية</label>
                <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                <small class="form-text">يمكنك اختيار أكثر من صورة</small>
            </div>
            
            <button type="submit" class="btn btn-primary">إنشاء العقار</button>
        </form>
        
        <hr>
        
        <h3>معلومات التصحيح:</h3>
        <div class="alert alert-info">
            <p><strong>الـ Log:</strong> يمكنك فحص الـ logs في <code>storage/logs/laravel.log</code></p>
            <p><strong>اختبار الصور:</strong> <a href="{{ route('test.image.upload') }}" target="_blank">اضغط هنا لاختبار رفع الصور</a></p>
        </div>
    </div>
</body>
</html>
