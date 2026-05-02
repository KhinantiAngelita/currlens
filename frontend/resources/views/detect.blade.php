<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Detection | YOLO + Laravel</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f5c842;
            --secondary: #f59e0b;
            --bg: #04060f;
            --card-bg: #0c0f1e;
            --text: #eef0f8;
            --text-muted: #6b7280;
            --accent: #10b981;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            background: var(--card-bg);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        h1 {
            font-family: 'Syne', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(to right, var(--primary), #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        p.subtitle {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 40px;
        }

        .upload-section {
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .upload-section:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .upload-section input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }

        .btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
            margin-top: 20px;
            font-size: 1rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .result-card {
            margin-top: 40px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 12px;
        }

        .total-badge {
            background: var(--accent);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .detection-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .detection-item:last-child {
            border-bottom: none;
        }

        .currency-name {
            font-weight: 600;
            color: #e2e8f0;
        }

        .confidence {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .nominal {
            color: var(--accent);
            font-weight: 700;
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Currency Lens</h1>
        <p class="subtitle">Upload an image to detect currency notes using YOLOv8</p>

        <form action="{{ route('predict') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="upload-section">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px; color: var(--text-muted);">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <p id="file-name">Click or drag image here</p>
                <input type="file" name="image" required onchange="document.getElementById('file-name').innerText = this.files[0].name">
            </div>
            <button type="submit" class="btn">Start Detection</button>
        </form>

        @if(isset($result))
            <div class="result-card">
                @if(isset($result['error']))
                    <div class="error">
                        <strong>Error:</strong> {{ $result['error'] }}
                    </div>
                @else
                    <div class="result-header">
                        <h2 style="margin: 0; font-size: 1.25rem;">Detection Results</h2>
                        <span class="total-badge">{{ $result['total_object'] ?? 0 }} Objects Found</span>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.875rem;">Total Nominal</p>
                        <h3 style="margin: 0; font-size: 2rem; color: var(--accent);">
                            @foreach($result['totals_by_currency'] ?? [] as $currency => $amount)
                                {{ number_format($amount) }} {{ $currency }}<br>
                            @endforeach
                            @if(empty($result['totals_by_currency']))
                                0 Detected
                            @endif
                        </h3>
                    </div>

                    <div>
                        <p style="margin-bottom: 12px; color: var(--text-muted); font-size: 0.875rem;">Detailed List</p>
                        @foreach($result['result'] ?? [] as $item)
                            <div class="detection-item">
                                <div>
                                    <div class="currency-name">{{ $item['readable'] }}</div>
                                    <div class="confidence">Confidence: {{ number_format($item['confidence'] * 100, 1) }}%</div>
                                </div>
                                <div class="nominal">{{ number_format($item['nominal']) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</body>
</html>
