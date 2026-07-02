<?php
use MVC\Controller;

class AuditLogController extends Controller
{
    private $auditLog;

    public function __construct()
    {
        parent::__construct();
        require_once MODELS . 'AuditLog.php';
        $this->auditLog = new AuditLog();
    }

    /**
     * GET /api/audit
     */
    public function index()
    {
        $this->auth(['Admin']);

        $search = $this->request->get('search') ?? '';
        $user_id = $this->request->get('user_id') ?? '';
        $log_action = $this->request->get('log_action') ?? '';
        $from_date = $this->request->get('from_date') ?? '';
        $to_date = $this->request->get('to_date') ?? '';
        $page = (int)($this->request->get('page') ?? 1);
        $per_page = 20;

        $total = (int) $this->auditLog->countAll($search, $user_id, $log_action, $from_date, $to_date);
        $logs = $this->auditLog->getAll($search, $user_id, $log_action, $from_date, $to_date, $page, $per_page);

        $conn = (new \MVC\Model())->db;
        $users = $conn->query("SELECT id, full_name, username FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll(\PDO::FETCH_ASSOC);
        $actions = $conn->query("SELECT DISTINCT action FROM audit_logs ORDER BY action")->fetchAll(\PDO::FETCH_ASSOC);

        $this->send(200, true, "Thành công", [
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'pagination' => paginate($total, $per_page, $page)
        ]);
    }

    /**
     * GET /api/audit/export
     */
    public function export()
    {
        $this->auth(['Admin']);

        $search = $this->request->get('search') ?? '';
        $user_id = $this->request->get('user_id') ?? '';
        $log_action = $this->request->get('log_action') ?? '';
        $from_date = $this->request->get('from_date') ?? '';
        $to_date = $this->request->get('to_date') ?? '';

        $logs = $this->auditLog->getAll($search, $user_id, $log_action, $from_date, $to_date, 1, 100000);

        $this->send(200, true, "Thành công", ['logs' => $logs]);
    }
}
