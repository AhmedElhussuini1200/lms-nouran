# بنية المشروع - Architecture

## نظرة عامة

تم بناء المشروع باستخدام نمط **MVC** مع **Repository Pattern** و **Service Layer** لضمان فصل الاهتمامات (Separation of Concerns) وسهولة الصيانة والاختبار.

## البنية الهيكلية

```
app/
├── Http/
│   └── Controllers/          # Controllers - طبقة التحكم
├── Models/                   # Models - طبقة البيانات
├── Services/                 # Services - طبقة الأعمال
├── Repositories/             # Repositories - طبقة الوصول للبيانات
└── Interfaces/               # Interfaces - التعاقدات (Contracts)
```

## الطبقات (Layers)

### 1. Controllers (طبقة التحكم)
**المسؤولية:** استقبال الطلبات وإرجاع الاستجابات

**الملفات:**
- `AuthController` - إدارة المصادقة
- `DashboardController` - لوحات التحكم
- `CourseController` - إدارة الحصص
- `AssignmentController` - إدارة الواجبات
- `ExamController` - إدارة الامتحانات
- `VideoController` - إدارة الفيديوهات
- `NotificationController` - إدارة الإشعارات

**المثال:**
```php
class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $courses = $this->courseService->getAllCourses();
        return view('courses.index', compact('courses'));
    }
}
```

### 2. Services (طبقة الأعمال)
**المسؤولية:** تنفيذ منطق الأعمال (Business Logic)

**الملفات:**
- `AuthService` - منطق المصادقة
- `DashboardService` - منطق لوحات التحكم
- `CourseService` - منطق الحصص
- `AssignmentService` - منطق الواجبات
- `ExamService` - منطق الامتحانات
- `VideoService` - منطق الفيديوهات
- `NotificationService` - منطق الإشعارات

**المثال:**
```php
class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function getAllCourses()
    {
        $user = Auth::user();
        
        if ($user->isTeacher()) {
            return $this->courseRepository->getByTeacher($user->id);
        }
        
        return $this->courseRepository->getByGrade($user->grade);
    }
}
```

### 3. Repositories (طبقة الوصول للبيانات)
**المسؤولية:** الوصول إلى قاعدة البيانات وإجراء العمليات CRUD

**الملفات:**
- `BaseRepository` - Repository أساسي
- `CourseRepository` - عمليات الحصص
- `AssignmentRepository` - عمليات الواجبات
- `ExamRepository` - عمليات الامتحانات
- `VideoRepository` - عمليات الفيديوهات
- `NotificationRepository` - عمليات الإشعارات
- `UserRepository` - عمليات المستخدمين

**المثال:**
```php
class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function getByTeacher($teacherId)
    {
        return $this->model->where('teacher_id', $teacherId)
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }
}
```

### 4. Interfaces (التعاقدات)
**المسؤولية:** تعريف التعاقدات (Contracts) للـ Repositories

**الملفات:**
- `RepositoryInterface` - Interface أساسي
- `CourseRepositoryInterface`
- `AssignmentRepositoryInterface`
- `ExamRepositoryInterface`
- `VideoRepositoryInterface`
- `NotificationRepositoryInterface`
- `UserRepositoryInterface`

**المثال:**
```php
interface CourseRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByTeacher($teacherId);
    public function getByGrade($grade);
}
```

## تدفق البيانات (Data Flow)

```
Request → Controller → Service → Repository → Model → Database
                ↓
            Response ← View
```

### مثال عملي:

1. **Request:** المستخدم يطلب قائمة الحصص
2. **Controller:** `CourseController@index` يستقبل الطلب
3. **Service:** `CourseService@getAllCourses` ينفذ منطق الأعمال
4. **Repository:** `CourseRepository@getByTeacher` يجلب البيانات
5. **Model:** `Course` يتفاعل مع قاعدة البيانات
6. **Response:** البيانات تُعاد عبر الطبقات إلى الـ View

## Dependency Injection

يتم ربط Interfaces بالـ Repositories من خلال `RepositoryServiceProvider`:

```php
// app/Providers/RepositoryServiceProvider.php
$this->app->bind(CourseRepositoryInterface::class, function ($app) {
    return new CourseRepository(new Course());
});
```

## المميزات

### 1. فصل الاهتمامات (Separation of Concerns)
- كل طبقة لها مسؤولية محددة
- سهولة الصيانة والتطوير

### 2. قابلية الاختبار (Testability)
- يمكن اختبار كل طبقة بشكل منفصل
- يمكن استبدال Repositories بـ Mocks في الاختبارات

### 3. المرونة (Flexibility)
- يمكن تغيير قاعدة البيانات دون تعديل الكود
- يمكن إضافة منطق أعمال جديد بسهولة

### 4. إعادة الاستخدام (Reusability)
- Services يمكن استخدامها في أماكن متعددة
- Repositories يمكن استخدامها من قبل Services مختلفة

## أفضل الممارسات

### 1. Controllers
- يجب أن تكون رفيعة (Thin)
- فقط استقبال الطلبات وإرجاع الاستجابات
- لا يجب أن تحتوي على منطق أعمال

### 2. Services
- تحتوي على منطق الأعمال
- يمكن أن تستخدم عدة Repositories
- يمكن أن تحتوي على التحقق من الصلاحيات

### 3. Repositories
- فقط عمليات قاعدة البيانات
- لا منطق أعمال
- يمكن إعادة استخدامها

### 4. Interfaces
- تعريف واضح للتعاقدات
- تسهيل الاختبارات
- إمكانية استبدال التنفيذ

## إضافة ميزة جديدة

لإضافة ميزة جديدة، اتبع الخطوات التالية:

1. **إنشاء Interface:**
```php
// app/Interfaces/NewFeatureRepositoryInterface.php
interface NewFeatureRepositoryInterface
{
    public function all();
    // ...
}
```

2. **إنشاء Repository:**
```php
// app/Repositories/NewFeatureRepository.php
class NewFeatureRepository extends BaseRepository implements NewFeatureRepositoryInterface
{
    // ...
}
```

3. **إنشاء Service:**
```php
// app/Services/NewFeatureService.php
class NewFeatureService
{
    protected $repository;
    
    public function __construct(NewFeatureRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    // ...
}
```

4. **إنشاء Controller:**
```php
// app/Http/Controllers/NewFeatureController.php
class NewFeatureController extends Controller
{
    protected $service;
    
    public function __construct(NewFeatureService $service)
    {
        $this->service = $service;
    }
    // ...
}
```

5. **ربط Interface بالـ Repository:**
```php
// app/Providers/RepositoryServiceProvider.php
$this->app->bind(NewFeatureRepositoryInterface::class, function ($app) {
    return new NewFeatureRepository(new NewFeature());
});
```

## الخلاصة

هذه البنية توفر:
- ✅ كود نظيف ومنظم
- ✅ سهولة الصيانة
- ✅ قابلية الاختبار
- ✅ المرونة والتوسع
- ✅ إعادة الاستخدام

