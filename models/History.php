<?php
class History extends BaseClass {
    public $expense_id;
    public $payment_id;
    public $name;
    public $amount;

    private $inner_join_company = ' = h.ExpenseId INNER JOIN companies c ON ';
    private $from_history = ' AS Name FROM paymenthistory h INNER JOIN ';
    private $types_and_users = '.CompanyId = c.CompanyId INNER JOIN users u ON u.UserId = c.UserId INNER JOIN types t ON t.TypeId = h.TypeId WHERE h.TypeId = ';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function get_all() {

        for ($i=0; $i < 3 ; $i++) {
            $this->query .= 'SELECT h.*, t.TypeName, u.UserId, u.FirstName, u.LastName, ';
            if ($i === 0) {
                $this->query .= 'b.BillName' . $this->from_history . 'bills b ON b.BillId ' . $this->inner_join_company . ' b';
            } else if ($i === 1) {
                $this->query .= 'l.LoanName' . $this->from_history . 'loans l ON l.LoanId' . $this->inner_join_company . 'l';
            } else if ($i === 2) {
                $this->query .= 's.Name' . $this->from_history . 'subscriptions s ON s.SubscriptionId' . $this->inner_join_company . 's';
            }

            $this->query .= $this->types_and_users;
            $this->query .= $i + 1;

            if ($this->user_id !== null) {
                $this->query .= ' AND u.UserId = ' . $this->user_id;
            }
            
            if ($i !== 2) {
                $this->query .= ' UNION ALL ';
            }
        }

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return $this->stmt;
    }

    public function get() {
        $this->geneate_select_query();

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->expense_id = $this->row_value('ExpenseId');
        $this->is_late = boolval($this->row_value('IsLate'));
        $this->is_paid = boolval($this->row_value('IsPaid'));
        $this->date_due = $this->format_date($this->row_value('DateDue'));
        $this->date_paid = $this->format_date($this->row_value('DatePaid'));
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
        
        if ($this->type_id !== 2) {
            $this->amount = $this->row_value('AmountDue');
        } else {
            $this->amount = $this->row_value('MonthlyAmountDue');
        }

        if ($this->type_id === 1) {
            $this->name = $this->row_value('BillName');
        } else if ($this->type_id === 2) {
            $this->name = $this->row_value('LoanName');
        } else if ($this->type_id === 3) {
            $this->name = $this->row_value('Name');
        }
    }

    public function get_type() {
        $this->query = 'SELECT TypeId FROM paymenthistory WHERE PaymentId = ' . $this->payment_id;
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->type_id = intval($this->stmt->fetchColumn());
    }

    public function history_has_access_user($decoded) {
        $this->query = 'SELECT EXISTS(';

        $this->geneate_select_query('',true);

        if (!$decoded->isAdmin) {
            $this->query .= ' AND c.UserId = ' . $this->user_id;
        }

        $this->query .= ') AS HasAccess';

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return boolval($this->stmt->fetchColumn());
    }

    public function history_exists() {
        $this->query = 'SELECT EXISTS(SELECT * FROM paymenthistory WHERE PaymentId = ' . $this->payment_id . ') AS PaymentExists';

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return boolval($this->stmt->fetchColumn());
    }

    private function geneate_select_query(bool $continue = false) {
        if (!$continue) {
            $this->query = '';
        }

        $columns = '';
        $pre_select = '';

        if ($this->type_id === 1) {
            $columns = 'b.*';
            $pre_select .= 'bills b ON b.BillId' . $this->inner_join_company .'b';
        } else if ($this->type_id === 2) {
            $columns = 'l.*';
            $pre_select .= 'loans l ON l.LoanId' . $this->inner_join_company . 'l';
        } else if ($this->type_id === 3) {
            $columns = 's.*';
            $pre_select .= 'subscriptions s ON s.SubscriptionId' . $this->inner_join_company . 's';
        }

        $this->query.= 'SELECT h.*, u.UserId, u.FirstName, u.LastName, '  . $columns . ' FROM paymenthistory h INNER JOIN ' . $pre_select;

        $this->query .= $this->types_and_users . $this->type_id . ' AND h.PaymentId = ' . $this->payment_id;;
    }
}