# توثيق شرح النظام والكود - مشروع Clinic (CalmSpace)

## 1) نظرة عامة سريعة

هذا المشروع عبارة عن تطبيق **PHP Monolith** بسيط، معمارية قريبة من **MVC**:

- **Models**: تتعامل مع قاعدة البيانات ومنطق الدومين.
- **Controllers**: تستقبل الطلب، تتحقق من الصلاحيات، تستدعي الموديل، وتختار الـ view.
- **Views**: صفحات PHP/HTML (Server-Rendered) مع Bootstrap.

لا يوجد Framework مثل Laravel؛ النظام مبني يدويًا باستخدام ملفات `*_run.php` للتوجيه.

---

## 2) بنية المجلدات ووظيفة كل جزء

- `index.php`: نقطة دخول التطبيق (يفتح شاشة الدخول).
- `config/`:
  - `db.php`: إعداد الاتصال بقاعدة البيانات + Singleton.
  - `encryption.php`: دوال تشفير/التحقق من كلمات المرور.
- `core/`:
  - `BaseController.php`: أساس مشترك للكنترولرز (DB + view + redirect + auth helpers).
- `controllers/`:
  - `auth_run.php`, `patient_run.php`, `therapist_run.php`, `manager_run.php`, `admin_run.php`, `schedule_run.php`, `session_run.php`.
  - كنترولرز الأعمال: `AuthController`, `PatientController`, `TherapistController`, `ManagerController`, `ScheduleController`, `SessionController`, `AdminController`.
- `models/`: `User`, `Patient`, `Therapist`, `Note`, `Schedule`, `Session`, `Admin`.
- `views/`:
  - `shared/header.php`, `shared/footer.php` (Layout مشترك).
  - صفحات حسب الدور: `auth`, `patient`, `therapist`, `manager`, `admin`.
- `assets/style.css`: تنسيقات الواجهة.

---

## 3) رحلة الطلب (Request Lifecycle) من أول ما المستخدم يفتح الموقع

1. المستخدم يفتح `/clinic/` -> يدخل `index.php`.
2. `index.php` يعمل `session_start()` ويحمّل الـ config/core/model/controller الأساسي.
3. ينشئ `AuthController` ويستدعي `login()`.
4. إذا المستخدم already logged in يتم تحويله Dashboard حسب الدور.
5. إذا غير مسجل يدخل صفحة `views/auth/login.php`.

بعد تسجيل الدخول، التنقل يكون غالبًا عبر:

- `/controllers/auth_run.php?action=...`
- `/controllers/patient_run.php?action=...`
- `/controllers/therapist_run.php?action=...`
- `/controllers/manager_run.php?action=...`
- `/controllers/admin_run.php?action=...`

كل ملف `*_run.php` يقوم ب:

- التأكد من Session + Role.
- قراءة قيمة `action` من query string.
- عمل dispatch للميثود المناسبة داخل الكنترولر.

---

## 4) شرح الملفات الأساسية (حتة حتة)

## 4.1 `config/db.php`

### ماذا يفعل؟

- يعرّف ثوابت الاتصال بقاعدة البيانات (`DB_HOST`, `DB_USER`, ...).
- ينشئ كلاس `Database` بنمط **Singleton**.
- يوفر:
  - `getInstance()` لإرجاع نفس instance دائمًا.
  - `getConnection()` لإرجاع اتصال `mysqli`.

### لماذا مهم؟

- يمنع فتح اتصالات كثيرة غير لازمة.
- يجعل الوصول للاتصال موحدًا في النظام.

---

## 4.2 `config/encryption.php`

### ماذا يفعل؟

- `hashPassword()` باستخدام `password_hash(..., PASSWORD_BCRYPT)`.
- `verifyPassword()` باستخدام `password_verify()`.

### استخدامه في النظام

- عند التسجيل: يتم Hash.
- عند تسجيل الدخول: يتم Verify.

---

## 4.3 `core/BaseController.php`

### الهدف

Base class لكل الكنترولرز تقريبًا.

### أهم الميثودز

- `__construct()`: يجهز `$this->db`.
- `view($path, $data)`: يعرض view مع بيانات.
- `redirect($url)`: تحويل HTTP.
- `isLoggedIn()`: فحص Session.
- `requireLogin()`: إجبار تسجيل الدخول.
- `requireRole($role)`: إجبار دور معين.

### لماذا مهم؟

يوحد سلوك الأمان والتوجيه بدل كتابة نفس الكود في كل Controller.

---

## 4.4 `controllers/auth_run.php` و `AuthController.php`

## `auth_run.php`

Router بسيط يقرأ `action`:

- `login`
- `loginPost`
- `register`
- `registerPost`
- `logout`

## `AuthController`

- `login()`: يعرض شاشة الدخول أو يحول حسب الدور.
- `loginPost()`: يتحقق من المدخلات، يجلب المستخدم بالإيميل، يتحقق من الباسورد، يخزن Session.
- `registerPost()`: يتحقق من المدخلات ثم ينشئ مستخدم.
- `logout()`: ينهي الجلسة.
- `redirectByRole()`: يوجه المستخدم للوحة المناسبة حسب role.

---

## 4.5 `models/User.php`

### مسؤولياته

- البحث عن مستخدم (`findByEmail`, `findById`).
- جلب دور المستخدم (`getRole`).
- إنشاء حساب جديد (`create`).
- جلب كل المستخدمين (`getAll`).

### نقطة مهمة

`create()` يستخدم transaction:

- INSERT في `Users`.
- INSERT في `UserRoles`.
- INSERT profile مناسب حسب الدور (Patient/Therapist/Admin).
- Commit أو Rollback.

---

## 4.6 طبقة الـ Patient

## `controllers/patient_run.php`

- يتأكد أن role = `patient`.
- يقرأ `action` وينادي:
  - `dashboard`, `intakeForm`, `agreements`, `sessions`, `paySession`, `favorites`, `logMood`, `emergency`.

## `controllers/PatientController.php`

### أهم الفلوهات:

- `dashboard($userId)`:
  - يتأكد الصلاحية.
  - يجلب profile المريض.
  - يجلب الجلسات القادمة + mood logs + المعالج المعين + ملاحظات المعالج.
  - يعرض `views/patient/dashboard.php`.

- `intakeForm()`:
  - GET: عرض الفورم.
  - POST: حفظ التاريخ الطبي.

- `agreements()`:
  - GET/POST لتوقيع الاتفاقية.

- `sessions()`:
  - عرض جلسات المريض.

- `paySession()`:
  - دفع Session عبر PaymentId.

- `favorites()`:
  - إضافة/عرض المعالجين المفضلين.

- `logMood()`:
  - تسجيل مزاج يومي.

- `emergency()`:
  - عند وجود `trigger` ينشئ Crisis Alert.

## `models/Patient.php`

يحتوي منطق بيانات المريض:

- profile (`getPatientByUserId`).
- intake (`completeIntakeForm`).
- agreements (`signAgreement`).
- favorites (`addFavorite`, `getFavorites`) مع fallback لو جدول favorites اسمه مختلف.
- sessions/payment (`getUpcomingSessions`, `payForSession`).
- mood (`getMoodLogs`, `addMoodLog`).
- therapist linkage (`getAssignedTherapist`, `getRecentTherapistNotes`).
- crisis (`triggerCrisis`).

---

## 4.7 طبقة الـ Therapist

## `controllers/therapist_run.php`

- يتأكد أن role = `therapist`.
- يعمل dispatch على:
  - dashboard
  - moodReports/moodReport
  - availability
  - session/startSession/endSession
  - notes/saveNote
  - profile
  - patients
  - sendManagerNote

## `controllers/TherapistController.php`

### أهم الفلوهات:

- `dashboard()`: appointments + today sessions + weekly mood reports.
- `availability()`: تحديث availability + snooze + notification للمرضى.
- `viewSession()`: فتح الجلسة وجلب الملاحظات.
- `startSession()/endSession()`: تغيير حالة الجلسة.
- `notes()/saveNote()`: كتابة/تعديل clinical notes، وفحص كلمات الأزمة.
- `profile()`: تحديث بيانات المعالج.
- `patients()`: قائمة المرضى + shared journals.
- `sendManagerNote()`: إرسال ملاحظة للمديرين.

## `models/Therapist.php`

يركز على:

- profile/availability.
- appointments/sessions.
- mood summaries.
- إدارة حالة الجلسة live/completed.
- crisis alerts من session.
- بيانات المرضى المرتبطين بالمعالج.

## `models/Note.php`

- يحدد اسم جدول الملاحظات تلقائيًا (`ClinicalNote` أو بدائل).
- `create`, `getByTherapist`, `getBySession`, `update`.
- يتعامل مع versioning للملاحظة.

---

## 4.8 طبقة الـ Manager

## `controllers/manager_run.php`

- يتأكد role = `manager`.
- يسمح فقط actions محددة:
  - dashboard, assignTherapist, verifyTherapists, verifyIntakeForms, reports, crisisAlerts, notes.

## `controllers/ManagerController.php`

### أهم الفلوهات:

- `dashboard()`:
  - stats + upcoming + cancelled + unverified licenses.
- `assignTherapist()`:
  - إنشاء Appointment جديد بين patient وtherapist.
- `verifyTherapists()`:
  - verify/renew/revoke license.
- `verifyIntakeForms()`:
  - مراجعة intake forms والتحقق منها.
- `reports()`:
  - weekly mood reports + open crisis alerts.
- `crisisAlerts()`:
  - عرض التنبيهات المفتوحة وإمكانية resolve.
- `notes()`:
  - ملاحظات قادمة من admin/therapist.

### ملاحظة معمارية

الـ ManagerController يجمع مهام كثيرة ومتعددة (scheduling + reporting + licensing)، لذلك هو “ثقيل”.

---

## 4.9 Scheduling & Sessions (Manager side)

## `controllers/schedule_run.php` + `ScheduleController.php`

- مسؤول عن:
  - عرض كل المواعيد.
  - إنشاء موعد جديد.
  - إلغاء موعد مع تطبيق غرامة متأخرة عند الحاجة.

## `models/Schedule.php`

- `getAllAppointments`, `getUpcomingAppointments`.
- `isDoubleBooked` لمنع تضارب المواعيد.
- `createAppointment`:
  - ينشئ Appointment + Session + Payment.
- `cancelAppointment`:
  - يغير Appointment/Session/Payment ويحدد سياسة refund/fine.
- `getAllTherapists`, `getAllPatients`.

## `controllers/session_run.php` + `SessionController.php`

- إدارة lifecycle للجلسة من جهة المدير:
  - list/start/end/cancelled/refund/applyFine.

## `models/Session.php`

- قراءة sessions/cancelled/stats.
- start/end session.
- refund/fine في payment.

---

## 4.10 طبقة Admin

## `controllers/admin_run.php`

- يتحقق من دور admin.
- routes:
  - `dashboard`
  - `users`
  - `updateUser`
  - `sendManagerNote` (POST) لإرسال Broadcast للمديرين.

## `controllers/AdminController.php`

Wrapper بسيط فوق `models/Admin.php` (طرق static).

## `models/Admin.php`

عمليات إدارية:

- roles/users management.
- delete/update user.
- violation reports.
- warning/ban/activate.
- intake verification.
- weekly mood reports.
- open crisis alert count.

---

## 4.11 واجهة المستخدم (Views) وكيف ترتبط بالكنترولرز

## `views/shared/header.php`

- يحمل CSS وBootstrap.
- navbar ديناميكي حسب `$_SESSION['role']`.
- links كلها ترجع إلى `*_run.php?action=...`.
- يحتوي modal للأدمن لإرسال ملاحظة للمديرين.

## `views/shared/footer.php`

- يغلق `main`.
- footer بسيط + Bootstrap JS.

## أمثلة صفحات

- `views/auth/login.php`: فورم login إلى `auth_run.php?action=loginPost`.
- `views/patient/dashboard.php`: يعرض sessions/therapist/mood/notes.
- `views/therapist/dashboard.php`: appointments + today sessions + mood summary.
- `views/manager/dashboard.php`: KPIs + اختصارات workflow.
- `views/admin/dashboard.php`: لوحة بسيطة مع feedback messages.

---

## 5) كيف السيستم “ماشي” من منظور الأعمال (Business Flow)

## 5.1 Flow التسجيل والدخول

1. مستخدم يسجل من `register`.
2. `User::create` ينشئ user + role + profile.
3. يسجل دخول من `loginPost`.
4. يتم redirect حسب role.

## 5.2 Flow حجز جلسة (Manager)

1. Manager يدخل Assign Therapist.
2. يختار patient + therapist + datetime.
3. `Schedule::createAppointment`:
   - يتحقق من double booking.
   - ينشئ Appointment.
   - ينشئ Session.
   - ينشئ Payment.

## 5.3 Flow الجلسة (Therapist / Manager)

1. قبل البدء: الحالة `pending/scheduled`.
2. عند start: تتحول `live`.
3. عند end: تتحول `completed`.
4. المدفوعات تتحدث حسب الحالة.

## 5.4 Flow المرضى

- المريض يعبي intake.
- يوقع agreement.
- يتابع الجلسات.
- يسجل mood logs.
- يقدر يعمل emergency alert.

## 5.5 Flow إدارة المخاطر

- therapist notes فيها keywords خطرة -> ينشأ `CrisisAlert`.
- manager يراجع alerts ويعمل resolve.

---

## 6) Design Patterns المطبقة حاليًا

- **MVC (مبسّط)**.
- **Singleton** في `Database`.
- **Front Controller/Action Router (بشكل يدوي)** عبر `*_run.php`.
- **Template Composition** عبر `header.php` و`footer.php`.

---

## 7) ملاحظات مهمة أثناء قراءة الكود

- بعض الـ Models تورث من `BaseController` (هذا coupling بين الطبقات، غير مثالي MVC).
- بعض منطق SQL موجود داخل controllers مباشرة (أفضل نقله للموديلات/خدمات).
- الاتساق في paths والـ redirects غير موحد 100%.
- لا يوجد test suite رسمي داخل المشروع.

---

## 8) اقتراح ترتيب تعلّم الكود (لو هتكمّل تطوير)

1. ابدأ بـ `index.php` + `auth_run.php` + `AuthController`.
2. افهم `BaseController`.
3. تتبع flow واحد كامل (مثلا patient dashboard).
4. تتبع flow إداري (assign therapist).
5. ثم اقرأ Models: `User`, `Patient`, `Schedule`, `Session`, `Therapist`, `Note`, `Admin`.
6. أخيرًا راجع views لكل role.

---

## 9) خلاصة

النظام واضح وقابل للفهم بسرعة، ومبني يدويًا بشكل عملي.  
أقوى جزء فيه هو وضوح الـ role-based workflows، وأكبر مساحة تحسين هي الفصل المعماري الأكثر صرامة بين Controller وModel وإضافة اختبارات.

