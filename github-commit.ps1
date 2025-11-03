# Quick commit script for GitHub (PowerShell)
# Usage: .\github-commit.ps1

Write-Host "==============================================" -ForegroundColor Cyan
Write-Host "  GoRefill - GitHub Commit Script" -ForegroundColor Cyan
Write-Host "==============================================" -ForegroundColor Cyan
Write-Host ""

# Show current status
Write-Host "📊 Current Git Status:" -ForegroundColor Yellow
git status --short
Write-Host ""

# Confirm
$confirm = Read-Host "Do you want to commit all changes? (y/n)"

if ($confirm -eq 'y' -or $confirm -eq 'Y') {
    Write-Host "✅ Staging all changes..." -ForegroundColor Green
    git add .
    
    Write-Host ""
    Write-Host "📝 Committing with message..." -ForegroundColor Green
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
    
    Write-Host ""
    Write-Host "🚀 Pushing to GitHub..." -ForegroundColor Yellow
    git push origin main
    
    Write-Host ""
    Write-Host "✅ Done! Changes pushed to GitHub." -ForegroundColor Green
} else {
    Write-Host "❌ Cancelled." -ForegroundColor Red
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
