<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use App\Models\CurrencyHistoryReport;
use App\Jobs\GenerateCurrencyHistoryReport;

class CurrencyController extends Controller
{
    public function getCurrencies(Request $request) {
        try {
            $response = Http::withHeaders([
                'apiKey' => getenv('CURRENCY_DATA_API_KEY')
            ])->get(getenv('CURRENCY_DATA_API_URL').'/list');

            return json_decode($response->body());
        } catch (Throwable $e) {
            return response([
                'message' => 'Unable to get currencies'
            ], 500);
        }
    }

    public function convertCurrencies(Request $request) {
        try {
            $request->validate([
                'source' => 'required|string|min:3|max:3',
                'currencies' => 'required'
            ]);

            $response = Http::withHeaders([
                'apiKey' => getenv('CURRENCY_DATA_API_KEY')
            ])->get(getenv('CURRENCY_DATA_API_URL').'/live', [
                'source' => strtoupper($request->get('source')),
                'currencies' => strtoupper($request->get('currencies'))
            ]);

            return json_decode($response->body());
        } catch (Throwable $e) {
            return response([
                'message' => 'Unable to convert currencies'
            ], 500);
        }
    }

    public function generateReport(Request $request) {
        try {
            $fields = $request->validate([
                'range' => [
                    'required',
                    Rule::in(['one-year', 'six-months', 'one-month']),
                ],
                'source' => 'required|string|min:3|max:3',
                'currency' => 'required|string|min:3|max:3',
            ]);
            $user = auth('sanctum')->user();

            $historyReport = CurrencyHistoryReport::create([
                'user_id' => $user['id'],
                'range' => $fields['range'],
                'source' => strtolower($fields['source']),
                'currency' => strtolower($fields['currency'])
            ]);

            GenerateCurrencyHistoryReport::dispatch($fields, $user, $historyReport['id']);

            return response()->json(['message' => 'Successfully scheduled report generation']);
        } catch (Throwable $e) {
            return response([
                'message' => 'Unable to generate currency history report'
            ], 500);
        }
    }

    public function getReports(Request $request, $userId) {
        try {
            $reports = CurrencyHistoryReport::where('user_id', $userId)->get();
            $responseReports = [];
            foreach($reports as $report) {
                if($report['report'] != null) {
                    $responseReport = json_decode($report['report'], true);
                    $responseReport['status'] = 'completed';
                } else {
                    $responseReport['quotes'] = [];
                    $responseReport['status'] = 'processing';
                }
                $responseReports[] = [
                    'id' => $report['id'],
                    'userId' => $report['user_id'],
                    'range' => $report['range'],
                    'source' => $report['source'],
                    'currency' => $report['currency'],
                    'startDate' => $report['startDate'],
                    'endDate' => $report['endDate'],
                    'createdAt' => $report['created_at'],
                    'report' => $responseReport['quotes'],
                    'status' => $responseReport['status'],
                ];
            }
            return response()->json($responseReports, 200);
        } catch (Throwable $e) {
            return response([
                'message' => 'Unable to retrieve currency history reports for user id'.$userId
            ], 500);
        }
    }

    public function getReportId(Request $request, $userId, $reportId) {
        try {
            $report = CurrencyHistoryReport::where([['user_id', $userId], ['id', $reportId]])->get();
            return response()->json($report, 200);
        } catch (Throwable $e) {
            return response([
                'message' => 'Unable to retrieve currency history report id '.$reportId.' for user id'.$userId
            ], 500);
        }
    }
}
