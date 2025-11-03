#!/bin/bash
# Quick commit script for GitHub
# Usage: ./github-commit.sh

echo "=============================================="
echo "  GoRefill - GitHub Commit Script"
echo "=============================================="
echo ""

# Show current status
echo "📊 Current Git Status:"
git status --short
echo ""

# Confirm
read -p "Do you want to commit all changes? (y/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]
then
    echo "✅ Staging all changes..."
    git add .
    
    echo ""
    echo "📝 Committing with message..."
    git commit -m "feat: Week 4 Complete - Voucher System, Analytics Dashboard & Email Notifications

✨ New Features:
- Complete voucher/discount code system with admin management
- Analytics dashboard with Chart.js visualizations
- Sales reports with date range filtering & CSV export
- Email notifications (PHPMailer) for all user events
- 6 responsive HTML email templates

📊 Analytics: Real-time stats, charts, CSV export
📧 Emails: Welcome, order, payment, shipping, delivery
🐛 Fixes: PHP 8 null coalescing compatibility
📝 Docs: Complete README & feature documentation

Week 4 Days 17-19 complete."
    
    echo ""
    echo "🚀 Pushing to GitHub..."
    git push origin main
    
    echo ""
    echo "✅ Done! Changes pushed to GitHub."
else
    echo "❌ Cancelled."
fi
