<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlyTransactionReportRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function monthlyTransactionReport(MonthlyTransactionReportRequest $request)
    {
        try {
            
            $user_id = $request->user()->id;
            $user_type = $request->user()->user_type;

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $result = DB::select("
                WITH RECURSIVE MonthList AS (
                    SELECT CAST('" . $startDate . "' AS DATE) AS IDate
                    UNION ALL
                    SELECT DATE_ADD(IDate, INTERVAL 1 MONTH)
                    FROM MonthList
                    WHERE IDate < DATE('" . $endDate . "')
                )
                SELECT `month`,
                `year`,
                SUM(paid) AS paid,
                SUM(outstanding) outstanding,
                SUM(overdue) overdue
                FROM(
                SELECT t.payable_amount, p.amount, MONTH(m.IDate) AS `month`, YEAR(m.IDate) AS `year`,
                IF( DATE(DATE_FORMAT(MAX(p.paid_on), '%Y-%m-%01')) >= DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01')), CASE WHEN t.payable_amount <= SUM(IF((DATE(DATE_FORMAT(p.paid_on, '%Y-%m-%01')) <= DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01'))), p.amount, 0)) AND MAX(DATE(p.paid_on)) THEN SUM(IF((DATE(DATE_FORMAT(p.paid_on, '%Y-%m-%01')) <= DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01'))), p.amount, 0)) ELSE 0 END, 0) AS paid,
                CASE WHEN t.payable_amount > SUM(IF((DATE(DATE_FORMAT(p.paid_on, '%Y-%m-%01')) <= DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01'))), p.amount, 0)) AND DATE(DATE_FORMAT(t.due_on, '%Y-%m-%01')) >= DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01')) THEN t.payable_amount - SUM(IF((DATE(DATE_FORMAT(p.paid_on, '%Y-%m-%01')) <= DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01'))), p.amount, 0)) ELSE 0 END AS outstanding,
                CASE WHEN t.payable_amount > SUM(IF((DATE(DATE_FORMAT(p.paid_on, '%Y-%m-%01')) <= DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01'))), p.amount, 0)) AND DATE(DATE_FORMAT(t.due_on, '%Y-%m-%01')) < DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01')) THEN t.payable_amount - SUM(IF((DATE(DATE_FORMAT(p.paid_on, '%Y-%m-%01')) <= DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01'))), p.amount, 0)) ELSE 0 END AS overdue
                FROM MonthList m
                LEFT JOIN `transactions` t ON (DATE(DATE_FORMAT(t.created_at, '%Y-%m-%01')) <= DATE(DATE_FORMAT(m.IDate, '%Y-%m-%01'))) ".($user_type == 'CUSTOMER' ? "t.user_id = ". $user_id : "") . "
                LEFT JOIN payments p ON t.id = p.transaction_id
                GROUP BY `year`, `month`,t.id
                HAVING payable_amount >= SUM(IFNULL(p.amount,0))
                ORDER BY `year`, `month`) AS mtable
                GROUP BY `year`, `month`
            ");

            return response()->json(['reports' => $result]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
