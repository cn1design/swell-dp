<?php
/**
 * Template Name: 顧客引上げ施策
 */

?>
<!-- 顧客引上げ施策 プレミアム図解 -->
<div class="customer-elevation-premium">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        .customer-elevation-premium {
            max-width: 1400px;
            margin: 60px auto;
            padding: 0 20px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            padding: 60px 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }
        
        .customer-elevation-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.5;
            pointer-events: none;
        }
        
        .premium-content {
            position: relative;
            z-index: 1;
        }
        
        /* ヒーローセクション */
        .premium-hero {
            text-align: center;
            margin-bottom: 60px;
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .premium-hero .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 8px 24px;
            border-radius: 50px;
            color: white;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(255,255,255,0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(255,255,255,0);
            }
        }
        
        .premium-hero h1 {
            font-size: 56px;
            font-weight: 800;
            color: white;
            margin: 0 0 20px 0;
            letter-spacing: -1px;
            text-shadow: 0 4px 20px rgba(0,0,0,0.2);
            line-height: 1.2;
        }
        
        .premium-hero .hero-subtitle {
            font-size: 22px;
            color: rgba(255,255,255,0.9);
            margin: 0 0 30px 0;
            font-weight: 500;
        }
        
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 40px;
        }
        
        .stat-item {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(20px);
            padding: 20px 30px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(255,255,255,0.25);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .stat-number {
            display: block;
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
        }
        
        /* オーバービューカード */
        .overview-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }
        
        .overview-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }
        
        .overview-card h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .overview-card h2 .icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .overview-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        
        .overview-item {
            background: linear-gradient(135deg, #f6f8fb 0%, #ffffff 100%);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .overview-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
            border-color: #667eea;
        }
        
        .overview-item h3 {
            font-size: 18px;
            font-weight: 700;
            color: #667eea;
            margin: 0 0 12px 0;
        }
        
        .overview-item p {
            margin: 8px 0;
            color: #4a5568;
            font-size: 15px;
        }
        
        .start-date-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
        }
        
        /* パターンカード */
        .patterns-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .pattern-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: fadeInUp 0.8s ease-out 0.4s both;
            position: relative;
        }
        
        .pattern-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        
        .pattern-card.pattern-a {
            border-top: 5px solid #10b981;
        }
        
        .pattern-card.pattern-b {
            border-top: 5px solid #3b82f6;
        }
        
        .pattern-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .pattern-card.pattern-b .pattern-header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        
        .pattern-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .pattern-header h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
            position: relative;
            z-index: 1;
        }
        
        .pattern-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .pattern-body {
            padding: 35px;
        }
        
        .pattern-section {
            margin-bottom: 30px;
        }
        
        .pattern-section:last-child {
            margin-bottom: 0;
        }
        
        .pattern-section h4 {
            font-size: 18px;
            font-weight: 700;
            color: #1a202c;
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .pattern-section h4 .emoji {
            font-size: 20px;
        }
        
        .pattern-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .pattern-list li {
            padding: 12px 0 12px 35px;
            position: relative;
            color: #4a5568;
            font-size: 15px;
            line-height: 1.6;
        }
        
        .pattern-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            top: 12px;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }
        
        .pattern-card.pattern-b .pattern-list li::before {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        
        /* スケジュールタイムライン */
        .schedule-timeline {
            background: linear-gradient(135deg, #f6f8fb 0%, #ffffff 100%);
            border-radius: 16px;
            padding: 25px;
            margin-top: 20px;
            border: 2px solid #e2e8f0;
        }
        
        .timeline-item {
            display: flex;
            align-items: center;
            margin: 20px 0;
            position: relative;
        }
        
        .timeline-item:last-child .timeline-connector {
            display: none;
        }
        
        .timeline-marker {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            position: relative;
            z-index: 2;
            flex-shrink: 0;
        }
        
        .pattern-card.pattern-b .timeline-marker {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        
        .timeline-connector {
            position: absolute;
            left: 25px;
            top: 50px;
            width: 2px;
            height: 40px;
            background: linear-gradient(180deg, #10b981 0%, transparent 100%);
            z-index: 1;
        }
        
        .pattern-card.pattern-b .timeline-connector {
            background: linear-gradient(180deg, #3b82f6 0%, transparent 100%);
        }
        
        .timeline-content {
            flex: 1;
            margin-left: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .timeline-content:hover {
            border-color: #10b981;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
        }
        
        .pattern-card.pattern-b .timeline-content:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
        }
        
        .timeline-content strong {
            display: block;
            font-size: 16px;
            color: #1a202c;
            margin-bottom: 5px;
        }
        
        .timeline-duration {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 8px;
        }
        
        .pattern-card.pattern-b .timeline-duration {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        
        .slack-notice {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 20px;
            color: #92400e;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .slack-notice::before {
            content: '💬';
            font-size: 20px;
        }
        
        /* タイムライン例 */
        .timeline-example {
            background: white;
            border-radius: 20px;
            padding: 45px;
            margin-bottom: 50px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            animation: fadeInUp 0.8s ease-out 0.6s both;
            position: relative;
            overflow: hidden;
        }
        
        .timeline-example::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }
        
        .timeline-example h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin: 0 0 35px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .timeline-example h2 .icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .timeline-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .timeline-week {
            background: white;
            border-radius: 16px;
            padding: 25px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .timeline-week:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .timeline-week.type-a {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-color: #10b981;
        }
        
        .timeline-week.type-a:hover {
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }
        
        .timeline-week.type-b {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-color: #3b82f6;
        }
        
        .timeline-week.type-b:hover {
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        }
        
        .week-number {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .week-content {
            font-size: 18px;
            font-weight: 700;
            color: #1a202c;
        }
        
        .timeline-arrow {
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: #d1d5db;
            z-index: 10;
        }
        
        .timeline-note {
            text-align: center;
            color: #6b7280;
            font-size: 16px;
            font-style: italic;
            margin-top: 25px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        /* 重要ポイント */
        .key-points {
            background: white;
            border-radius: 20px;
            padding: 45px;
            margin-bottom: 50px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            animation: fadeInUp 0.8s ease-out 0.8s both;
            position: relative;
            overflow: hidden;
        }
        
        .key-points::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #8b5cf6 0%, #7c3aed 100%);
        }
        
        .key-points h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin: 0 0 35px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .key-points h2 .icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        
        .key-points-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .key-point-item {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            padding: 20px 25px;
            border-radius: 12px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            border: 2px solid #e9d5ff;
            transition: all 0.3s ease;
        }
        
        .key-point-item:hover {
            transform: translateX(5px);
            border-color: #8b5cf6;
            box-shadow: 0 5px 20px rgba(139, 92, 246, 0.15);
        }
        
        .key-point-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }
        
        .key-point-text {
            flex: 1;
        }
        
        .key-point-text strong {
            display: block;
            font-size: 14px;
            color: #6b21a8;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .key-point-text p {
            margin: 0;
            color: #1a202c;
            font-size: 15px;
            font-weight: 500;
        }
        
        /* 詳細フロー */
        .detailed-flow {
            margin-bottom: 50px;
            animation: fadeInUp 0.8s ease-out 1s both;
        }
        
        .flow-header {
            background: white;
            padding: 25px 35px;
            border-radius: 16px 16px 0 0;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        
        .flow-header .flow-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .flow-header.flow-b .flow-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .flow-header h3 {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin: 0;
            flex: 1;
        }
        
        .flow-body {
            background: white;
            padding: 40px;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .flow-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .flow-card {
            background: linear-gradient(135deg, #f6f8fb 0%, #ffffff 100%);
            border-radius: 16px;
            padding: 25px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .flow-card:hover {
            transform: translateY(-5px);
            border-color: #10b981;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.15);
        }
        
        .flow-b .flow-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.15);
        }
        
        .flow-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        }
        
        .flow-b .flow-card::before {
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
        }
        
        .flow-day-label {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .flow-b .flow-day-label {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .flow-card h4 {
            font-size: 17px;
            font-weight: 700;
            color: #1a202c;
            margin: 20px 0 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .flow-card h4:first-of-type {
            margin-top: 0;
        }
        
        .flow-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .flow-card li {
            padding: 8px 0 8px 28px;
            position: relative;
            color: #4a5568;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .flow-card li::before {
            content: '→';
            position: absolute;
            left: 0;
            top: 8px;
            color: #10b981;
            font-weight: 700;
            font-size: 16px;
        }
        
        .flow-b .flow-card li::before {
            color: #3b82f6;
        }
        
        .flow-note {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 3px solid #f59e0b;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 13px;
            color: #92400e;
            line-height: 1.6;
        }
        
        .flow-time-badge {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #ef4444;
            padding: 12px 18px;
            border-radius: 10px;
            text-align: center;
            margin-top: 15px;
            font-weight: 700;
            color: #991b1b;
            font-size: 14px;
        }
        
        .flow-slack-badge {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 2px solid #3b82f6;
            padding: 12px 18px;
            border-radius: 10px;
            text-align: center;
            margin-top: 15px;
        }
        
        .flow-slack-badge strong {
            display: block;
            color: #1e40af;
            font-size: 14px;
            margin-bottom: 4px;
        }
        
        .flow-slack-badge small {
            color: #3b82f6;
            font-size: 12px;
        }
        
        /* コーチノート */
        .coach-notes {
            background: white;
            border-radius: 20px;
            padding: 45px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            animation: fadeInUp 0.8s ease-out 1.2s both;
            position: relative;
            overflow: hidden;
        }
        
        .coach-notes::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
        }
        
        .coach-notes h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin: 0 0 35px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .coach-notes h2 .icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        
        .notes-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }
        
        .note-card {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #fecaca;
            border-radius: 16px;
            padding: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .note-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .note-card:hover {
            transform: translateY(-8px);
            border-color: #ef4444;
            box-shadow: 0 15px 40px rgba(239, 68, 68, 0.2);
        }
        
        .note-card h3 {
            font-size: 20px;
            font-weight: 700;
            color: #991b1b;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .note-card h3 .emoji {
            font-size: 24px;
        }
        
        .note-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .note-card li {
            padding: 12px 0 12px 32px;
            position: relative;
            color: #7f1d1d;
            font-size: 15px;
            line-height: 1.6;
            font-weight: 500;
        }
        
        .note-card li::before {
            content: '✓';
            position: absolute;
            left: 0;
            top: 12px;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }
        
        /* CTA セクション */
        .cta-section {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out 1.4s both;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
        }
        
        @keyframes moveGrid {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(50px, 50px);
            }
        }
        
        .cta-content {
            position: relative;
            z-index: 1;
        }
        
        .cta-section h2 {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin: 0 0 15px 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .cta-section p {
            font-size: 18px;
            color: rgba(255,255,255,0.8);
            margin: 0 0 30px 0;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }
        
        .cta-button.secondary {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.2);
        }
        
        .cta-button.secondary:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.4);
        }
        
        /* レスポンシブ */
        @media (max-width: 1200px) {
            .patterns-container {
                grid-template-columns: 1fr;
            }
            
            .timeline-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .notes-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 968px) {
            .customer-elevation-premium {
                padding: 40px 25px;
            }
            
            .premium-hero h1 {
                font-size: 40px;
            }
            
            .hero-stats {
                flex-direction: column;
                gap: 20px;
            }
            
            .overview-content {
                grid-template-columns: 1fr;
            }
            
            .flow-grid {
                grid-template-columns: 1fr;
            }
            
            .key-points-grid {
                grid-template-columns: 1fr;
            }
            
            .timeline-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 600px) {
            .customer-elevation-premium {
                padding: 30px 20px;
                border-radius: 16px;
            }
            
            .premium-hero h1 {
                font-size: 32px;
            }
            
            .premium-hero .hero-subtitle {
                font-size: 18px;
            }
            
            .overview-card,
            .timeline-example,
            .key-points,
            .coach-notes,
            .cta-section {
                padding: 30px 25px;
            }
            
            .pattern-header,
            .pattern-body {
                padding: 25px 20px;
            }
            
            .cta-buttons {
                flex-direction: column;
            }
            
            .cta-button {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* プリントスタイル */
        @media print {
            .customer-elevation-premium {
                background: white;
                box-shadow: none;
            }
            
            .cta-section {
                display: none;
            }
        }
    </style>

    <div class="premium-content">
        <!-- ヒーローセクション -->
        <div class="premium-hero">
            <div class="hero-badge">New Business Initiative 2024</div>
            <h1>顧客引上げ施策</h1>
            <p class="hero-subtitle">LINE友達登録者への無料添削サービス - 新たな事業展開の第一歩</p>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number">2</span>
                    <span class="stat-label">施策パターン</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">毎週</span>
                    <span class="stat-label">アプローチ頻度</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">30-60分</span>
                    <span class="stat-label">添削時間</span>
                </div>
            </div>
        </div>

        <!-- 概要カード -->
        <div class="overview-card">
            <h2>
                <span class="icon">📋</span>
                施策概要
            </h2>
            <div class="overview-content">
                <div class="overview-item">
                    <h3>実施方法</h3>
                    <p>A・Bの2パターンを隔週で交互に実施</p>
                    <p>例: 1週目A → 2週目B → 3週目A → 4週目B...</p>
                </div>
                <div class="overview-item">
                    <h3>開始スケジュール</h3>
                    <p>毎週火曜日に順次アナウンス</p>
                    <span class="start-date-badge">📅 開始: 2024年10月21日(火)〜</span>
                </div>
            </div>
        </div>

        <!-- パターンカード -->
        <div class="patterns-container">
            <!-- Aパターン -->
            <div class="pattern-card pattern-a">
                <div class="pattern-header">
                    <h3>A: Web Design×AI 無料体験</h3>
                    <span class="pattern-badge">5日間プログラム</span>
                </div>
                <div class="pattern-body">
                    <div class="pattern-section">
                        <h4><span class="emoji">📝</span>課題内容</h4>
                        <ul class="pattern-list">
                            <li>既存教材を使用したデザイン制作</li>
                            <li>AIツールを活用した実践的な学習</li>
                        </ul>
                    </div>
                    
                    <div class="pattern-section">
                        <h4><span class="emoji">✏️</span>添削サービス</h4>
                        <ul class="pattern-list">
                            <li>参加者が3日間で制作したデザインを専門的に添削</li>
                            <li>添削時間: 30分〜1時間（無料体験）</li>
                            <li>具体的な改善提案と次のステップを提示</li>
                        </ul>
                    </div>
                    
                    <div class="pattern-section">
                        <h4><span class="emoji">📅</span>スケジュール</h4>
                        <div class="schedule-timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker">①</div>
                                <div class="timeline-connector"></div>
                                <div class="timeline-content">
                                    <strong>デザイン作業期間</strong>
                                    <span class="timeline-duration">3日間</span>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker">②</div>
                                <div class="timeline-content">
                                    <strong>コーチ添削期間</strong>
                                    <span class="timeline-duration">1〜2日間（予備含む）</span>
                                </div>
                            </div>
                        </div>
                        <div class="slack-notice">
                            Slack「06_コーチ連絡網」へ完了報告をお願いします
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bパターン -->
            <div class="pattern-card pattern-b">
                <div class="pattern-header">
                    <h3>B: ポートフォリオ添削</h3>
                    <span class="pattern-badge">3日間プログラム</span>
                </div>
                <div class="pattern-body">
                    <div class="pattern-section">
                        <h4><span class="emoji">📥</span>受付内容</h4>
                        <ul class="pattern-list">
                            <li>ポートフォリオデータの受け取り</li>
                            <li>パスワード保護されたファイルにも対応</li>
                            <li>各種形式（PDF、URL、Figmaリンク等）に対応</li>
                        </ul>
                    </div>
                    
                    <div class="pattern-section">
                        <h4><span class="emoji">✏️</span>添削サービス</h4>
                        <ul class="pattern-list">
                            <li>デザイン品質の総合的な評価</li>
                            <li>構成・レイアウトの分析</li>
                            <li>色使い・タイポグラフィのフィードバック</li>
                            <li>添削時間: 30分〜1時間（無料サービス）</li>
                        </ul>
                    </div>
                    
                    <div class="pattern-section">
                        <h4><span class="emoji">📅</span>スケジュール</h4>
                        <div class="schedule-timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker">①</div>
                                <div class="timeline-connector"></div>
                                <div class="timeline-content">
                                    <strong>データ受け取り期間</strong>
                                    <span class="timeline-duration">1日間</span>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker">②</div>
                                <div class="timeline-content">
                                    <strong>コーチ添削期間</strong>
                                    <span class="timeline-duration">1〜2日間（予備含む）</span>
                                </div>
                            </div>
                        </div>
                        <div class="slack-notice">
                            Slack「06_コーチ連絡網」へ完了報告をお願いします
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- タイムライン例 -->
        <div class="timeline-example">
            <h2>
                <span class="icon">📆</span>
                実施スケジュール例
            </h2>
            <div class="timeline-grid">
                <div class="timeline-week type-a">
                    <span class="week-number">1週目</span>
                    <div class="week-content">A: デザイン添削</div>
                </div>
                <div class="timeline-week type-b">
                    <span class="week-number">2週目</span>
                    <div class="week-content">B: PF添削</div>
                </div>
                <div class="timeline-week type-a">
                    <span class="week-number">3週目</span>
                    <div class="week-content">A: デザイン添削</div>
                </div>
                <div class="timeline-week type-b">
                    <span class="week-number">4週目</span>
                    <div class="week-content">B: PF添削</div>
                </div>
            </div>
            <p class="timeline-note">以降、A・Bのパターンを隔週で継続的に実施していきます</p>
        </div>

        <!-- 重要ポイント -->
        <div class="key-points">
            <h2>
                <span class="icon">⚠️</span>
                重要ポイント
            </h2>
            <div class="key-points-grid">
                <div class="key-point-item">
                    <div class="key-point-icon">👥</div>
                    <div class="key-point-text">
                        <strong>対象者</strong>
                        <p>グロコミLINE友達登録者</p>
                    </div>
                </div>
                <div class="key-point-item">
                    <div class="key-point-icon">🎯</div>
                    <div class="key-point-text">
                        <strong>目的</strong>
                        <p>受講確度の向上と顧客エンゲージメント強化</p>
                    </div>
                </div>
                <div class="key-point-item">
                    <div class="key-point-icon">⏱️</div>
                    <div class="key-point-text">
                        <strong>添削時間</strong>
                        <p>無料サービスのため30分〜1時間を基本とする</p>
                    </div>
                </div>
                <div class="key-point-item">
                    <div class="key-point-icon">💬</div>
                    <div class="key-point-text">
                        <strong>報告先</strong>
                        <p>Slack「06_コーチ連絡網」チャンネル</p>
                    </div>
                </div>
                <div class="key-point-item">
                    <div class="key-point-icon">📅</div>
                    <div class="key-point-text">
                        <strong>開始日</strong>
                        <p>2024年10月21日(火)〜 毎週実施</p>
                    </div>
                </div>
                <div class="key-point-item">
                    <div class="key-point-icon">🔄</div>
                    <div class="key-point-text">
                        <strong>実施頻度</strong>
                        <p>毎週継続的にアプローチを実施</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- A: 詳細フロー -->
        <div class="detailed-flow">
            <div class="flow-header">
                <div class="flow-icon">🎨</div>
                <h3>A: Web Design×AI 無料体験 - 詳細フロー</h3>
            </div>
            <div class="flow-body">
                <div class="flow-grid">
                    <div class="flow-card">
                        <div class="flow-day-label">Day 1</div>
                        <h4>📢 課題配信</h4>
                        <ul>
                            <li>参加者へ教材を送付</li>
                            <li>デザイン制作開始の案内</li>
                            <li>AIツールの使用方法を説明</li>
                            <li>提出期限の明示</li>
                        </ul>
                    </div>
                    
                    <div class="flow-card">
                        <div class="flow-day-label">Day 2-3</div>
                        <h4>👤 参加者制作期間</h4>
                        <ul>
                            <li>AIツールを活用してデザイン制作</li>
                            <li>教材に沿った実践的な学習</li>
                            <li>3日間でデザインを完成させる</li>
                        </ul>
                        <div class="flow-note">
                            💡 期間中の質問対応も随時行います
                        </div>
                    </div>
                    
                    <div class="flow-card">
                        <div class="flow-day-label">Day 4</div>
                        <h4>📥 データ受取</h4>
                        <ul>
                            <li>参加者から作品を受領</li>
                            <li>ファイル形式の確認</li>
                            <li>閲覧可能かチェック</li>
                        </ul>
                        <h4>✏️ コーチ添削</h4>
                        <ul>
                            <li>デザインの総合評価</li>
                            <li>具体的な改善点の指摘</li>
                            <li>良い点のフィードバック</li>
                            <li>次のステップの提案</li>
                        </ul>
                        <div class="flow-time-badge">
                            ⏱️ 添削時間: 30分〜1時間
                        </div>
                    </div>
                    
                    <div class="flow-card">
                        <div class="flow-day-label">Day 5</div>
                        <h4>📤 フィードバック返却</h4>
                        <ul>
                            <li>添削結果を参加者へ送付</li>
                            <li>次のステップを案内</li>
                            <li>受講プランの提案</li>
                        </ul>
                        <h4>📊 完了報告</h4>
                        <div class="flow-slack-badge">
                            <strong>💬 Slack報告</strong>
                            <small>06_コーチ連絡網チャンネルへ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- B: 詳細フロー -->
        <div class="detailed-flow flow-b">
            <div class="flow-header flow-b">
                <div class="flow-icon">📁</div>
                <h3>B: ポートフォリオ添削 - 詳細フロー</h3>
            </div>
            <div class="flow-body">
                <div class="flow-grid">
                    <div class="flow-card">
                        <div class="flow-day-label">Day 1</div>
                        <h4>📥 受付作業</h4>
                        <ul>
                            <li>参加者からポートフォリオを受領</li>
                            <li>パスワードの確認と動作テスト</li>
                            <li>ファイル形式のチェック（PDF/URL/Figma等）</li>
                            <li>閲覧可能か確認</li>
                        </ul>
                        <div class="flow-note">
                            <strong>確認項目:</strong><br>
                            ✓ PDF/URL/Figmaリンク等の形式<br>
                            ✓ パスワード保護の動作確認<br>
                            ✓ 不備があれば即座に連絡
                        </div>
                    </div>
                    
                    <div class="flow-card">
                        <div class="flow-day-label">Day 2</div>
                        <h4>✏️ コーチ添削</h4>
                        <ul>
                            <li>デザイン品質の総合評価</li>
                            <li>構成・レイアウトの分析</li>
                            <li>色使い・タイポグラフィの評価</li>
                            <li>ユーザビリティのチェック</li>
                            <li>強みと改善点の明確化</li>
                            <li>プロフェッショナルな視点でのアドバイス</li>
                        </ul>
                        <div class="flow-time-badge">
                            ⏱️ 添削時間目安: 30分〜1時間
                        </div>
                    </div>
                    
                    <div class="flow-card">
                        <div class="flow-day-label">Day 3</div>
                        <h4>📤 返却作業</h4>
                        <ul>
                            <li>添削結果をまとめる</li>
                            <li>参加者へフィードバックを送付</li>
                            <li>具体的な改善提案を提示</li>
                            <li>受講案内とネクストステップ</li>
                        </ul>
                        <h4>📊 完了報告</h4>
                        <div class="flow-slack-badge">
                            <strong>💬 Slack報告</strong>
                            <small>06_コーチ連絡網チャンネルへ投稿</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- コーチへの注意事項 -->
        <div class="coach-notes">
            <h2>
                <span class="icon">⚠️</span>
                コーチの皆様へ - 重要な注意事項
            </h2>
            <div class="notes-grid">
                <div class="note-card">
                    <h3><span class="emoji">🎯</span>添削のポイント</h3>
                    <ul>
                        <li>具体的で実行可能な改善提案を行う</li>
                        <li>良い点を必ず見つけて伝える</li>
                        <li>励ましの言葉を添えてモチベーション向上</li>
                        <li>次のステップを明確に示す</li>
                        <li>プロフェッショナルな視点を提供</li>
                    </ul>
                </div>
                
                <div class="note-card">
                    <h3><span class="emoji">⏰</span>時間管理</h3>
                    <ul>
                        <li>無料体験のため最大1時間厳守</li>
                        <li>効率的かつ丁寧に添削</li>
                        <li>期限を必ず守る</li>
                        <li>予備日を賢く活用</li>
                        <li>タイムマネジメントを徹底</li>
                    </ul>
                </div>
                
                <div class="note-card">
                    <h3><span class="emoji">📢</span>報告必須</h3>
                    <ul>
                        <li><strong>Slack報告先:</strong><br>06_コーチ連絡網チャンネル</li>
                        <li>完了報告を必ず行う</li>
                        <li>所要時間を記録</li>
                        <li>特記事項があれば共有</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- CTAセクション -->
        <div class="cta-section">
            <div class="cta-content">
                <h2>新たな事業展開の第一歩を共に</h2>
                <p>この施策は、私たちの事業成長における重要なマイルストーンです。<br>全スタッフの協力により、より多くの受講生に価値を届けていきましょう。</p>
                <div class="cta-buttons">
                    <a href="#" class="cta-button">
                        <span>📋</span>
                        詳細資料をダウンロード
                    </a>
                    <a href="#" class="cta-button secondary">
                        <span>💬</span>
                        質問・相談はSlackへ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /顧客引上げ施策 プレミアム図解 -->