<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'prediction_type',
        'time_horizon',
        'model_type',
        'predicted_value',
        'confidence_score',
        'features',
        'model_data',
        'actual_value',
        'accuracy',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'predicted_value' => 'decimal:2',
        'confidence_score' => 'decimal:2',
        'features' => 'array',
        'model_data' => 'array',
        'actual_value' => 'decimal:2',
        'accuracy' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeByType($query, $type)
    {
        return $query->where('prediction_type', $type);
    }

    public function scopeByModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>', now()->subDays($days));
    }

    public function scopeHighConfidence($query, $threshold = 80)
    {
        return $query->where('confidence_score', '>=', $threshold);
    }

    public function isRevenuePrediction()
    {
        return $this->prediction_type === 'revenue';
    }

    public function isTrafficPrediction()
    {
        return $this->prediction_type === 'traffic';
    }

    public function isConversionPrediction()
    {
        return $this->prediction_type === 'conversion';
    }

    public function isChurnPrediction()
    {
        return $this->prediction_type === 'churn';
    }

    public function isLinearModel()
    {
        return $this->model_type === 'linear';
    }

    public function isRegressionModel()
    {
        return $this->model_type === 'regression';
    }

    public function isNeuralNetworkModel()
    {
        return $this->model_type === 'neural_network';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function getPredictionLabel()
    {
        return match($this->prediction_type) {
            'revenue' => 'Revenue Forecast',
            'traffic' => 'Traffic Prediction',
            'conversion' => 'Conversion Rate Prediction',
            'churn' => 'Churn Risk Prediction',
            default => 'General Prediction'
        };
    }

    public function getModelLabel()
    {
        return match($this->model_type) {
            'linear' => 'Linear Regression',
            'regression' => 'Multiple Regression',
            'neural_network' => 'Neural Network',
            default => 'Unknown Model'
        };
    }

    public function getTimeHorizonLabel()
    {
        return match($this->time_horizon) {
            '7d' => '7 Days',
            '30d' => '30 Days',
            '90d' => '90 Days',
            '1y' => '1 Year',
            default => $this->time_horizon
        };
    }

    public function getConfidenceLevel()
    {
        if ($this->confidence_score >= 90) return 'very_high';
        if ($this->confidence_score >= 80) return 'high';
        if ($this->confidence_score >= 70) return 'medium';
        if ($this->confidence_score >= 60) return 'low';
        return 'very_low';
    }

    public function getAccuracyLevel()
    {
        if ($this->accuracy >= 90) return 'excellent';
        if ($this->accuracy >= 80) return 'good';
        if ($this->accuracy >= 70) return 'fair';
        if ($this->accuracy >= 60) return 'poor';
        return 'very_poor';
    }

    public function getPredictionError()
    {
        if ($this->actual_value === null) {
            return null;
        }

        $error = abs($this->predicted_value - $this->actual_value);
        $percentageError = $this->actual_value > 0 ? ($error / $this->actual_value) * 100 : 0;

        return [
            'absolute_error' => $error,
            'percentage_error' => $percentageError
        ];
    }

    public function getPerformanceMetrics()
    {
        if ($this->actual_value === null) {
            return null;
        }

        $error = $this->getPredictionError();
        
        return [
            'predicted_value' => $this->predicted_value,
            'actual_value' => $this->actual_value,
            'error' => $error['absolute_error'],
            'percentage_error' => $error['percentage_error'],
            'accuracy' => $this->accuracy,
            'confidence_score' => $this->confidence_score,
            'is_accurate' => $error['percentage_error'] <= 10,
            'performance_rating' => $this->getPerformanceRating()
        ];
    }

    private function getPerformanceRating()
    {
        if ($this->actual_value === null) {
            return 'pending';
        }

        $error = $this->getPredictionError();
        
        if ($error['percentage_error'] <= 5) return 'excellent';
        if ($error['percentage_error'] <= 10) return 'good';
        if ($error['percentage_error'] <= 20) return 'fair';
        if ($error['percentage_error'] <= 30) return 'poor';
        return 'very_poor';
    }

    public function updateActualValue($actualValue)
    {
        $this->actual_value = $actualValue;
        
        if ($this->predicted_value > 0) {
            $error = abs($this->predicted_value - $actualValue);
            $this->accuracy = max(0, 100 - ($error / $this->predicted_value * 100));
        }
        
        $this->status = 'completed';
        $this->save();
    }

    public function getFeatureImportance()
    {
        return $this->features['importance'] ?? [];
    }

    public function getTopFeatures($limit = 5)
    {
        $importance = $this->getFeatureImportance();
        arsort($importance);
        
        return array_slice($importance, 0, $limit, true);
    }

    public function getModelParameters()
    {
        return $this->model_data['parameters'] ?? [];
    }

    public function getTrainingMetrics()
    {
        return $this->model_data['training_metrics'] ?? [];
    }

    public function getValidationMetrics()
    {
        return $this->model_data['validation_metrics'] ?? [];
    }

    public function generateReport()
    {
        return [
            'prediction_info' => [
                'id' => $this->id,
                'type' => $this->prediction_type,
                'type_label' => $this->getPredictionLabel(),
                'time_horizon' => $this->time_horizon,
                'time_horizon_label' => $this->getTimeHorizonLabel(),
                'model_type' => $this->model_type,
                'model_label' => $this->getModelLabel(),
                'status' => $this->status,
                'created_at' => $this->created_at->toDateString()
            ],
            'prediction_data' => [
                'predicted_value' => $this->predicted_value,
                'actual_value' => $this->actual_value,
                'confidence_score' => $this->confidence_score,
                'confidence_level' => $this->getConfidenceLevel(),
                'accuracy' => $this->accuracy,
                'accuracy_level' => $this->getAccuracyLevel()
            ],
            'performance' => $this->getPerformanceMetrics(),
            'features' => $this->features,
            'top_features' => $this->getTopFeatures(),
            'model_data' => $this->model_data,
            'error_analysis' => $this->getPredictionError()
        ];
    }

    public function exportToJson()
    {
        return [
            'id' => $this->id,
            'prediction_type' => $this->prediction_type,
            'time_horizon' => $this->time_horizon,
            'model_type' => $this->model_type,
            'predicted_value' => $this->predicted_value,
            'confidence_score' => $this->confidence_score,
            'features' => $this->features,
            'model_data' => $this->model_data,
            'actual_value' => $this->actual_value,
            'accuracy' => $this->accuracy,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function compareWith($otherPrediction)
    {
        return [
            'this_prediction' => [
                'id' => $this->id,
                'predicted_value' => $this->predicted_value,
                'confidence_score' => $this->confidence_score,
                'accuracy' => $this->accuracy
            ],
            'other_prediction' => [
                'id' => $otherPrediction->id,
                'predicted_value' => $otherPrediction->predicted_value,
                'confidence_score' => $otherPrediction->confidence_score,
                'accuracy' => $otherPrediction->accuracy
            ],
            'comparison' => [
                'value_difference' => $this->predicted_value - $otherPrediction->predicted_value,
                'confidence_difference' => $this->confidence_score - $otherPrediction->confidence_score,
                'accuracy_difference' => ($this->accuracy ?? 0) - ($otherPrediction->accuracy ?? 0),
                'better_performer' => $this->determineBetterPerformer($otherPrediction)
            ]
        ];
    }

    private function determineBetterPerformer($otherPrediction)
    {
        $thisScore = ($this->confidence_score + ($this->accuracy ?? 0)) / 2;
        $otherScore = ($otherPrediction->confidence_score + ($otherPrediction->accuracy ?? 0)) / 2;
        
        if ($thisScore > $otherScore) return 'this';
        if ($otherScore > $thisScore) return 'other';
        return 'equal';
    }

    public function getRecommendations()
    {
        $recommendations = [];
        
        if ($this->confidence_score < 70) {
            $recommendations[] = 'Consider collecting more data to improve prediction confidence';
        }
        
        if ($this->accuracy < 70 && $this->actual_value !== null) {
            $recommendations[] = 'Model accuracy is low - consider retraining with different parameters';
        }
        
        if ($this->model_type === 'linear' && $this->accuracy < 80) {
            $recommendations[] = 'Consider using more complex models like neural networks';
        }
        
        if ($this->getPredictionError()['percentage_error'] > 20) {
            $recommendations[] = 'Prediction error is high - review feature selection and model parameters';
        }
        
        return $recommendations;
    }
}
