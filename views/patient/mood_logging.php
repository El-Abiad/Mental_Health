<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الحالة المزاجية - منصة الصحة النفسية</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        select,
        textarea {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .mood-options {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .mood-option {
            flex: 1;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .mood-option:hover {
            border-color: #667eea;
            background-color: #f0f4ff;
        }

        .mood-option.selected {
            border-color: #667eea;
            background-color: #667eea;
            color: white;
        }

        .mood-emoji {
            font-size: 24px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>تسجيل الحالة المزاجية</h1>

        <form method="POST">
            <label for="mood">اختر حالتك المزاجية الحالية:</label>
            <div class="mood-options">
                <div class="mood-option" data-mood="happy">
                    <div class="mood-emoji">😊</div>
                    <div>سعيد</div>
                </div>
                <div class="mood-option" data-mood="sad">
                    <div class="mood-emoji">😢</div>
                    <div>حزين</div>
                </div>
                <div class="mood-option" data-mood="anxious">
                    <div class="mood-emoji">😰</div>
                    <div>قلق</div>
                </div>
                <div class="mood-option" data-mood="calm">
                    <div class="mood-emoji">😌</div>
                    <div>هادئ</div>
                </div>
                <div class="mood-option" data-mood="angry">
                    <div class="mood-emoji">😠</div>
                    <div>غاضب</div>
                </div>
                <div class="mood-option" data-mood="tired">
                    <div class="mood-emoji">😴</div>
                    <div>مرهق</div>
                </div>
            </div>

            <input type="hidden" name="mood" id="selected-mood" value="happy">

            <label for="intensity">شدة الحالة المزاجية (1-10):</label>
            <select name="intensity" id="intensity">
                <option value="1">1 - خفيف جداً</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5" selected>5 - متوسط</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10 - شديد جداً</option>
            </select>

            <label for="notes">ملاحظات إضافية (اختياري):</label>
            <textarea name="notes" id="notes" rows="4" placeholder="اكتب أي تفاصيل إضافية عن حالتك المزاجية..."></textarea>

            <label for="triggers">العوامل المؤثرة (اختياري):</label>
            <textarea name="triggers" id="triggers" rows="3" placeholder="ما الذي أثر على مزاجك اليوم؟"></textarea>

            <button type="submit">حفظ تسجيل الحالة المزاجية</button>
        </form>

        <div class="back-link">
            <a href="/test_all_pages.php">← العودة لمختبر الصفحات</a>
        </div>
    </div>

    <script>
        // Interactive mood selection
        const moodOptions = document.querySelectorAll('.mood-option');
        const selectedMoodInput = document.getElementById('selected-mood');

        moodOptions.forEach(option => {
            option.addEventListener('click', function() {
                moodOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                // Update hidden input
                selectedMoodInput.value = this.dataset.mood;
            });
        });

        // Set default selection
        document.querySelector('.mood-option[data-mood="happy"]').classList.add('selected');
    </script>
</body>

</html>