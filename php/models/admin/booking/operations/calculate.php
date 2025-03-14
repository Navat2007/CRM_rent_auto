<?php
class OperationsCalculate
{
    private \PgSql\Connection|null|false $conn = null;

    function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function calculate($booking_id): void
    {
        $sql = "
        SELECT 
            baap.*, dot.used_for
        FROM 
            booking_accruals_and_payments as baap
        INNER JOIN directory_operation_types as dot ON baap.directory_operation_types_id = dot.id
        WHERE 
            baap.booking_id = '$booking_id'";
        $result = pg_query($this->conn, $sql);

        if(pg_num_rows($result) > 0)
        {
            $accrued_total = 0;
            $paid_total = 0;
            $deposit_booking_total = 0;
            $balance = 0;

            while ($row = pg_fetch_object($result))
            {
                //accrued_total
                if($row->used_for != "pay_deposit" && $row->used_for != "return_deposit"){
                    if((int)$row->is_income == 1){
                        $accrued_total += (float)$row->accrued;
                    }

                    if((int)$row->is_income == 0){
                        $accrued_total -= (float)$row->accrued;
                    }
                }

                //paid_total
                if($row->used_for != "pay_deposit" && $row->used_for != "return_deposit"){
                    if((int)$row->is_income == 1){
                        $paid_total += (float)$row->paid;
                    }

                    if((int)$row->is_income == 0){
                        $paid_total -= (float)$row->paid;
                    }
                }

                //deposit_booking_total
                if($row->used_for == "pay_deposit"){
                    $deposit_booking_total += (float)$row->paid;
                    $balance += (float)$row->paid - (float)$row->accrued;
                }

                if($row->used_for == "return_deposit"){
                    $deposit_booking_total -= (float)$row->paid;
                    $balance += (float)$row->accrued - (float)$row->paid;
                }
            }

            //balance
            $balance = $balance + ($paid_total - $accrued_total);

            $sql = "
            UPDATE 
                booking
            SET 
                accrued_total = '$accrued_total',
                paid_total = '$paid_total',
                deposit_booking_total = '$deposit_booking_total',
                balance = '$balance',
                last_user_id = '-1'
            WHERE 
                id = '$booking_id'";
            pg_query($this->conn, $sql);

            pg_free_result($result);
        }
    }
}
