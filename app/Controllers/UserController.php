<?php
/**
 * User Controller
 * Handles user-specific pages and features
 */

require_once __DIR__ . '/../Models/Voucher.php';
require_once __DIR__ . '/BaseController.php';

class UserController extends BaseController
{
    private $voucherModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->voucherModel = new Voucher($this->pdo);
    }
    
    /**
     * Show user vouchers page
     * GET /user/vouchers
     */
    public function vouchers()
    {
        $this->requireAuth();
        
        $userId = $_SESSION['user_id'];
        
        // Get available vouchers
        $availableVouchers = $this->voucherModel->getActive();
        
        // Get user voucher usage history
        $usageHistory = $this->voucherModel->getUserVoucherHistory($userId);
        
        $this->render('user/vouchers', [
            'title' => 'My Vouchers - GoRefill',
            'availableVouchers' => $availableVouchers,
            'usageHistory' => $usageHistory
        ]);
    }
}
