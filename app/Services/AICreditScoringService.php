<?php

namespace App\Services;

use App\Models\Loan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AICreditScoringService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1/chat/completions');
    }

    public function assessLoan(Loan $loan): array
    {
        try {
            $borrowerData = $this->prepareBorrowerData($loan);

            $riskAssessment = $this->analyzeWithAI($borrowerData);
            $creditScore = $this->calculateCreditScore($riskAssessment);
            $defaultProbability = $this->calculateDefaultProbability($riskAssessment);

            return [
                'success' => true,
                'credit_score' => $creditScore,
                'default_probability' => $defaultProbability,
                'risk_factors' => $riskAssessment['risk_factors'],
                'recommendation' => $this->getRecommendation($creditScore, $defaultProbability),
                'decision_reason' => $riskAssessment['analysis'],
            ];
        } catch (\Exception $e) {
            Log::error('AI Credit Scoring Failed for Loan ' . $loan->id . ': ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'credit_score' => null,
                'default_probability' => null,
                'risk_factors' => [],
                'recommendation' => 'REVIEW',
                'decision_reason' => 'AI assessment failed - requires manual review',
            ];
        }
    }

    private function prepareBorrowerData(Loan $loan): array
    {
        $borrower = $loan->borrower;

        return [
            'loan_details' => [
                'principal_amount' => $loan->principal_amount,
                'loan_duration' => $loan->loan_duration,
                'interest_rate' => $loan->interest_rate,
                'loan_type' => $loan->loan_type->loan_name ?? 'Unknown',
            ],
            'borrower_financials' => [
                'monthly_income' => $loan->borrower_monthly_income,
                'employment_stability' => $loan->borrower_employment_months,
                'existing_debts' => $loan->borrower_existing_debts,
                'credit_history_length' => $loan->borrower_credit_history_months,
                'previous_defaults' => $loan->borrower_previous_defaults,
            ],
            'financial_ratios' => [
                'debt_to_income' => $loan->borrower_existing_debts / max($loan->borrower_monthly_income, 1),
                'loan_to_income' => $loan->principal_amount / max($loan->borrower_monthly_income * 12, 1),
            ]
        ];
    }

    private function analyzeWithAI(array $applicantData): array
    {
        $prompt = $this->buildRiskAssessmentPrompt($applicantData);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl, [
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are a financial risk assessment expert. Return ONLY valid JSON without any additional text."
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.1,
            'max_tokens' => 500
        ]);

        if (!$response->successful()) {
            throw new \Exception('AI API request failed: ' . $response->body());
        }

        $content = $response->json()['choices'][0]['message']['content'];

        // Extract JSON from response
        $jsonData = $this->extractJson($content);

        if (!$jsonData) {
            throw new \Exception('Failed to parse AI response: ' . $content);
        }

        return $jsonData;
    }

    private function extractJson(string $content): ?array
    {
        // Try to find JSON between first { and last }
        $start = strpos($content, '{');
        $end = strrpos($content, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $jsonString = substr($content, $start, $end - $start + 1);
            $data = json_decode($jsonString, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }

        return null;
    }

    private function buildRiskAssessmentPrompt(array $data): string
    {
        return "Analyze this loan applicant data and return ONLY JSON with: risk_score (1-100), risk_factors (array), analysis (string), confidence (0-1).

Loan Details:
Principal: ZMW {$data['loan_details']['principal_amount']}
Duration: {$data['loan_details']['loan_duration']} months
Rate: {$data['loan_details']['interest_rate']}%
Type: {$data['loan_details']['loan_type']}

Borrower:
Income: ZMW {$data['borrower_financials']['monthly_income']}
Employment: {$data['borrower_financials']['employment_stability']} months
Debts: ZMW {$data['borrower_financials']['existing_debts']}
Credit History: {$data['borrower_financials']['credit_history_length']} months
Defaults: {$data['borrower_financials']['previous_defaults']}

Ratios:
Debt-to-Income: " . number_format($data['financial_ratios']['debt_to_income'] * 100, 2) . "%
Loan-to-Income: " . number_format($data['financial_ratios']['loan_to_income'] * 100, 2) . "%";
    }

    private function calculateCreditScore(array $riskAssessment): float
    {
        $baseScore = 100 - $riskAssessment['risk_score'];
        return max(300, min(850, $baseScore * 8.5));
    }

    private function calculateDefaultProbability(array $riskAssessment): float
    {
        $riskScore = $riskAssessment['risk_score'];
        $probability = 1 / (1 + exp(- ($riskScore - 50) / 10));
        return round($probability, 4);
    }

    private function getRecommendation(float $creditScore, float $defaultProbability): string
    {
        if ($creditScore >= 700 && $defaultProbability <= 0.1) {
            return 'APPROVE';
        } elseif ($creditScore >= 600 && $defaultProbability <= 0.2) {
            return 'APPROVE';
        } elseif ($creditScore >= 500 && $defaultProbability <= 0.3) {
            return 'REVIEW';
        } else {
            return 'REJECT';
        }
    }
}
